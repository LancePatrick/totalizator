<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalAgents' => User::where('role', 'agent')->count(),
            'totalPlayers' => User::where('role', 'player')->count(),
            'activeAgents' => User::where('role', 'agent')->where('is_active', true)->count(),
            'activePlayers' => User::where('role', 'player')->where('is_active', true)->count(),
            'pendingKyc' => User::where('role', 'player')->where('kyc_status', 'pending')->count(),
        ]);
    }
}