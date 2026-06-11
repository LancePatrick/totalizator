<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionTransaction;
use App\Models\CommissionWithdrawalRequest;
use App\Models\GameBet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminCommissionReportController extends Controller
{
    public function index(Request $request)
    {
        $agents = User::query()
            ->where('role', 'agent')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                    if (Schema::hasColumn('users', 'agent_code')) {
                        $q->orWhere('agent_code', 'like', "%{$search}%");
                    }
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $agents->getCollection()->transform(function ($agent) use ($request) {
            $transactionsQuery = CommissionTransaction::where('agent_id', $agent->id);
            $withdrawalsQuery = CommissionWithdrawalRequest::where('agent_id', $agent->id);

            if ($request->filled('date_from')) {
                $transactionsQuery->whereDate('created_at', '>=', $request->date_from);
                $withdrawalsQuery->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $transactionsQuery->whereDate('created_at', '<=', $request->date_to);
                $withdrawalsQuery->whereDate('created_at', '<=', $request->date_to);
            }

            /*
            |--------------------------------------------------------------------------
            | Correct Agent Commission Earned
            |--------------------------------------------------------------------------
            | Commission earned should be based on valid player bets:
            | Agent Commission = Total valid player bets × 2%
            |--------------------------------------------------------------------------
            */

            $playerIds = User::query()
                ->where('role', 'player')
                ->where('agent_id', $agent->id)
                ->pluck('id');

            $totalPlayerBets = $this->validPlayerBets(
                $playerIds,
                $request->date_from,
                $request->date_to
            );

            $agent->total_player_bets = $totalPlayerBets;
            $agent->players_count = $playerIds->count();

            $agent->total_commission_earned = round($totalPlayerBets * 0.02, 2);

            /*
            |--------------------------------------------------------------------------
            | Converted To Load
            |--------------------------------------------------------------------------
            */

            $agent->total_converted_to_load = (clone $transactionsQuery)
                ->whereIn('type', [
                    'convert_to_load',
                    'commission_converted_to_load',
                    'commission_to_load',
                    'agent_commission_convert',
                    'agent_commission_converted',
                    'commission_convert',
                    'commission_converted',
                    'convert_commission',
                    'converted_commission',
                ])
                ->where('direction', 'debit')
                ->sum('amount');

            /*
            |--------------------------------------------------------------------------
            | Commission Cashout / Withdrawals
            |--------------------------------------------------------------------------
            */

            $agent->pending_commission_withdrawals = (clone $withdrawalsQuery)
                ->where('status', 'pending')
                ->sum('amount');

            $agent->approved_commission_withdrawals = (clone $withdrawalsQuery)
                ->where('status', 'approved')
                ->sum('amount');

            $agent->rejected_commission_withdrawals = (clone $withdrawalsQuery)
                ->where('status', 'rejected')
                ->sum('amount');

            $agent->total_commission_cashout_requested =
                (float) $agent->pending_commission_withdrawals
                + (float) $agent->approved_commission_withdrawals
                + (float) $agent->rejected_commission_withdrawals;

            /*
            |--------------------------------------------------------------------------
            | Available Commission Balance
            |--------------------------------------------------------------------------
            | Rejected cashout is not deducted because it should return to balance.
            |--------------------------------------------------------------------------
            */

            $availableCommission = round(
                (float) $agent->total_commission_earned
                - (float) $agent->total_converted_to_load
                - (float) $agent->pending_commission_withdrawals
                - (float) $agent->approved_commission_withdrawals,
                2
            );

            if ($availableCommission < 0) {
                $availableCommission = 0;
            }

            $agent->commission_balance = $availableCommission;
            $agent->available_commission = $availableCommission;
            $agent->current_commission_balance = $availableCommission;

            /*
            |--------------------------------------------------------------------------
            | Extra aliases for different Blade names
            |--------------------------------------------------------------------------
            */

            $agent->commission_earned = $agent->total_commission_earned;
            $agent->computed_commission = $agent->total_commission_earned;
            $agent->computed_agent_commission = $agent->total_commission_earned;
            $agent->agent_commission_amount = $agent->total_commission_earned;

            $agent->converted_to_load = $agent->total_converted_to_load;
            $agent->cash_withdrawal = $agent->total_commission_cashout_requested;
            $agent->cashout_requested = $agent->total_commission_cashout_requested;
            $agent->pending_cashout = $agent->pending_commission_withdrawals;
            $agent->approved_cashout = $agent->approved_commission_withdrawals;
            $agent->rejected_cashout = $agent->rejected_commission_withdrawals;

            $agent->wallet_amount = (float) ($agent->wallet_balance ?? 0);

            /*
            |--------------------------------------------------------------------------
            | Latest Records
            |--------------------------------------------------------------------------
            */

            $agent->latest_commission_transactions = CommissionTransaction::where('agent_id', $agent->id)
                ->latest()
                ->take(8)
                ->get();

            $agent->latest_commission_withdrawals = CommissionWithdrawalRequest::where('agent_id', $agent->id)
                ->latest()
                ->take(5)
                ->get();

            return $agent;
        });

        /*
        |--------------------------------------------------------------------------
        | Summary Totals
        |--------------------------------------------------------------------------
        | Use all filtered agents, not only the current page.
        |--------------------------------------------------------------------------
        */

        $summaryAgents = User::query()
            ->where('role', 'agent')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                    if (Schema::hasColumn('users', 'agent_code')) {
                        $q->orWhere('agent_code', 'like', "%{$search}%");
                    }
                });
            })
            ->get();

        $totalCommissionEarned = 0;
        $totalConvertedToLoad = 0;
        $totalCashoutRequested = 0;
        $totalPendingCashout = 0;
        $totalApprovedCashout = 0;
        $totalRejectedCashout = 0;

        foreach ($summaryAgents as $summaryAgent) {
            $playerIds = User::query()
                ->where('role', 'player')
                ->where('agent_id', $summaryAgent->id)
                ->pluck('id');

            $totalPlayerBets = $this->validPlayerBets(
                $playerIds,
                $request->date_from,
                $request->date_to
            );

            $totalCommissionEarned += round($totalPlayerBets * 0.02, 2);

            $summaryTransactionsQuery = CommissionTransaction::where('agent_id', $summaryAgent->id);
            $summaryWithdrawalsQuery = CommissionWithdrawalRequest::where('agent_id', $summaryAgent->id);

            if ($request->filled('date_from')) {
                $summaryTransactionsQuery->whereDate('created_at', '>=', $request->date_from);
                $summaryWithdrawalsQuery->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $summaryTransactionsQuery->whereDate('created_at', '<=', $request->date_to);
                $summaryWithdrawalsQuery->whereDate('created_at', '<=', $request->date_to);
            }

            $convertedToLoad = (clone $summaryTransactionsQuery)
                ->whereIn('type', [
                    'convert_to_load',
                    'commission_converted_to_load',
                    'commission_to_load',
                    'agent_commission_convert',
                    'agent_commission_converted',
                    'commission_convert',
                    'commission_converted',
                    'convert_commission',
                    'converted_commission',
                ])
                ->where('direction', 'debit')
                ->sum('amount');

            $pendingCashout = (clone $summaryWithdrawalsQuery)
                ->where('status', 'pending')
                ->sum('amount');

            $approvedCashout = (clone $summaryWithdrawalsQuery)
                ->where('status', 'approved')
                ->sum('amount');

            $rejectedCashout = (clone $summaryWithdrawalsQuery)
                ->where('status', 'rejected')
                ->sum('amount');

            $totalConvertedToLoad += (float) $convertedToLoad;
            $totalPendingCashout += (float) $pendingCashout;
            $totalApprovedCashout += (float) $approvedCashout;
            $totalRejectedCashout += (float) $rejectedCashout;
            $totalCashoutRequested += (float) $pendingCashout + (float) $approvedCashout + (float) $rejectedCashout;
        }

        $totalCommissionBalance = round(
            (float) $totalCommissionEarned
            - (float) $totalConvertedToLoad
            - (float) $totalPendingCashout
            - (float) $totalApprovedCashout,
            2
        );

        if ($totalCommissionBalance < 0) {
            $totalCommissionBalance = 0;
        }

        return view('admin.commission-reports.index', [
            'agents' => $agents,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,

            'totalCommissionEarned' => $totalCommissionEarned,
            'totalConvertedToLoad' => $totalConvertedToLoad,
            'totalCashoutRequested' => $totalCashoutRequested,
            'totalPendingCashout' => $totalPendingCashout,
            'totalApprovedCashout' => $totalApprovedCashout,
            'totalRejectedCashout' => $totalRejectedCashout,
            'totalCommissionBalance' => $totalCommissionBalance,

            /*
            |--------------------------------------------------------------------------
            | Extra aliases for current/old index compatibility
            |--------------------------------------------------------------------------
            */

            'commissionEarned' => $totalCommissionEarned,
            'convertedToLoad' => $totalConvertedToLoad,
            'cashoutRequested' => $totalCashoutRequested,
            'pendingCashout' => $totalPendingCashout,
            'approvedCashout' => $totalApprovedCashout,
            'rejectedCashout' => $totalRejectedCashout,
            'currentCommissionBalance' => $totalCommissionBalance,
            'commissionBalance' => $totalCommissionBalance,
            'currentBalance' => $totalCommissionBalance,
        ]);
    }

    private function validPlayerBets($playerIds, $dateFrom = null, $dateTo = null): float
    {
        $playerIds = collect($playerIds)
            ->filter()
            ->values();

        if ($playerIds->isEmpty()) {
            return 0;
        }

        if (!Schema::hasTable('game_bets')) {
            return 0;
        }

        $betUserColumn = $this->betUserColumn();

        if (!$betUserColumn) {
            return 0;
        }

        $query = GameBet::query()
            ->whereIn($betUserColumn, $playerIds);

        if (Schema::hasColumn('game_bets', 'status')) {
            $query->whereNotIn('status', ['refunded', 'cancelled']);
        }

        if (
            Schema::hasTable('game_rounds') &&
            Schema::hasColumn('game_bets', 'game_round_id') &&
            Schema::hasColumn('game_rounds', 'winning_side')
        ) {
            $query->whereNotIn('game_round_id', function ($subQuery) {
                $subQuery->select('id')
                    ->from('game_rounds')
                    ->where('winning_side', 'cancelled');
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return (float) $query->sum('amount');
    }

    private function betUserColumn(): ?string
    {
        if (!Schema::hasTable('game_bets')) {
            return null;
        }

        if (Schema::hasColumn('game_bets', 'user_id')) {
            return 'user_id';
        }

        if (Schema::hasColumn('game_bets', 'player_id')) {
            return 'player_id';
        }

        return null;
    }
}