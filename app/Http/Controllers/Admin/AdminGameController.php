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

class AdminGameController extends Controller
{
    public function index()
    {
        return view('admin.games.index', [
            'currentGame' => GameRound::latest()->first(),
            'games' => GameRound::latest()->paginate(10),
            'logrohan' => LogrohanEntry::latest()->take(30)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'round_name' => ['required', 'string', 'max:255'],
            'round_number' => ['nullable', 'string', 'max:50'],
            'video_url' => ['nullable', 'string'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:50'],
        ]);

        GameRound::create([
            'created_by' => auth()->id(),
            'round_name' => $data['round_name'],
            'round_number' => $data['round_number'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'commission_rate' => $data['commission_rate'],
            'status' => 'waiting',
        ]);

        return back()->with('success', 'Game round created.');
    }

    public function start(GameRound $game)
    {
        $game->update([
            'status' => 'open',
            'started_at' => now(),
        ]);

        return back()->with('success', 'Game started. Betting is now open.');
    }

    public function close(GameRound $game)
    {
        $game->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Betting closed.');
    }

    public function end(GameRound $game)
    {
        $game->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return back()->with('success', 'Game ended. You can now declare result.');
    }

    public function declare(Request $request, GameRound $game)
    {
        $data = $request->validate([
            'winning_side' => ['required', 'in:meron,wala,draw,cancelled'],
        ]);

        DB::transaction(function () use ($game, $data) {
            $winningSide = $data['winning_side'];

            $game = GameRound::whereKey($game->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($game->status === 'settled') {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Recalculate totals and final odds before payout
            |--------------------------------------------------------------------------
            */
            if (method_exists($game, 'recalculateTotalsAndOdds')) {
                $game->recalculateTotalsAndOdds();
                $game->refresh();
            }

            /*
            |--------------------------------------------------------------------------
            | Get final totalizator odds
            |--------------------------------------------------------------------------
            */
            $finalOdds = match ($winningSide) {
                'meron' => (float) ($game->meron_odds ?? 0),
                'wala' => (float) ($game->wala_odds ?? 0),
                'draw' => (float) ($game->draw_odds ?? 0),
                default => 0,
            };

            /*
            |--------------------------------------------------------------------------
            | Safety fallback: if odds are 0, return at least original bet for winners
            |--------------------------------------------------------------------------
            */
            if ($winningSide !== 'cancelled' && $finalOdds <= 0) {
                $finalOdds = 1;
            }

            $bets = GameBet::where('game_round_id', $game->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            foreach ($bets as $bet) {
                $player = User::whereKey($bet->user_id)
                    ->lockForUpdate()
                    ->first();

                if (!$player) {
                    continue;
                }

                $balanceBefore = (float) $player->wallet_balance;

                /*
                |--------------------------------------------------------------------------
                | Cancelled: refund all pending bets
                |--------------------------------------------------------------------------
                */
                if ($winningSide === 'cancelled') {
                    $refundAmount = round((float) $bet->amount, 2);
                    $balanceAfter = $balanceBefore + $refundAmount;

                    $player->update([
                        'wallet_balance' => $balanceAfter,
                    ]);

                    $bet->update([
                        'status' => 'refunded',
                        'payout_amount' => $refundAmount,
                    ]);

                    WalletTransaction::create([
                        'user_id' => $player->id,
                        'admin_id' => auth()->id(),
                        'type' => 'refund',
                        'direction' => 'credit',
                        'amount' => $refundAmount,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'reference_type' => GameBet::class,
                        'reference_id' => $bet->id,
                        'description' => 'Refund for cancelled game round #' . $game->id,
                    ]);

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Winner: payout using FINAL TOTALIZATOR ODDS
                |--------------------------------------------------------------------------
                */
                if ($bet->side === $winningSide) {
                    $payoutAmount = round((float) $bet->amount * $finalOdds, 2);
                    $balanceAfter = $balanceBefore + $payoutAmount;

                    $player->update([
                        'wallet_balance' => $balanceAfter,
                    ]);

                    $bet->update([
                        'status' => 'won',
                        'odds_at_bet' => $finalOdds,
                        'payout_amount' => $payoutAmount,
                    ]);

                    WalletTransaction::create([
                        'user_id' => $player->id,
                        'admin_id' => auth()->id(),
                        'type' => 'payout',
                        'direction' => 'credit',
                        'amount' => $payoutAmount,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'reference_type' => GameBet::class,
                        'reference_id' => $bet->id,
                        'description' => 'Winning payout for ' . strtoupper($winningSide) . ' round #' . $game->id,
                    ]);

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Loser
                |--------------------------------------------------------------------------
                */
                $bet->update([
                    'status' => 'lost',
                    'payout_amount' => 0,
                ]);
            }

            $game->update([
                'status' => 'settled',
                'winning_side' => $winningSide,
                'settled_at' => now(),
                'ended_at' => $game->ended_at ?? now(),
            ]);

            LogrohanEntry::create([
                'game_round_id' => $game->id,
                'round_number' => $game->round_number,
                'result' => $winningSide,
            ]);
        });

        return back()->with('success', 'Result declared, payouts processed, and saved to logrohan.');
    }
}