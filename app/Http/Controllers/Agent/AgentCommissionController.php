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
use Illuminate\Support\Facades\Schema;

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

        $betsQuery = GameBet::query()
            ->whereIn('user_id', $playerIds)
            ->whereNotIn('status', ['refunded', 'cancelled']);

        if (Schema::hasTable('game_rounds')) {
            $betsQuery->whereNotIn('game_round_id', function ($query) {
                $query->select('id')
                    ->from('game_rounds')
                    ->where('winning_side', 'cancelled');
            });
        }

        if ($request->filled('date_from')) {
            $betsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $betsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $totalPlayerBets = (float) (clone $betsQuery)->sum('amount');
        $computedCommission = round($totalPlayerBets * 0.02, 2);

        $transactions = CommissionTransaction::where($this->commissionAgentColumn(), $agent->id)
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

        if (!Schema::hasTable('commission_transactions')) {
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

            $betsQuery = GameBet::with('user')
                ->whereIn('user_id', $playerIds)
                ->whereNotIn('status', ['refunded', 'cancelled']);

            if (Schema::hasTable('game_rounds')) {
                $betsQuery->whereNotIn('game_round_id', function ($query) {
                    $query->select('id')
                        ->from('game_rounds')
                        ->where('winning_side', 'cancelled');
                });
            }

            $bets = $betsQuery
                ->oldest('id')
                ->get();

            foreach ($bets as $bet) {
                if ($this->commissionAlreadyCredited($agent->id, $bet->id)) {
                    continue;
                }

                $commissionAmount = round((float) $bet->amount * 0.02, 2);

                if ($commissionAmount <= 0) {
                    continue;
                }

                $before = (float) ($agent->commission_balance ?? 0);
                $after = round($before + $commissionAmount, 2);

                if (Schema::hasColumn('users', 'commission_balance')) {
                    $agent->update([
                        'commission_balance' => $after,
                    ]);
                }

                CommissionTransaction::create($this->onlyExistingColumns('commission_transactions', [
                    'user_id' => $agent->id,
                    'agent_id' => $agent->id,
                    'player_id' => $bet->user_id,
                    'game_round_id' => $bet->game_round_id,
                    'game_bet_id' => $bet->id,

                    'type' => 'player_bet_commission',
                    'direction' => 'credit',
                    'amount' => $commissionAmount,
                    'balance_before' => $before,
                    'balance_after' => $after,

                    'reference_type' => GameBet::class,
                    'reference_id' => $bet->id,

                    'description' => '2% commission earned from player bet: ' . ($bet->user?->name ?? 'Player'),
                ]));

                $agent->commission_balance = $after;
            }
        });
    }

    private function commissionAlreadyCredited(int $agentId, int $betId): bool
    {
        if (!Schema::hasTable('commission_transactions')) {
            return false;
        }

        $query = CommissionTransaction::query()
            ->where('type', 'player_bet_commission');

        if (Schema::hasColumn('commission_transactions', 'agent_id')) {
            $query->where('agent_id', $agentId);
        } elseif (Schema::hasColumn('commission_transactions', 'user_id')) {
            $query->where('user_id', $agentId);
        }

        if (Schema::hasColumn('commission_transactions', 'game_bet_id')) {
            return (clone $query)
                ->where('game_bet_id', $betId)
                ->exists();
        }

        if (
            Schema::hasColumn('commission_transactions', 'reference_type') &&
            Schema::hasColumn('commission_transactions', 'reference_id')
        ) {
            return (clone $query)
                ->where('reference_type', GameBet::class)
                ->where('reference_id', $betId)
                ->exists();
        }

        $description = 'bet: ' . $betId;

        if (Schema::hasColumn('commission_transactions', 'description')) {
            return (clone $query)
                ->where('description', 'like', '%' . $description . '%')
                ->exists();
        }

        return false;
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

                if ((float) ($agent->commission_balance ?? 0) < $amount) {
                    throw new \RuntimeException('Insufficient commission balance.');
                }

                $commissionBefore = (float) ($agent->commission_balance ?? 0);
                $walletBefore = (float) ($agent->wallet_balance ?? 0);

                $commissionAfter = round($commissionBefore - $amount, 2);
                $walletAfter = round($walletBefore + $amount, 2);

                $agent->update([
                    'commission_balance' => $commissionAfter,
                    'wallet_balance' => $walletAfter,
                ]);

                if (Schema::hasTable('commission_transactions')) {
                    CommissionTransaction::create($this->onlyExistingColumns('commission_transactions', [
                        'user_id' => $agent->id,
                        'agent_id' => $agent->id,
                        'type' => 'convert_to_load',
                        'direction' => 'debit',
                        'amount' => $amount,
                        'balance_before' => $commissionBefore,
                        'balance_after' => $commissionAfter,
                        'description' => 'Commission converted to load wallet.',
                    ]));
                }

                if (Schema::hasTable('wallet_transactions')) {
                    WalletTransaction::create($this->onlyExistingColumns('wallet_transactions', [
                        'user_id' => $agent->id,
                        'admin_id' => null,
                        'type' => 'commission_converted_to_load',
                        'direction' => 'credit',
                        'amount' => $amount,
                        'balance_before' => $walletBefore,
                        'balance_after' => $walletAfter,
                        'description' => 'Commission converted to wallet load.',
                    ]));
                }
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

                if ((float) ($agent->commission_balance ?? 0) < $amount) {
                    throw new \RuntimeException('Insufficient commission balance.');
                }

                $before = (float) ($agent->commission_balance ?? 0);
                $after = round($before - $amount, 2);

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

                if (Schema::hasTable('commission_transactions')) {
                    CommissionTransaction::create($this->onlyExistingColumns('commission_transactions', [
                        'user_id' => $agent->id,
                        'agent_id' => $agent->id,
                        'type' => 'commission_withdrawal_request',
                        'direction' => 'debit',
                        'amount' => $amount,
                        'balance_before' => $before,
                        'balance_after' => $after,
                        'reference_type' => CommissionWithdrawalRequest::class,
                        'reference_id' => $withdrawal->id,
                        'description' => 'Commission withdrawal requested. Amount held while pending admin approval.',
                    ]));
                }
            });

            return back()->with('success', 'Commission withdrawal request submitted.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'amount' => $e->getMessage(),
            ]);
        }
    }

    private function commissionAgentColumn(): string
    {
        if (Schema::hasColumn('commission_transactions', 'agent_id')) {
            return 'agent_id';
        }

        return 'user_id';
    }

    private function onlyExistingColumns(string $table, array $payload): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        return collect($payload)
            ->filter(function ($value, $column) use ($table) {
                return Schema::hasColumn($table, $column);
            })
            ->all();
    }
}