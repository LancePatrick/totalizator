<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\GameBet;
use App\Models\MoneyRequest;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class AgentPlayerController extends Controller
{
    public function index(Request $request)
    {
        $agent = auth()->user();

        $players = User::query()
            ->where('role', 'player')
            ->where('agent_id', $agent->id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $players->getCollection()->transform(function ($player) {
            $player->total_bets = GameBet::where('user_id', $player->id)->sum('amount');

            $player->total_load_requests = MoneyRequest::where('user_id', $player->id)
                ->where('status', 'approved')
                ->sum('amount');

            $player->total_withdrawals = WithdrawalRequest::where('user_id', $player->id)
                ->where('status', 'approved')
                ->sum('amount');

            return $player;
        });

        return view('agent.players.index', [
            'agent' => $agent,
            'players' => $players,
            'registrationLink' => $agent->registration_link,
        ]);
    }
}