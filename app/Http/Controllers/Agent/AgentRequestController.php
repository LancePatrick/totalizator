<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\MoneyRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;

class AgentRequestController extends Controller
{
    public function index()
    {
        $agent = auth()->user();

        return view('agent.requests.index', [
            'moneyRequests' => MoneyRequest::with('user')
                ->where(function ($query) use ($agent) {
                    $query->where('agent_id', $agent->id)
                        ->orWhereHas('user', function ($userQuery) use ($agent) {
                            $userQuery->where('agent_id', $agent->id);
                        });
                })
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->latest()
                ->take(50)
                ->get(),

            'withdrawals' => WithdrawalRequest::with('user')
                ->where(function ($query) use ($agent) {
                    $query->where('agent_id', $agent->id)
                        ->orWhereHas('user', function ($userQuery) use ($agent) {
                            $userQuery->where('agent_id', $agent->id);
                        });
                })
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->latest()
                ->take(50)
                ->get(),
        ]);
    }

    public function approveMoney(MoneyRequest $moneyRequest)
    {
        try {
            $agent = auth()->user();

            DB::transaction(function () use ($moneyRequest, $agent) {
                $moneyRequest = MoneyRequest::where('id', $moneyRequest->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($moneyRequest->status !== 'pending') {
                    throw new \RuntimeException('This money request is already reviewed.');
                }

                $player = User::where('id', $moneyRequest->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $agent = User::where('id', $agent->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($player->role !== 'player') {
                    throw new \RuntimeException('Only player money requests can be approved here.');
                }

                if ((int) $player->agent_id !== (int) $agent->id) {
                    throw new \RuntimeException('This player is not assigned to you.');
                }

                $amount = (float) $moneyRequest->amount;

                if ((float) $agent->wallet_balance < $amount) {
                    throw new \RuntimeException('Insufficient agent wallet balance.');
                }

                $agentBefore = (float) $agent->wallet_balance;
                $playerBefore = (float) $player->wallet_balance;

                $agentAfter = $agentBefore - $amount;
                $playerAfter = $playerBefore + $amount;

                $agent->update([
                    'wallet_balance' => $agentAfter,
                ]);

                $player->update([
                    'wallet_balance' => $playerAfter,
                ]);

                $moneyRequest->update([
                    'agent_id' => $agent->id,
                    'admin_id' => null,
                    'status' => 'approved',
                    'reviewed_at' => now(),
                    'reviewed_by' => $agent->id,
                ]);

                WalletTransaction::create([
                    'user_id' => $agent->id,
                    'admin_id' => null,
                    'type' => 'player_load_approved',
                    'direction' => 'debit',
                    'amount' => $amount,
                    'balance_before' => $agentBefore,
                    'balance_after' => $agentAfter,
                    'reference_type' => MoneyRequest::class,
                    'reference_id' => $moneyRequest->id,
                    'description' => 'Agent loaded player wallet: ' . $player->name,
                ]);

                WalletTransaction::create([
                    'user_id' => $player->id,
                    'admin_id' => null,
                    'type' => 'wallet_load',
                    'direction' => 'credit',
                    'amount' => $amount,
                    'balance_before' => $playerBefore,
                    'balance_after' => $playerAfter,
                    'reference_type' => MoneyRequest::class,
                    'reference_id' => $moneyRequest->id,
                    'description' => 'Wallet loaded by agent: ' . $agent->name,
                ]);
            });

            return back()->with('success', 'Money request approved. Agent wallet deducted and player wallet credited.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'request' => $e->getMessage(),
            ]);
        }
    }

    public function rejectMoney(MoneyRequest $moneyRequest)
    {
        try {
            $agent = auth()->user();

            DB::transaction(function () use ($moneyRequest, $agent) {
                $moneyRequest = MoneyRequest::where('id', $moneyRequest->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($moneyRequest->status !== 'pending') {
                    throw new \RuntimeException('This money request is already reviewed.');
                }

                $player = User::where('id', $moneyRequest->user_id)->firstOrFail();

                if ($player->role !== 'player') {
                    throw new \RuntimeException('Only player money requests can be rejected here.');
                }

                if ((int) $player->agent_id !== (int) $agent->id) {
                    throw new \RuntimeException('This player is not assigned to you.');
                }

                $moneyRequest->update([
                    'agent_id' => $agent->id,
                    'admin_id' => null,
                    'status' => 'rejected',
                    'reviewed_at' => now(),
                    'reviewed_by' => $agent->id,
                ]);
            });

            return back()->with('success', 'Money request rejected.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'request' => $e->getMessage(),
            ]);
        }
    }

    public function approveWithdrawal(WithdrawalRequest $withdrawal)
    {
        try {
            $agent = auth()->user();

            DB::transaction(function () use ($withdrawal, $agent) {
                $withdrawal = WithdrawalRequest::where('id', $withdrawal->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($withdrawal->status !== 'pending') {
                    throw new \RuntimeException('This withdrawal request is already reviewed.');
                }

                $player = User::where('id', $withdrawal->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $agent = User::where('id', $agent->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($player->role !== 'player') {
                    throw new \RuntimeException('Only player withdrawal requests can be approved here.');
                }

                if ((int) $player->agent_id !== (int) $agent->id) {
                    throw new \RuntimeException('This player is not assigned to you.');
                }

                if ((int) $withdrawal->agent_id !== (int) $agent->id) {
                    throw new \RuntimeException('This withdrawal is not assigned to you.');
                }

                $amount = (float) $withdrawal->amount;

                $agentBefore = (float) $agent->wallet_balance;
                $agentAfter = $agentBefore + $amount;

                $agent->update([
                    'wallet_balance' => $agentAfter,
                ]);

                $withdrawal->update([
                    'player_id' => $player->id,
                    'agent_id' => $agent->id,
                    'admin_id' => null,
                    'status' => 'approved',
                    'reviewed_at' => now(),
                    'reviewed_by' => $agent->id,
                ]);

                WalletTransaction::create([
                    'user_id' => $agent->id,
                    'admin_id' => null,
                    'type' => 'player_withdrawal_approved_received',
                    'direction' => 'credit',
                    'amount' => $amount,
                    'balance_before' => $agentBefore,
                    'balance_after' => $agentAfter,
                    'reference_type' => WithdrawalRequest::class,
                    'reference_id' => $withdrawal->id,
                    'description' => 'Agent received approved player withdrawal amount from: ' . $player->name,
                ]);
            });

            return back()->with('success', 'Withdrawal approved. Agent wallet credited.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'request' => $e->getMessage(),
            ]);
        }
    }

    public function rejectWithdrawal(WithdrawalRequest $withdrawal)
    {
        try {
            $agent = auth()->user();

            DB::transaction(function () use ($withdrawal, $agent) {
                $withdrawal = WithdrawalRequest::where('id', $withdrawal->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($withdrawal->status !== 'pending') {
                    throw new \RuntimeException('This withdrawal request is already reviewed.');
                }

                $player = User::where('id', $withdrawal->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($player->role !== 'player') {
                    throw new \RuntimeException('Only player withdrawal requests can be rejected here.');
                }

                if ((int) $player->agent_id !== (int) $agent->id) {
                    throw new \RuntimeException('This player is not assigned to you.');
                }

                if ((int) $withdrawal->agent_id !== (int) $agent->id) {
                    throw new \RuntimeException('This withdrawal is not assigned to you.');
                }

                $amount = (float) $withdrawal->amount;

                $playerBefore = (float) $player->wallet_balance;
                $playerAfter = $playerBefore + $amount;

                $player->update([
                    'wallet_balance' => $playerAfter,
                ]);

                $withdrawal->update([
                    'player_id' => $player->id,
                    'agent_id' => $agent->id,
                    'admin_id' => null,
                    'status' => 'rejected',
                    'reviewed_at' => now(),
                    'reviewed_by' => $agent->id,
                ]);

                WalletTransaction::create([
                    'user_id' => $player->id,
                    'admin_id' => null,
                    'type' => 'withdrawal_rejected_refund',
                    'direction' => 'credit',
                    'amount' => $amount,
                    'balance_before' => $playerBefore,
                    'balance_after' => $playerAfter,
                    'reference_type' => WithdrawalRequest::class,
                    'reference_id' => $withdrawal->id,
                    'description' => 'Withdrawal rejected by agent. Amount returned to player wallet.',
                ]);
            });

            return back()->with('success', 'Withdrawal rejected. Amount returned to player wallet.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'request' => $e->getMessage(),
            ]);
        }
    }
}