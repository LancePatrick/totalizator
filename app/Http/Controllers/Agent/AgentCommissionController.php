<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\CommissionTransaction;
use App\Models\CommissionWithdrawalRequest;
use App\Models\GameBet;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentCommissionController extends Controller
{
    public function index(Request $request)
    {
        $agent = auth()->user();

        $this->syncMissingBetCommissions($agent);

        $agent->refresh();

        $playerIds = User::where('agent_id', $agent->id)
            ->where('role', 'player')
            ->pluck('id');

        $betsQuery = GameBet::whereIn('user_id', $playerIds);

        if ($request->filled('date_from')) {
            $betsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $betsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $totalPlayerBets = (clone $betsQuery)->sum('amount');
        $computedCommission = round($totalPlayerBets * 0.02, 2);

        $transactions = CommissionTransaction::where('agent_id', $agent->id)
            ->latest()
            ->take(50)
            ->get();

        $withdrawals = CommissionWithdrawalRequest::where('agent_id', $agent->id)
            ->latest()
            ->take(30)
            ->get();

        return view('agent.commissions.index', [
            'agent' => $agent,
            'totalPlayerBets' => $totalPlayerBets,
            'computedCommission' => $computedCommission,
            'transactions' => $transactions,
            'withdrawals' => $withdrawals,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
        ]);
    }

    private function syncMissingBetCommissions(User $agent): void
    {
        if ($agent->role !== 'agent') {
            return;
        }

        DB::transaction(function () use ($agent) {
            $agent = User::where('id', $agent->id)
                ->lockForUpdate()
                ->firstOrFail();

            $playerIds = User::where('agent_id', $agent->id)
                ->where('role', 'player')
                ->pluck('id');

            if ($playerIds->isEmpty()) {
                return;
            }

            $bets = GameBet::with('user')
                ->whereIn('user_id', $playerIds)
                ->latest('id')
                ->get();

            foreach ($bets as $bet) {
                $alreadyCredited = CommissionTransaction::where('agent_id', $agent->id)
                    ->where('type', 'player_bet_commission')
                    ->where('reference_type', GameBet::class)
                    ->where('reference_id', $bet->id)
                    ->exists();

                if ($alreadyCredited) {
                    continue;
                }

                $commissionAmount = round((float) $bet->amount * 0.02, 2);

                if ($commissionAmount <= 0) {
                    continue;
                }

                $before = (float) $agent->commission_balance;
                $after = $before + $commissionAmount;

                $agent->update([
                    'commission_balance' => $after,
                ]);

                CommissionTransaction::create([
                    'agent_id' => $agent->id,
                    'type' => 'player_bet_commission',
                    'direction' => 'credit',
                    'amount' => $commissionAmount,
                    'balance_before' => $before,
                    'balance_after' => $after,
                    'reference_type' => GameBet::class,
                    'reference_id' => $bet->id,
                    'description' => '2% commission earned from player bet: ' . ($bet->user?->name ?? 'Player'),
                ]);
            }
        });
    }

    public function convertToLoad(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                $agent = User::where('id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                $amount = (float) $data['amount'];

                if ($agent->role !== 'agent') {
                    throw new \RuntimeException('Only agents can convert commission.');
                }

                if ((float) $agent->commission_balance < $amount) {
                    throw new \RuntimeException('Insufficient commission balance.');
                }

                $commissionBefore = (float) $agent->commission_balance;
                $walletBefore = (float) $agent->wallet_balance;

                $commissionAfter = $commissionBefore - $amount;
                $walletAfter = $walletBefore + $amount;

                $agent->update([
                    'commission_balance' => $commissionAfter,
                    'wallet_balance' => $walletAfter,
                ]);

                CommissionTransaction::create([
                    'agent_id' => $agent->id,
                    'type' => 'convert_to_load',
                    'direction' => 'debit',
                    'amount' => $amount,
                    'balance_before' => $commissionBefore,
                    'balance_after' => $commissionAfter,
                    'description' => 'Commission converted to load wallet.',
                ]);

                WalletTransaction::create([
                    'user_id' => $agent->id,
                    'admin_id' => null,
                    'type' => 'commission_converted_to_load',
                    'direction' => 'credit',
                    'amount' => $amount,
                    'balance_before' => $walletBefore,
                    'balance_after' => $walletAfter,
                    'description' => 'Commission converted to wallet load.',
                ]);
            });

            return back()->with('success', 'Commission converted to load wallet.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'amount' => $e->getMessage(),
            ]);
        }
    }

    public function withdrawCash(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:150'],
            'account_number' => ['required', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                $agent = User::where('id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                $amount = (float) $data['amount'];

                if ($agent->role !== 'agent') {
                    throw new \RuntimeException('Only agents can withdraw commission.');
                }

                if ((float) $agent->commission_balance < $amount) {
                    throw new \RuntimeException('Insufficient commission balance.');
                }

                $before = (float) $agent->commission_balance;
                $after = $before - $amount;

                $agent->update([
                    'commission_balance' => $after,
                ]);

                $withdrawal = CommissionWithdrawalRequest::create([
                    'agent_id' => $agent->id,
                    'admin_id' => null,
                    'amount' => $amount,
                    'payment_method' => $data['payment_method'],
                    'account_name' => $data['account_name'],
                    'account_number' => $data['account_number'],
                    'notes' => $data['notes'] ?? null,
                    'status' => 'pending',
                ]);

                CommissionTransaction::create([
                    'agent_id' => $agent->id,
                    'type' => 'commission_withdrawal_request',
                    'direction' => 'debit',
                    'amount' => $amount,
                    'balance_before' => $before,
                    'balance_after' => $after,
                    'reference_type' => CommissionWithdrawalRequest::class,
                    'reference_id' => $withdrawal->id,
                    'description' => 'Commission withdrawal requested. Amount held while pending admin approval.',
                ]);
            });

            return back()->with('success', 'Commission withdrawal request submitted.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'amount' => $e->getMessage(),
            ]);
        }
    }
}