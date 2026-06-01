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
        $player = auth()->user();

        return view('player.wallet.index', [
            'moneyRequests' => MoneyRequest::where('user_id', $player->id)
                ->latest()
                ->take(30)
                ->get(),

            'withdrawals' => WithdrawalRequest::where('user_id', $player->id)
                ->latest()
                ->take(30)
                ->get(),
        ]);
    }

    public function requestMoney(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'reference_number' => ['nullable', 'string', 'max:150'],
            'proof_image' => ['nullable', 'image', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $player = auth()->user();

        $proofPath = null;

        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')
                ->store('proofs/player-money-requests', 'public');
        }

        MoneyRequest::create([
            'user_id' => $player->id,
            'agent_id' => $player->agent_id,
            'admin_id' => null,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'] ?? null,
            'reference_number' => $data['reference_number'] ?? null,
            'proof_image_path' => $proofPath,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Money request submitted to your agent.');
    }

    public function withdraw(Request $request)
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
                $player = User::where('id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($player->role !== 'player') {
                    throw new \RuntimeException('Only players can request withdrawal here.');
                }

                if (!$player->agent_id) {
                    throw new \RuntimeException('You do not have an assigned agent.');
                }

                $amount = (float) $data['amount'];

                if ($amount > (float) $player->wallet_balance) {
                    throw new \RuntimeException('You can only withdraw up to your current wallet balance.');
                }

                $playerBefore = (float) $player->wallet_balance;
                $playerAfter = $playerBefore - $amount;

                $player->update([
                    'wallet_balance' => $playerAfter,
                ]);

                $withdrawal = WithdrawalRequest::create([
                    'user_id' => $player->id,
                    'player_id' => $player->id,
                    'agent_id' => $player->agent_id,
                    'admin_id' => null,
                    'amount' => $amount,
                    'payment_method' => $data['payment_method'],
                    'account_name' => $data['account_name'],
                    'account_number' => $data['account_number'],
                    'notes' => $data['notes'] ?? null,
                    'status' => 'pending',
                ]);

                WalletTransaction::create([
                    'user_id' => $player->id,
                    'admin_id' => null,
                    'type' => 'player_withdrawal_request',
                    'direction' => 'debit',
                    'amount' => $amount,
                    'balance_before' => $playerBefore,
                    'balance_after' => $playerAfter,
                    'reference_type' => WithdrawalRequest::class,
                    'reference_id' => $withdrawal->id,
                    'description' => 'Player withdrawal request submitted. Amount deducted while waiting for agent approval.',
                ]);
            });

            return back()->with('success', 'Withdrawal request submitted. Amount deducted while waiting for agent approval.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'amount' => $e->getMessage(),
            ]);
        }
    }
}