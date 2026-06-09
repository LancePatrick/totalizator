<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlayerKycController extends Controller
{
    public function index()
    {
        return view('player.kyc.index', [
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->kyc_status === 'approved') {
            return redirect()
                ->route('player.kyc.index')
                ->with('success', 'Your KYC is already verified.');
        }

        if ($user->kyc_status === 'pending') {
            return redirect()
                ->route('player.kyc.index')
                ->withErrors([
                    'kyc' => 'Your KYC is already pending review. Please wait for admin approval.',
                ]);
        }

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'address' => ['required', 'string', 'max:1000'],
            'valid_id_type' => ['required', 'string', 'max:100'],
            'valid_id_number' => ['required', 'string', 'max:100'],
            'valid_id_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'selfie_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $validIdPath = $request->file('valid_id_image')->store('kyc/valid-ids', 'public');
        $selfiePath = $request->file('selfie_image')->store('kyc/selfies', 'public');

        $user->update([
            'kyc_status' => 'pending',
            'kyc_rejection_reason' => null,

            'kyc_full_name' => $data['full_name'],
            'kyc_birthdate' => $data['birthdate'],
            'kyc_address' => $data['address'],
            'kyc_valid_id_type' => $data['valid_id_type'],
            'kyc_valid_id_number' => $data['valid_id_number'],
            'kyc_valid_id_image' => $validIdPath,
            'kyc_selfie_image' => $selfiePath,
            'kyc_submitted_at' => now(),
            'kyc_reviewed_at' => null,
            'kyc_reviewed_by' => null,
        ]);

        return redirect()
            ->route('player.kyc.index')
            ->with('success', 'KYC submitted successfully. Please wait for admin approval.');
    }
}