<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionTransaction;
use App\Models\CommissionWithdrawalRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminCommissionWithdrawalController extends Controller
{
    public function index()
    {
        return view('admin.commission-withdrawals.index', [
            'withdrawals' => CommissionWithdrawalRequest::with('agent')
                ->latest()
                ->paginate(30),
        ]);
    }

    public function approve(CommissionWithdrawalRequest $commissionWithdrawal)
    {
        try {
            DB::transaction(function () use ($commissionWithdrawal) {
                $commissionWithdrawal = CommissionWithdrawalRequest::where('id', $commissionWithdrawal->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($commissionWithdrawal->status !== 'pending') {
                    throw new \RuntimeException('This commission withdrawal is already reviewed.');
                }

                $commissionWithdrawal->update([
                    'admin_id' => auth()->id(),
                    'status' => 'approved',
                    'reviewed_at' => now(),
                    'reviewed_by' => auth()->id(),
                ]);
            });

            return back()->with('success', 'Commission withdrawal approved.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'request' => $e->getMessage(),
            ]);
        }
    }

    public function reject(CommissionWithdrawalRequest $commissionWithdrawal)
    {
        try {
            DB::transaction(function () use ($commissionWithdrawal) {
                $commissionWithdrawal = CommissionWithdrawalRequest::where('id', $commissionWithdrawal->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($commissionWithdrawal->status !== 'pending') {
                    throw new \RuntimeException('This commission withdrawal is already reviewed.');
                }

                $agent = User::where('id', $commissionWithdrawal->agent_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $amount = (float) $commissionWithdrawal->amount;

                $before = (float) $agent->commission_balance;
                $after = $before + $amount;

                $agent->update([
                    'commission_balance' => $after,
                ]);

                $commissionWithdrawal->update([
                    'admin_id' => auth()->id(),
                    'status' => 'rejected',
                    'reviewed_at' => now(),
                    'reviewed_by' => auth()->id(),
                ]);

                CommissionTransaction::create([
                    'agent_id' => $agent->id,
                    'type' => 'commission_withdrawal_rejected_refund',
                    'direction' => 'credit',
                    'amount' => $amount,
                    'balance_before' => $before,
                    'balance_after' => $after,
                    'reference_type' => CommissionWithdrawalRequest::class,
                    'reference_id' => $commissionWithdrawal->id,
                    'description' => 'Commission withdrawal rejected by admin. Amount returned to commission balance.',
                ]);
            });

            return back()->with('success', 'Commission withdrawal rejected and refunded.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'request' => $e->getMessage(),
            ]);
        }
    }
}