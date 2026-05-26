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
        return view('agent.requests.index', [
            'moneyRequests' => MoneyRequest::with('player')
                ->where('agent_id', auth()->id())
                ->latest()
                ->get(),

            'withdrawals' => WithdrawalRequest::with('player')
                ->where('agent_id', auth()->id())
                ->latest()
                ->get(),
        ]);
    }

    public function approveMoney(MoneyRequest $moneyRequest)
    {
        $agent = auth()->user();

        if (!$agent || $agent->role !== 'agent') {
            abort(403);
        }

        $player = User::whereKey($moneyRequest->user_id)->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Security check
        |--------------------------------------------------------------------------
        | Allow approval only if:
        | 1. money_requests.agent_id is this agent, OR
        | 2. the player is assigned to this agent.
        |--------------------------------------------------------------------------
        */
        $requestBelongsToAgent = (int) $moneyRequest->agent_id === (int) $agent->id;
        $playerBelongsToAgent = (int) $player->agent_id === (int) $agent->id;

        if (!$requestBelongsToAgent && !$playerBelongsToAgent) {
            abort(403);
        }

        if ($moneyRequest->status !== 'pending') {
            return back()->withErrors([
                'request' => 'This request is already reviewed.',
            ]);
        }

        DB::transaction(function () use ($moneyRequest, $agent, $player) {
            $player = User::whereKey($player->id)
                ->lockForUpdate()
                ->firstOrFail();

            $before = (float) $player->wallet_balance;
            $amount = (float) $moneyRequest->amount;
            $after = $before + $amount;

            $player->update([
                'wallet_balance' => $after,
            ]);

            $moneyRequest->update([
                'agent_id' => $agent->id,
                'status' => 'approved',
                'reviewed_by' => $agent->id,
                'reviewed_at' => now(),
            ]);

            WalletTransaction::create([
                'user_id' => $player->id,
                'admin_id' => $agent->id,
                'type' => 'player_money_request',
                'direction' => 'credit',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference_type' => MoneyRequest::class,
                'reference_id' => $moneyRequest->id,
                'description' => 'Player money request approved by agent.',
            ]);
        });

        return redirect()
            ->route('agent.requests.index')
            ->with('success', 'Player money request approved and wallet credited.');
    }

    public function rejectMoney(MoneyRequest $moneyRequest)
    {
        $agent = auth()->user();

        if (!$agent || $agent->role !== 'agent') {
            abort(403);
        }

        $player = User::whereKey($moneyRequest->user_id)->firstOrFail();

        $requestBelongsToAgent = (int) $moneyRequest->agent_id === (int) $agent->id;
        $playerBelongsToAgent = (int) $player->agent_id === (int) $agent->id;

        if (!$requestBelongsToAgent && !$playerBelongsToAgent) {
            abort(403);
        }

        if ($moneyRequest->status !== 'pending') {
            return back()->withErrors([
                'request' => 'This request is already reviewed.',
            ]);
        }

        $moneyRequest->update([
            'agent_id' => $agent->id,
            'status' => 'rejected',
            'reviewed_by' => $agent->id,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('agent.requests.index')
            ->with('success', 'Player money request rejected.');
    }

    public function approveWithdrawal(WithdrawalRequest $withdrawal)
    {
        $agent = auth()->user();

        if (!$agent || $agent->role !== 'agent') {
            abort(403);
        }

        $player = User::whereKey($withdrawal->user_id)->firstOrFail();

        $requestBelongsToAgent = (int) $withdrawal->agent_id === (int) $agent->id;
        $playerBelongsToAgent = (int) $player->agent_id === (int) $agent->id;

        if (!$requestBelongsToAgent && !$playerBelongsToAgent) {
            abort(403);
        }

        if ($withdrawal->status !== 'pending') {
            return back()->withErrors([
                'request' => 'This withdrawal is already reviewed.',
            ]);
        }

        DB::transaction(function () use ($withdrawal, $agent, $player) {
            $player = User::whereKey($player->id)
                ->lockForUpdate()
                ->firstOrFail();

            $before = (float) $player->wallet_balance;
            $amount = (float) $withdrawal->amount;

            if ($before < $amount) {
                throw new \Exception('Insufficient player wallet balance.');
            }

            $after = $before - $amount;

            $player->update([
                'wallet_balance' => $after,
            ]);

            $withdrawal->update([
                'agent_id' => $agent->id,
                'status' => 'approved',
                'reviewed_by' => $agent->id,
                'reviewed_at' => now(),
            ]);

            WalletTransaction::create([
                'user_id' => $player->id,
                'admin_id' => $agent->id,
                'type' => 'player_withdrawal',
                'direction' => 'debit',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference_type' => WithdrawalRequest::class,
                'reference_id' => $withdrawal->id,
                'description' => 'Player withdrawal approved by agent.',
            ]);
        });

        return redirect()
            ->route('agent.requests.index')
            ->with('success', 'Player withdrawal approved and wallet debited.');
    }

    public function rejectWithdrawal(WithdrawalRequest $withdrawal)
    {
        $agent = auth()->user();

        if (!$agent || $agent->role !== 'agent') {
            abort(403);
        }

        $player = User::whereKey($withdrawal->user_id)->firstOrFail();

        $requestBelongsToAgent = (int) $withdrawal->agent_id === (int) $agent->id;
        $playerBelongsToAgent = (int) $player->agent_id === (int) $agent->id;

        if (!$requestBelongsToAgent && !$playerBelongsToAgent) {
            abort(403);
        }

        if ($withdrawal->status !== 'pending') {
            return back()->withErrors([
                'request' => 'This withdrawal is already reviewed.',
            ]);
        }

        $withdrawal->update([
            'agent_id' => $agent->id,
            'status' => 'rejected',
            'reviewed_by' => $agent->id,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('agent.requests.index')
            ->with('success', 'Player withdrawal rejected.');
    }
}