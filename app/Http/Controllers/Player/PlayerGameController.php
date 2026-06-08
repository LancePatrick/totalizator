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

class PlayerGameController extends Controller
{
    public function index(Request $request)
    {
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
            'logrohan' => LogrohanEntry::latest()->take(240)->get(),
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
        $currentGame = null;

        if ($request->filled('game_id')) {
            $currentGame = GameRound::whereKey($request->game_id)
                ->whereIn('status', ['waiting', 'open', 'closed', 'settled'])
                ->first();
        }

        $user = User::whereKey(auth()->id())->first();

        $rooms = $this->availableRooms()->map(function ($game) {
            return [
                'id' => $game->id,
                'title' => $game->title ?? $game->round_name ?? 'Game Room',
                'round_code' => $game->round_code ?? $game->round_number ?? $game->id,
                'status' => $game->status,
                'total_pool' => (float) ($game->total_pool ?? 0),
            ];
        });

        $myBets = GameBet::with('round')
            ->where('user_id', auth()->id())
            ->when($currentGame, function ($query) use ($currentGame) {
                $query->where('game_round_id', $currentGame->id);
            })
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($bet) {
                return [
                    'id' => $bet->id,
                    'side' => strtoupper($bet->side),
                    'side_key' => strtolower($bet->side),
                    'amount' => (float) $bet->amount,
                    'odds' => (float) $bet->odds_at_bet,
                    'status' => strtoupper($bet->status),
                    'status_key' => strtolower($bet->status),
                    'round' => $bet->round?->round_code ?? $bet->game_round_id,
                ];
            });

        $logrohan = LogrohanEntry::latest()->take(240)->get();

        $normalizeSide = function ($entry) {
            $side = strtolower($entry->winning_side ?? $entry->result ?? $entry->side ?? 'cancelled');

            if (in_array($side, ['cancel', 'canceled'])) {
                $side = 'cancelled';
            }

            if (!in_array($side, ['meron', 'wala', 'draw', 'cancelled'])) {
                $side = 'cancelled';
            }

            return $side;
        };

        $meronCount = $logrohan->filter(fn ($entry) => $normalizeSide($entry) === 'meron')->count();
        $walaCount = $logrohan->filter(fn ($entry) => $normalizeSide($entry) === 'wala')->count();
        $drawCount = $logrohan->filter(fn ($entry) => $normalizeSide($entry) === 'draw')->count();
        $cancelledCount = $logrohan->filter(fn ($entry) => $normalizeSide($entry) === 'cancelled')->count();

        if (!$currentGame) {
            return response()->json([
                'has_game' => false,
                'wallet_balance' => (float) ($user->wallet_balance ?? 0),
                'rooms' => $rooms,
                'my_bets' => $myBets,
                'road_counts' => [
                    'meron' => $meronCount,
                    'wala' => $walaCount,
                    'draw' => $drawCount,
                    'cancelled' => $cancelledCount,
                ],
            ]);
        }

        return response()->json([
            'has_game' => true,
            'wallet_balance' => (float) ($user->wallet_balance ?? 0),
            'rooms' => $rooms,

            'game' => [
                'id' => $currentGame->id,
                'title' => $currentGame->title ?? $currentGame->round_name ?? 'Game Room',
                'round_code' => $currentGame->round_code ?? $currentGame->round_number ?? $currentGame->id,
                'status' => $currentGame->status,

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

            'road_counts' => [
                'meron' => $meronCount,
                'wala' => $walaCount,
                'draw' => $drawCount,
                'cancelled' => $cancelledCount,
            ],
        ]);
    }

    public function bet(Request $request)
    {
        $data = $request->validate([
            'game_round_id' => ['required', 'exists:game_rounds,id'],
            'side' => ['required', 'in:meron,wala,draw'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $amount = (float) $data['amount'];

        return DB::transaction(function () use ($data, $amount) {
            $player = User::whereKey(auth()->id())
                ->lockForUpdate()
                ->firstOrFail();

            $game = GameRound::whereKey($data['game_round_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($game->status, ['ended', 'settled'])) {
                return redirect()
                    ->route('player.game.index', ['game_id' => $game->id])
                    ->withErrors(['game' => 'This game room is already declared. Please choose another open room.']);
            }

            if ($game->status !== 'open') {
                return redirect()
                    ->route('player.game.index', ['game_id' => $game->id])
                    ->withErrors(['game' => 'Betting is closed. Please choose an open room.']);
            }

            if (!$player->is_active) {
                return redirect()
                    ->route('player.game.index', ['game_id' => $game->id])
                    ->withErrors(['account' => 'Your account is inactive.']);
            }

            if ((float) $player->wallet_balance < $amount) {
                return redirect()
                    ->route('player.game.index', ['game_id' => $game->id])
                    ->withErrors(['amount' => 'Insufficient wallet balance.']);
            }

            $oddsAtBet = match ($data['side']) {
                'meron' => (float) $game->meron_odds,
                'wala' => (float) $game->wala_odds,
                'draw' => (float) $game->draw_odds,
            };

            $balanceBefore = (float) $player->wallet_balance;
            $balanceAfter = $balanceBefore - $amount;

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

            WalletTransaction::create([
                'user_id' => $player->id,
                'admin_id' => null,
                'type' => 'bet',
                'direction' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => GameBet::class,
                'reference_id' => $bet->id,
                'description' => 'Bet placed on ' . strtoupper($data['side']),
            ]);

            $game->recalculateTotalsAndOdds();

            return redirect()
                ->route('player.game.index', ['game_id' => $game->id])
                ->with('success', 'Bet placed successfully.');
        });
    }

    private function availableRooms()
    {
        return GameRound::whereIn('status', ['waiting', 'open', 'closed', 'settled'])
            ->latest('id')
            ->take(50)
            ->get();
    }
}