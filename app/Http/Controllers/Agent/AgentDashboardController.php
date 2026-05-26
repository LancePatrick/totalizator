<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\User;

class AgentDashboardController extends Controller
{
    public function index()
    {
        $agent = auth()->user();

        return view('agent.dashboard', [
            'agent' => $agent,
            'totalPlayers' => User::where('agent_id', $agent->id)->where('role', 'player')->count(),
            'activePlayers' => User::where('agent_id', $agent->id)->where('role', 'player')->where('is_active', true)->count(),
            'inactivePlayers' => User::where('agent_id', $agent->id)->where('role', 'player')->where('is_active', false)->count(),
            'pendingKyc' => User::where('agent_id', $agent->id)->where('role', 'player')->where('kyc_status', 'pending')->count(),
            'players' => User::where('agent_id', $agent->id)->where('role', 'player')->latest()->take(8)->get(),
        ]);
    }
}