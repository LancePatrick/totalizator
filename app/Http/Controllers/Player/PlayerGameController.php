<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\GameBet;
use App\Models\GameRound;
use App\Models\LogrohanEntry;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlayerGameController extends Controller
{
    public function index(Request $request)
    {
        if ($redirect = $this->blockIfKycNotApproved()) {
            return $redirect;
        }

        $gameRooms = $this->availableRooms();
        $currentGame = null;

        if ($request->filled('game_id')) {
            $currentGame = GameRound::whereKey($request->game_id)
                ->whereIn('status', ['waiting', 'open', 'closed', 'settled'])
                ->first();
        }

        if (!$currentGame && $request->filled('game_id')) {
            return redirect()
                ->route('player.game.index')
                ->withErrors(['game' => 'This game room is ended or not available anymore.']);
        }

        return view('player.game.index', [
            'currentGame' => $currentGame,
            'gameRooms' => $gameRooms,
            'selectedGameId' => $currentGame?->id,
            'logrohan' => $this->roomRoadEntries($currentGame?->id),
            'myBets' => GameBet::with('round')
                ->where('user_id', auth()->id())
                ->when($currentGame, function ($query) use ($currentGame) {
                    $query->where('game_round_id', $currentGame->id);
                })
                ->latest()
                ->take(15)
                ->get(),
        ]);
    }

    public function liveData(Request $request)
    {
        if ($redirect = $this->blockIfKycNotApproved()) {
            return response()->json([
                'redirect' => route('player.kyc.index'),
                'message' => 'KYC verification required.',
            ], 403);
        }

        $currentGame = null;

        if ($request->filled('game_id')) {
            $currentGame = GameRound::whereKey($request->game_id)
                ->whereIn('status', ['waiting', 'open', 'closed', 'settled'])
                ->first();
        }

        $selectedGameId = $currentGame?->id;
        $user = User::whereKey(auth()->id())->first();

        $rooms = $this->availableRooms()->map(function ($game) {
            return [
                'id' => $game->id,
                'title' => $game->title ?? $game->round_name ?? 'Game Room',
                'round_code' => $game->round_code ?? $game->round_number ?? $game->id,
                'status' => $game->status,
                'winning_side' => $game->winning_side,
                'total_pool' => (float) ($game->total_pool ?? 0),
            ];
        });

        $myBets = $this->formattedMyBets($selectedGameId);
        $road = $this->formattedRoad($selectedGameId);

        $roadCounts = [
            'meron' => $road->where('side', 'meron')->count(),
            'wala' => $road->where('side', 'wala')->count(),
            'draw' => $road->where('side', 'draw')->count(),
            'cancelled' => $road->where('side', 'cancelled')->count(),
        ];

        if (!$currentGame) {
            return response()->json([
                'has_game' => false,
                'wallet_balance' => (float) ($user->wallet_balance ?? 0),
                'rooms' => $rooms,
                'my_bets' => $myBets,
                'road' => $road,
                'road_counts' => $roadCounts,
            ]);
        }

        $currentGame->refresh();

        return response()->json([
            'has_game' => true,
            'wallet_balance' => (float) ($user->wallet_balance ?? 0),
            'rooms' => $rooms,
            'game' => [
                'id' => $currentGame->id,
                'title' => $currentGame->title ?? $currentGame->round_name ?? 'Game Room',
                'round_code' => $currentGame->round_code ?? $currentGame->round_number ?? $currentGame->id,
                'status' => $currentGame->status,
                'winning_side' => $currentGame->winning_side,

                'meron_total' => (float) ($currentGame->meron_total ?? 0),
                'wala_total' => (float) ($currentGame->wala_total ?? 0),
                'draw_total' => (float) ($currentGame->draw_total ?? 0),
                'total_pool' => (float) ($currentGame->total_pool ?? 0),
                'net_pool' => (float) ($currentGame->net_pool ?? 0),

                'meron_odds' => (float) ($currentGame->meron_odds ?? 0),
                'wala_odds' => (float) ($currentGame->wala_odds ?? 0),
                'draw_odds' => (float) ($currentGame->draw_odds ?? 0),
            ],
            'my_bets' => $myBets,
            'road' => $road,
            'road_counts' => $roadCounts,
        ]);
    }

    public function bet(Request $request)
    {
        if ($redirect = $this->blockIfKycNotApproved()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Please complete your KYC verification before placing bets.',
                ], 403);
            }

            return $redirect;
        }

        $data = $request->validate([
            'game_round_id' => ['required', 'exists:game_rounds,id'],
            'side' => ['required', 'in:meron,wala,draw'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $amount = (float) $data['amount'];

        return DB::transaction(function () use ($request, $data, $amount) {
            $player = User::whereKey(auth()->id())
                ->lockForUpdate()
                ->firstOrFail();

            if ($player->kyc_status !== 'approved') {
                return $this->betResponse(
                    $request,
                    false,
                    'You need to complete KYC verification before placing bets.',
                    null,
                    403
                );
            }

            if (!$player->is_active) {
                return $this->betResponse(
                    $request,
                    false,
                    'Your account is inactive. Please submit an appeal.',
                    null,
                    403
                );
            }

            $game = GameRound::whereKey($data['game_round_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($game->status === 'ended') {
                return $this->betResponse(
                    $request,
                    false,
                    'This game room is already ended. Please choose another room.',
                    null,
                    422
                );
            }

            if ($game->status !== 'open') {
                return $this->betResponse(
                    $request,
                    false,
                    'Betting is closed. Please choose an open room.',
                    $game->id,
                    422
                );
            }

            if (filled($game->winning_side)) {
                return $this->betResponse(
                    $request,
                    false,
                    'This round is already declared. Please wait for the next round.',
                    $game->id,
                    422
                );
            }

            if ((float) ($player->wallet_balance ?? 0) < $amount) {
                return $this->betResponse(
                    $request,
                    false,
                    'Insufficient wallet balance.',
                    $game->id,
                    422
                );
            }

            $balanceBefore = (float) ($player->wallet_balance ?? 0);
            $balanceAfter = round($balanceBefore - $amount, 2);

            $oddsAtBet = $game->oddsForSide($data['side']);

            $player->update([
                'wallet_balance' => $balanceAfter,
            ]);

            $bet = GameBet::create([
                'game_round_id' => $game->id,
                'user_id' => $player->id,
                'side' => $data['side'],
                'amount' => $amount,
                'odds_at_bet' => $oddsAtBet,
                'status' => 'pending',
                'payout_amount' => 0,
            ]);

            if (Schema::hasTable('wallet_transactions')) {
                WalletTransaction::create($this->onlyExistingColumns('wallet_transactions', [
                    'user_id' => $player->id,
                    'type' => 'bet',
                    'direction' => 'debit',
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => GameBet::class,
                    'reference_id' => $bet->id,
                    'description' => 'Player bet on ' . strtoupper($data['side']) . '.',
                ]));
            }

            $game->recalculateTotalsAndOdds();

            return $this->betResponse(
                $request,
                true,
                'Bet placed successfully.',
                $game->id
            );
        });
    }

    private function betResponse(Request $request, bool $ok, string $message, ?int $gameId = null, int $status = 200)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => $ok,
                'message' => $message,
                'game_id' => $gameId,
            ], $status);
        }

        if ($ok) {
            return redirect()
                ->route('player.game.index', ['game_id' => $gameId])
                ->with('success', $message);
        }

        return redirect()
            ->route('player.game.index', $gameId ? ['game_id' => $gameId] : [])
            ->withErrors(['game' => $message]);
    }

    private function availableRooms()
    {
        return GameRound::whereIn('status', ['waiting', 'open', 'closed', 'settled'])
            ->latest('id')
            ->take(50)
            ->get();
    }

    private function roomRoadEntries($gameRoundId = null)
    {
        if (!Schema::hasTable('logrohan_entries')) {
            return collect();
        }

        return LogrohanEntry::query()
            ->when($gameRoundId, function ($query) use ($gameRoundId) {
                $query->where('game_round_id', $gameRoundId);
            })
            ->latest()
            ->take(120)
            ->get()
            ->reverse()
            ->values();
    }

    private function formattedRoad($gameRoundId = null)
    {
        return $this->roomRoadEntries($gameRoundId)
            ->map(function ($entry) {
                $side = $this->normalizeSide($entry->winning_side ?? $entry->result ?? $entry->side ?? 'cancelled');

                return [
                    'id' => $entry->id,
                    'side' => $side,
                    'label' => match ($side) {
                        'meron' => 'M',
                        'wala' => 'W',
                        'draw' => 'D',
                        default => 'C',
                    },
                    'round' => $entry->round_number
                        ?? $entry->round_code
                        ?? $entry->game_round_id
                        ?? $entry->id,
                    'created_at' => optional($entry->created_at)->format('M d, h:i A'),
                ];
            });
    }

    private function formattedMyBets($gameRoundId = null)
    {
        return GameBet::with('round')
            ->where('user_id', auth()->id())
            ->when($gameRoundId, function ($query) use ($gameRoundId) {
                $query->where('game_round_id', $gameRoundId);
            })
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($bet) {
                $status = strtolower($bet->status ?? 'pending');
                $payoutAmount = (float) ($bet->payout_amount ?? 0);
                $payoutLabel = 'Possible Payout';

                if ($status === 'refunded') {
                    $payoutLabel = 'Refunded';
                    $payoutAmount = (float) $bet->amount;
                } elseif (in_array($status, ['won', 'paid'])) {
                    $payoutLabel = 'Payout';
                } elseif ($status === 'lost') {
                    $payoutLabel = 'Lost';
                    $payoutAmount = 0;
                } else {
                    $payoutAmount = round((float) $bet->amount * (float) $bet->odds_at_bet, 2);
                }

                return [
                    'id' => $bet->id,
                    'side' => strtoupper($bet->side),
                    'side_key' => strtolower($bet->side),
                    'amount' => (float) $bet->amount,
                    'odds' => (float) $bet->odds_at_bet,
                    'status' => strtoupper($bet->status),
                    'status_key' => strtolower($bet->status),
                    'payout_amount' => $payoutAmount,
                    'payout_label' => $payoutLabel,
                    'round' => $bet->round?->round_code
                        ?? $bet->round?->round_number
                        ?? $bet->game_round_id,
                ];
            });
    }

    private function normalizeSide($side): string
    {
        $side = strtolower((string) $side);

        if (in_array($side, ['m', 'meron'])) {
            return 'meron';
        }

        if (in_array($side, ['w', 'wala'])) {
            return 'wala';
        }

        if (in_array($side, ['d', 'draw'])) {
            return 'draw';
        }

        return 'cancelled';
    }

    private function onlyExistingColumns(string $table, array $payload): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        return collect($payload)
            ->filter(fn ($value, $column) => Schema::hasColumn($table, $column))
            ->all();
    }

    private function blockIfKycNotApproved()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'player') {
            return redirect()->route('dashboard');
        }

        if (!$user->is_active) {
            return redirect()
                ->route('player.account.inactive')
                ->withErrors(['account' => 'Your account is inactive. Please submit an appeal.']);
        }

        if ($user->kyc_status !== 'approved') {
            return redirect()
                ->route('player.kyc.index')
                ->withErrors(['kyc' => 'Please complete your KYC verification before entering the game.']);
        }

        return null;
    }
}