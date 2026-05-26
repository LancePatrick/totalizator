<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\MoneyRequest;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class PlayerWalletController extends Controller
{
    public function index()
    {
        return view('player.wallet.index', [
            'moneyRequests' => MoneyRequest::where('user_id', auth()->id())
                ->latest()
                ->take(30)
                ->get(),

            'withdrawals' => WithdrawalRequest::where('user_id', auth()->id())
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

        if (!$player->agent_id) {
            return back()->withErrors([
                'agent' => 'You do not have an assigned agent yet.',
            ]);
        }

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

        $player = auth()->user();

        if (!$player->agent_id) {
            return back()->withErrors([
                'agent' => 'You do not have an assigned agent yet.',
            ]);
        }

        if ((float) $player->wallet_balance < (float) $data['amount']) {
            return back()->withErrors([
                'amount' => 'Insufficient wallet balance.',
            ]);
        }

        WithdrawalRequest::create([
            'user_id' => $player->id,
            'agent_id' => $player->agent_id,
            'admin_id' => null,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'account_name' => $data['account_name'],
            'account_number' => $data['account_number'],
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Withdrawal request submitted to your agent.');
    }
}