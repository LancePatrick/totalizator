<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlayerAccountStatusController extends Controller
{
    public function inactive()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'player') {
            return redirect()->route('dashboard');
        }

        if ($user->is_active) {
            if ($user->kyc_status !== 'approved') {
                return redirect()->route('player.kyc.index');
            }

            return redirect()->route('player.dashboard');
        }

        return view('player.account.inactive', [
            'user' => $user,
        ]);
    }

    public function submitAppeal(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'player') {
            return redirect()->route('dashboard');
        }

        if ($user->is_active) {
            return redirect()->route('player.dashboard');
        }

        if ($user->appeal_status === 'pending') {
            return back()->withErrors([
                'appeal' => 'You already have a pending appeal. Please wait for admin review.',
            ]);
        }

        $data = $request->validate([
            'appeal_reason' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $user->update([
            'appeal_reason' => $data['appeal_reason'],
            'appeal_status' => 'pending',
            'appeal_submitted_at' => now(),
            'appeal_reviewed_at' => null,
            'appeal_reviewed_by' => null,
        ]);

        return back()->with('success', 'Your appeal has been submitted. Please wait for admin review.');
    }
}