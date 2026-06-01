<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionTransaction;
use App\Models\CommissionWithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;

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
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('agent_code', 'like', "%{$search}%");
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

            $agent->total_commission_earned = (clone $transactionsQuery)
                ->where('type', 'player_bet_commission')
                ->where('direction', 'credit')
                ->sum('amount');

            $agent->total_converted_to_load = (clone $transactionsQuery)
                ->where('type', 'convert_to_load')
                ->where('direction', 'debit')
                ->sum('amount');

            $agent->total_commission_cashout_requested = (clone $transactionsQuery)
                ->where('type', 'commission_withdrawal_request')
                ->where('direction', 'debit')
                ->sum('amount');

            $agent->pending_commission_withdrawals = (clone $withdrawalsQuery)
                ->where('status', 'pending')
                ->sum('amount');

            $agent->approved_commission_withdrawals = (clone $withdrawalsQuery)
                ->where('status', 'approved')
                ->sum('amount');

            $agent->rejected_commission_withdrawals = (clone $withdrawalsQuery)
                ->where('status', 'rejected')
                ->sum('amount');

            $agent->latest_commission_transactions = CommissionTransaction::where('agent_id', $agent->id)
                ->latest()
                ->take(5)
                ->get();

            return $agent;
        });

        $summaryTransactionsQuery = CommissionTransaction::query();
        $summaryWithdrawalsQuery = CommissionWithdrawalRequest::query();

        if ($request->filled('date_from')) {
            $summaryTransactionsQuery->whereDate('created_at', '>=', $request->date_from);
            $summaryWithdrawalsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $summaryTransactionsQuery->whereDate('created_at', '<=', $request->date_to);
            $summaryWithdrawalsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        return view('admin.commission-reports.index', [
            'agents' => $agents,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,

            'totalCommissionEarned' => (clone $summaryTransactionsQuery)
                ->where('type', 'player_bet_commission')
                ->where('direction', 'credit')
                ->sum('amount'),

            'totalConvertedToLoad' => (clone $summaryTransactionsQuery)
                ->where('type', 'convert_to_load')
                ->where('direction', 'debit')
                ->sum('amount'),

            'totalCashoutRequested' => (clone $summaryTransactionsQuery)
                ->where('type', 'commission_withdrawal_request')
                ->where('direction', 'debit')
                ->sum('amount'),

            'totalPendingCashout' => (clone $summaryWithdrawalsQuery)
                ->where('status', 'pending')
                ->sum('amount'),

            'totalApprovedCashout' => (clone $summaryWithdrawalsQuery)
                ->where('status', 'approved')
                ->sum('amount'),
        ]);
    }
}