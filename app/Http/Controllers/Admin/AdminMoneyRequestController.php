<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MoneyRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMoneyRequestController extends Controller
{
    public function index(Request $request)
    {
        $agentMoneyRequests = MoneyRequest::query()
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('role', 'agent');
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('agent_code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->latest()
            ->paginate(20, ['*'], 'money_page')
            ->withQueryString();

        $agentWithdrawals = WithdrawalRequest::query()
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('role', 'agent');
            })
            ->latest()
            ->paginate(20, ['*'], 'withdrawal_page')
            ->withQueryString();

        return view('admin.money-requests.index', [
            'agentMoneyRequests' => $agentMoneyRequests,
            'agentWithdrawals' => $agentWithdrawals,
        ]);
    }

    public function approveMoney(MoneyRequest $moneyRequest)
    {
        if ($moneyRequest->status !== 'pending') {
            return back()->withErrors([
                'request' => 'This request is already reviewed.',
            ]);
        }

        DB::transaction(function () use ($moneyRequest) {
            $agent = User::whereKey($moneyRequest->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($agent->role !== 'agent', 403);

            $before = (float) $agent->wallet_balance;
            $amount = (float) $moneyRequest->amount;
            $after = $before + $amount;

            $agent->update([
                'wallet_balance' => $after,
            ]);

            $moneyRequest->update([
                'admin_id' => auth()->id(),
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            WalletTransaction::create([
                'user_id' => $agent->id,
                'admin_id' => auth()->id(),
                'type' => 'agent_money_request',
                'direction' => 'credit',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference_type' => MoneyRequest::class,
                'reference_id' => $moneyRequest->id,
                'description' => 'Agent money request approved by admin.',
            ]);
        });

        return back()->with('success', 'Agent money request approved and wallet credited.');
    }

    public function rejectMoney(MoneyRequest $moneyRequest)
    {
        if ($moneyRequest->status !== 'pending') {
            return back()->withErrors([
                'request' => 'This request is already reviewed.',
            ]);
        }

        $moneyRequest->update([
            'admin_id' => auth()->id(),
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Agent money request rejected.');
    }

    public function approveWithdrawal(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->withErrors([
                'request' => 'This withdrawal is already reviewed.',
            ]);
        }

        DB::transaction(function () use ($withdrawal) {
            $agent = User::whereKey($withdrawal->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($agent->role !== 'agent', 403);

            $before = (float) $agent->wallet_balance;
            $amount = (float) $withdrawal->amount;

            if ($before < $amount) {
                throw new \Exception('Insufficient agent wallet balance.');
            }

            $after = $before - $amount;

            $agent->update([
                'wallet_balance' => $after,
            ]);

            $withdrawal->update([
                'admin_id' => auth()->id(),
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            WalletTransaction::create([
                'user_id' => $agent->id,
                'admin_id' => auth()->id(),
                'type' => 'agent_withdrawal',
                'direction' => 'debit',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference_type' => WithdrawalRequest::class,
                'reference_id' => $withdrawal->id,
                'description' => 'Agent withdrawal approved by admin.',
            ]);
        });

        return back()->with('success', 'Agent withdrawal approved and wallet debited.');
    }

    public function rejectWithdrawal(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->withErrors([
                'request' => 'This withdrawal is already reviewed.',
            ]);
        }

        $withdrawal->update([
            'admin_id' => auth()->id(),
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Agent withdrawal rejected.');
    }
}