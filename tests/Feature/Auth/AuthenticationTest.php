<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Inactive Player Appeal Redirect
        |--------------------------------------------------------------------------
        | Huwag i-logout ang inactive player.
        | Kailangan naka-login siya para makita niya ang appeal page.
        |--------------------------------------------------------------------------
        */
        if ($user && $user->role === 'player' && !$user->is_active) {
            return redirect()->route('player.appeal.index');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if (route('home', [], false)) {
            return redirect()->route('home');
        }

        return redirect('/');
    }
}