<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlayerKycApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'player') {
            return $next($request);
        }

        if (!$user->is_active) {
            return redirect()
                ->route('player.account.inactive')
                ->withErrors([
                    'account' => 'Your account is inactive. Please submit an appeal.',
                ]);
        }

        if ($user->kyc_status !== 'approved') {
            return redirect()
                ->route('player.kyc.index')
                ->withErrors([
                    'kyc' => 'Please complete your KYC verification before using wallet, cash in, cash out, and game features.',
                ]);
        }

        return $next($request);
    }
}