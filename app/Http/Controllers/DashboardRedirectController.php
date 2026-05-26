<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Your account is inactive. Please contact support.',
                ]);
        }

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'agent' => redirect()->route('agent.dashboard'),
            default => redirect()->route('player.dashboard'),
        };
    }
}