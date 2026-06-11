<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameBet;
use App\Models\GameRound;
use App\Models\LogrohanEntry;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AdminGameController extends Controller
{
    public function index(Request $request)
    {
        $gameList = $this->visibleAdminRooms();

        $currentGame = null;

        if ($request->filled('game_id')) {
            $currentGame = GameRound::whereKey($request->game_id)
                ->whereIn('status', ['waiting', 'open', 'closed', 'settled'])
                ->first();
        }

        if (!$currentGame && $request->filled('game_id')) {
            return redirect()
                ->route('admin.games.index')
                ->withErrors(['game' => 'Selected game room was not found or already ended.']);
        }

        return view('admin.games.index', [
            'currentGame' => $currentGame,
            'gameList' => $gameList,
            'logrohan' => LogrohanEntry::latest()->take(50)->get(),
        ]);
    }

    public function liveData(Request $request)
    {
        $currentGame = null;

        if ($request->filled('game_id')) {
            $currentGame = GameRound::whereKey($request->game_id)
                ->whereIn('status', ['waiting', 'open', 'closed', 'settled'])
                ->first();
        }

        $gameList = $this->visibleAdminRooms()
            ->map(function ($game) {
                return [
                    'id' => $game->id,
                    'title' => $game->title ?? $game->round_name ?? 'Game Room',
                    'round_code' => $game->round_code ?? $game->round_number ?? $game->id,
                    'status' => $game->status,
                    'winning_side' => $game->winning_side,
                    'total_pool' => (float) ($game->total_pool ?? 0),
                    'created_at' => optional($game->created_at)->format('M d, Y h:i A'),
                ];
            });

        $logrohan = LogrohanEntry::latest()
            ->take(50)
            ->get()
            ->map(function ($entry) {
                $result = strtolower($entry->result ?? 'cancelled');

                if (in_array($result, ['cancel', 'canceled'])) {
                    $result = 'cancelled';
                }

                if (!in_array($result, ['meron', 'wala', 'draw', 'cancelled'])) {
                    $result = 'cancelled';
                }

                return [
                    'id' => $entry->id,
                    'round_number' => $entry->round_number ?? 'Room',
                    'result' => strtoupper($result),
                    'result_key' => $result,
                    'created_at' => optional($entry->created_at)->format('M d, Y h:i A'),
                ];
            });

        if (!$currentGame) {
            return response()->json([
                'has_game' => false,
                'games' => $gameList,
                'logrohan' => $logrohan,
            ]);
        }

        return response()->json([
            'has_game' => true,
            'game' => [
                'id' => $currentGame->id,
                'title' => $currentGame->title ?? $currentGame->round_name ?? 'Game Room',
                'round_code' => $currentGame->round_code ?? $currentGame->round_number ?? $currentGame->id,
                'status' => $currentGame->status,
                'winning_side' => $currentGame->winning_side,
                'video_url' => $currentGame->video_url,

                'meron_total' => (float) ($currentGame->meron_total ?? 0),
                'wala_total' => (float) ($currentGame->wala_total ?? 0),
                'draw_total' => (float) ($currentGame->draw_total ?? 0),
                'total_pool' => (float) ($currentGame->total_pool ?? 0),
                'net_pool' => (float) ($currentGame->net_pool ?? 0),

                'meron_odds' => (float) ($currentGame->meron_odds ?? 0),
                'wala_odds' => (float) ($currentGame->wala_odds ?? 0),
                'draw_odds' => (float) ($currentGame->draw_odds ?? 0),
            ],
            'games' => $gameList,
            'logrohan' => $logrohan,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'game_title' => ['required', 'string', 'max:255'],
            'round_number' => ['nullable', 'string', 'max:100'],
            'video_url' => ['nullable', 'string', 'max:2000'],
        ]);

        $payload = [
            'created_by' => auth()->id(),

            'title' => $data['game_title'],
            'round_name' => $data['game_title'],

            'round_code' => $data['round_number'] ?? null,
            'round_number' => $data['round_number'] ?? null,

            'video_url' => $data['video_url'] ?? null,

            'status' => 'waiting',
            'winning_side' => null,

            'commission_rate' => 0.05,
            'commission_amount' => 0,
            'admin_income' => 0,

            'company_commission_rate' => 0.03,
            'agent_commission_rate' => 0.02,
            'company_commission_amount' => 0,
            'agent_commission_amount' => 0,

            'meron_total' => 0,
            'wala_total' => 0,
            'draw_total' => 0,
            'total_pool' => 0,
            'net_pool' => 0,

            'meron_odds' => 0,
            'wala_odds' => 0,
            'draw_odds' => 0,

            'payout_total' => 0,

            'opened_at' => null,
            'started_at' => null,
            'closed_at' => null,
            'declared_at' => null,
            'ended_at' => null,
            'settled_at' => null,
        ];

        $game = GameRound::create($this->onlyExistingColumns('game_rounds', $payload));

        if (empty($data['round_number'])) {
            $autoCode = 'GAME-' . str_pad((string) $game->id, 4, '0', STR_PAD_LEFT);

            $game->update($this->onlyExistingColumns('game_rounds', [
                'round_code' => $autoCode,
                'round_number' => $autoCode,
            ]));
        }

        return redirect()
            ->route('admin.games.index', ['game_id' => $game->id])
            ->with('success', 'Game room created. You are now inside this room.');
    }

    public function start(GameRound $game)
    {
        if ($game->status === 'ended') {
            return redirect()
                ->route('admin.games.index')
                ->withErrors(['game' => 'This game room already ended.']);
        }

        if ($game->status === 'open' && blank($game->winning_side)) {
            return redirect()
                ->route('admin.games.index', ['game_id' => $game->id])
                ->withErrors(['game' => 'This game room is already open.']);
        }

        $this->resetRoomForNewRound($game);

        $game->update($this->onlyExistingColumns('game_rounds', [
            'status' => 'open',
            'winning_side' => null,
            'opened_at' => now(),
            'started_at' => now(),
            'closed_at' => null,
            'declared_at' => null,
            'settled_at' => null,
        ]));

        return redirect()
            ->route('admin.games.index', ['game_id' => $game->id])
            ->with('success', 'New round started. Betting is now open again.');
    }

    public function close(GameRound $game)
    {
        if ($game->status !== 'open') {
            return redirect()
                ->route('admin.games.index', ['game_id' => $game->id])
                ->withErrors(['game' => 'Only open game rooms can be closed.']);
        }

        if (filled($game->winning_side)) {
            return redirect()
                ->route('admin.games.index', ['game_id' => $game->id])
                ->withErrors(['game' => 'This round is already declared. Click Start This Room to begin a new round.']);
        }

        $game->update($this->onlyExistingColumns('game_rounds', [
            'status' => 'closed',
            'closed_at' => now(),
        ]));

        return redirect()
            ->route('admin.games.index', ['game_id' => $game->id])
            ->with('success', 'Betting has been closed for this room.');
    }

    public function end(GameRound $game)
    {
        if (!in_array($game->status, ['waiting', 'open', 'closed', 'settled'])) {
            return redirect()
                ->route('admin.games.index', ['game_id' => $game->id])
                ->withErrors(['game' => 'This game room cannot be ended.']);
        }

        $game->update($this->onlyExistingColumns('game_rounds', [
            'status' => 'ended',
            'ended_at' => now(),
        ]));

        return redirect()
            ->route('admin.games.index')
            ->with('success', 'Game room ended and hidden from active list.');
    }

    public function declare(Request $request, GameRound $game)
    {
        $data = $request->validate([
            'winning_side' => ['required', 'in:meron,wala,draw,cancelled'],
        ]);

        $winningSide = $data['winning_side'];

        if ($game->status === 'ended') {
            return redirect()
                ->route('admin.games.index')
                ->withErrors(['game' => 'This game room already ended.']);
        }

        if (!in_array($game->status, ['open', 'closed', 'settled'])) {
            return redirect()
                ->route('admin.games.index', ['game_id' => $game->id])
                ->withErrors(['game' => 'Only open or closed game rooms can be declared.']);
        }

        if (filled($game->winning_side)) {
            return redirect()
                ->route('admin.games.index', ['game_id' => $game->id])
                ->withErrors(['game' => 'This round is already declared. Click Start This Room to begin a new round.']);
        }

        DB::transaction(function () use ($game, $winningSide) {
            $game = GameRound::whereKey($game->id)
                ->lockForUpdate()
                ->firstOrFail();

            $pendingBets = GameBet::with('user')
                ->where('game_round_id', $game->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            $totalPool = (float) $pendingBets->sum('amount');

            if ($winningSide === 'cancelled') {
                $this->refundCancelledRound($game, $pendingBets, $totalPool);
                $this->createLogrohanEntry($game, 'cancelled');

                return;
            }

            $winnerBets = $pendingBets->where('side', $winningSide);
            $loserBets = $pendingBets->where('side', '!=', $winningSide);

            $winnerTotal = (float) $winnerBets->sum('amount');

            $commissionRate = 0.05;
            $companyRate = 0.03;
            $agentRate = 0.02;

            $commissionAmount = round($totalPool * $commissionRate, 2);
            $companyCommissionAmount = round($totalPool * $companyRate, 2);
            $agentCommissionAmount = round($totalPool * $agentRate, 2);
            $netPool = max(0, $totalPool - $commissionAmount);

            $payoutTotal = 0;

            foreach ($winnerBets as $bet) {
                $player = $bet->user;

                $payout = $winnerTotal > 0
                    ? round(((float) $bet->amount / $winnerTotal) * $netPool, 2)
                    : 0;

                $payoutTotal += $payout;

                if ($player && $payout > 0) {
                    $this->creditWallet(
                        user: $player,
                        amount: $payout,
                        type: 'payout',
                        description: 'Winning payout from game result.',
                        referenceType: GameBet::class,
                        referenceId: $bet->id
                    );
                }

                $bet->update([
                    'status' => 'won',
                    'payout_amount' => $payout,
                ]);
            }

            foreach ($loserBets as $bet) {
                $bet->update([
                    'status' => 'lost',
                    'payout_amount' => 0,
                ]);
            }

            $game->update($this->onlyExistingColumns('game_rounds', [
                'winning_side' => $winningSide,
                'status' => 'open',

                'total_pool' => $totalPool,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'company_commission_amount' => $companyCommissionAmount,
                'agent_commission_amount' => $agentCommissionAmount,
                'net_pool' => $netPool,
                'payout_total' => $payoutTotal,
                'admin_income' => $commissionAmount,

                'declared_at' => now(),
                'settled_at' => now(),
            ]));

            $this->createLogrohanEntry($game, $winningSide);
        });

        return redirect()
            ->route('admin.games.index', ['game_id' => $game->id])
            ->with('success', 'Game result declared successfully.');
    }

    private function refundCancelledRound(GameRound $game, $pendingBets, float $totalPool): void
    {
        foreach ($pendingBets as $bet) {
            $player = $bet->user;

            if ($player) {
                $this->creditWallet(
                    user: $player,
                    amount: (float) $bet->amount,
                    type: 'refund',
                    description: 'Bet refunded because game was cancelled.',
                    referenceType: GameBet::class,
                    referenceId: $bet->id
                );
            }

            $bet->update([
                'status' => 'refunded',
                'payout_amount' => $bet->amount,
            ]);
        }

        $game->update($this->onlyExistingColumns('game_rounds', [
            'winning_side' => 'cancelled',
            'status' => 'open',

            'total_pool' => $totalPool,
            'commission_rate' => 0.05,
            'commission_amount' => 0,
            'company_commission_amount' => 0,
            'agent_commission_amount' => 0,
            'net_pool' => $totalPool,
            'payout_total' => $totalPool,
            'admin_income' => 0,

            'declared_at' => now(),
            'settled_at' => now(),
        ]));
    }

    private function creditWallet(
        User $user,
        float $amount,
        string $type,
        string $description,
        string $referenceType,
        int $referenceId
    ): void {
        if ($amount <= 0) {
            return;
        }

        $lockedUser = User::whereKey($user->id)
            ->lockForUpdate()
            ->first();

        if (!$lockedUser) {
            return;
        }

        $balanceBefore = (float) ($lockedUser->wallet_balance ?? 0);
        $balanceAfter = $balanceBefore + $amount;

        $lockedUser->update([
            'wallet_balance' => $balanceAfter,
        ]);

        if (class_exists(WalletTransaction::class) && Schema::hasTable('wallet_transactions')) {
            WalletTransaction::create($this->onlyExistingColumns('wallet_transactions', [
                'user_id' => $lockedUser->id,
                'admin_id' => auth()->id(),
                'type' => $type,
                'direction' => 'credit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
            ]));
        }
    }

    private function resetRoomForNewRound(GameRound $game): void
    {
        $game->update($this->onlyExistingColumns('game_rounds', [
            'winning_side' => null,

            'meron_total' => 0,
            'wala_total' => 0,
            'draw_total' => 0,
            'total_pool' => 0,
            'commission_amount' => 0,
            'company_commission_amount' => 0,
            'agent_commission_amount' => 0,
            'net_pool' => 0,
            'payout_total' => 0,
            'admin_income' => 0,

            'meron_odds' => 0,
            'wala_odds' => 0,
            'draw_odds' => 0,
        ]));
    }

    private function visibleAdminRooms()
    {
        return GameRound::whereIn('status', ['waiting', 'open', 'closed', 'settled'])
            ->latest('id')
            ->take(50)
            ->get();
    }

    private function createLogrohanEntry(GameRound $game, string $result): void
    {
        if (!Schema::hasTable('logrohan_entries')) {
            return;
        }

        $result = strtolower($result);

        if (in_array($result, ['cancel', 'canceled'])) {
            $result = 'cancelled';
        }

        if (!in_array($result, ['meron', 'wala', 'draw', 'cancelled'])) {
            $result = 'cancelled';
        }

        try {
            LogrohanEntry::create($this->onlyExistingColumns('logrohan_entries', [
                'game_round_id' => $game->id,
                'round_number' => $game->round_number ?? $game->round_code ?? $game->id,
                'result' => $result,
            ]));
        } catch (Throwable $e) {
            // If old enum does not support cancelled yet, do not crash declaration.
        }
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
}