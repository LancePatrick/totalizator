<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\MoneyRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerWalletController extends Controller
{
    public function index()
    {
        if ($redirect = $this->blockIfKycNotApproved()) {
            return $redirect;
        }

        $user = auth()->user();

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(50)
            ->get();

        $moneyRequests = MoneyRequest::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        $withdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return view('player.wallet.index', [
            'user' => $user,
            'transactions' => $transactions,
            'moneyRequests' => $moneyRequests,

            // important: ginagamit ito ng blade mo
            'withdrawals' => $withdrawals,

            // extra alias kung may ibang part ng blade na ito ang gamit
            'withdrawalRequests' => $withdrawals,
        ]);
    }

    public function requestMoney(Request $request)
    {
        if ($redirect = $this->blockIfKycNotApproved()) {
            return $redirect;
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = auth()->user();

        if ($user->role !== 'player') {
            return back()->withErrors([
                'request' => 'Only players can send money requests here.',
            ]);
        }

        MoneyRequest::create([
            'user_id' => $user->id,
            'agent_id' => $user->agent_id,
            'admin_id' => null,
            'amount' => $data['amount'],
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Money request submitted successfully.');
    }

    public function withdraw(Request $request)
    {
        if ($redirect = $this->blockIfKycNotApproved()) {
            return $redirect;
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        return DB::transaction(function () use ($data) {
            $user = User::whereKey(auth()->id())
                ->lockForUpdate()
                ->firstOrFail();

            if ($user->role !== 'player') {
                return back()->withErrors([
                    'request' => 'Only players can send withdrawal requests here.',
                ]);
            }

            if ($user->kyc_status !== 'approved') {
                return redirect()
                    ->route('player.kyc.index')
                    ->withErrors([
                        'kyc' => 'You need to complete KYC verification before using wallet features.',
                    ]);
            }

            $amount = (float) $data['amount'];

            if ((float) $user->wallet_balance < $amount) {
                return back()->withErrors([
                    'amount' => 'Insufficient wallet balance.',
                ]);
            }

            $balanceBefore = (float) $user->wallet_balance;
            $balanceAfter = $balanceBefore - $amount;

            $user->update([
                'wallet_balance' => $balanceAfter,
            ]);

            $withdrawal = WithdrawalRequest::create([
                'user_id' => $user->id,
                'player_id' => $user->id,
                'agent_id' => $user->agent_id,
                'admin_id' => null,
                'amount' => $amount,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            WalletTransaction::create([
                'user_id' => $user->id,
                'admin_id' => null,
                'type' => 'withdrawal_request',
                'direction' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => WithdrawalRequest::class,
                'reference_id' => $withdrawal->id,
                'description' => 'Withdrawal request submitted.',
            ]);

            return back()->with('success', 'Withdrawal request submitted successfully.');
        });
    }

    public function moneyRequest(Request $request)
    {
        return $this->requestMoney($request);
    }

    public function withdrawalRequest(Request $request)
    {
        return $this->withdraw($request);
    }

    public function cashIn(Request $request)
    {
        return $this->requestMoney($request);
    }

    public function cashOut(Request $request)
    {
        return $this->withdraw($request);
    }

    private function blockIfKycNotApproved()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'player' && $user->kyc_status !== 'approved') {
            return redirect()
                ->route('player.kyc.index')
                ->withErrors([
                    'kyc' => 'You need to complete KYC verification before using wallet, cash in, cash out, and request features.',
                ]);
        }

        return null;
    }
}