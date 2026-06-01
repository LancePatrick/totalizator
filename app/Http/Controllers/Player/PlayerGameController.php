<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\CommissionTransaction;
use App\Models\GameBet;
use App\Models\GameRound;
use App\Models\LogrohanEntry;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerGameController extends Controller
{
    public function index()
    {
        $currentGame = GameRound::whereIn('status', [
                'waiting',
                'open',
                'closed',
                'ended',
            ])
            ->latest('id')
            ->first();

        return view('player.game.index', [
            'currentGame' => $currentGame,
            'logrohan' => LogrohanEntry::latest()->take(30)->get(),
            'myBets' => GameBet::with('round')
                ->where('user_id', auth()->id())
                ->latest()
                ->take(15)
                ->get(),
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

            if ($game->status !== 'open') {
                return back()->withErrors([
                    'game' => 'Betting is closed. Please wait for the admin to start a new open game.',
                ]);
            }

            if (!$player->is_active) {
                return back()->withErrors([
                    'account' => 'Your account is inactive.',
                ]);
            }

            if ((float) $player->wallet_balance < $amount) {
                return back()->withErrors([
                    'amount' => 'Insufficient wallet balance.',
                ]);
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

            /*
            |--------------------------------------------------------------------------
            | Agent Commission Logic
            |--------------------------------------------------------------------------
            | Kapag player may assigned agent, automatic kikita agent ng 2%
            | commission based sa bet amount.
            |
            | Example:
            | Player bet: ₱1,000
            | Agent commission: ₱20
            |
            | This goes to commission_balance, not wallet_balance.
            */

            if ($player->role === 'player' && $player->agent_id) {
                $agent = User::where('id', $player->agent_id)
                    ->where('role', 'agent')
                    ->lockForUpdate()
                    ->first();

                if ($agent) {
                    $commissionRate = 0.02;
                    $commissionAmount = round($amount * $commissionRate, 2);

                    if ($commissionAmount > 0) {
                        $commissionBefore = (float) $agent->commission_balance;
                        $commissionAfter = $commissionBefore + $commissionAmount;

                        $agent->update([
                            'commission_balance' => $commissionAfter,
                        ]);

                        CommissionTransaction::create([
                            'agent_id' => $agent->id,
                            'type' => 'player_bet_commission',
                            'direction' => 'credit',
                            'amount' => $commissionAmount,
                            'balance_before' => $commissionBefore,
                            'balance_after' => $commissionAfter,
                            'reference_type' => GameBet::class,
                            'reference_id' => $bet->id,
                            'description' => '2% commission earned from player bet: ' . $player->name,
                        ]);
                    }
                }
            }

            $game->recalculateTotalsAndOdds();

            return back()->with('success', 'Bet placed successfully.');
        });
    }
}