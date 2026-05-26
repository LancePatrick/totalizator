<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\KycSubmission;
use Illuminate\Http\Request;

class PlayerKycController extends Controller
{
    public function index()
    {
        return view('player.kyc.index', [
            'latestKyc' => KycSubmission::where('user_id', auth()->id())
                ->latest()
                ->first(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date'],
            'id_type' => ['required', 'string', 'max:100'],
            'id_number' => ['required', 'string', 'max:100'],
            'id_image' => ['nullable', 'image', 'max:4096'],
            'selfie_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $idImagePath = null;
        $selfieImagePath = null;

        if ($request->hasFile('id_image')) {
            $idImagePath = $request->file('id_image')->store('kyc/id-images', 'public');
        }

        if ($request->hasFile('selfie_image')) {
            $selfieImagePath = $request->file('selfie_image')->store('kyc/selfies', 'public');
        }

        KycSubmission::create([
            'user_id' => auth()->id(),
            'full_name' => $data['full_name'],
            'birthdate' => $data['birthdate'] ?? null,
            'id_type' => $data['id_type'],
            'id_number' => $data['id_number'],
            'id_image_path' => $idImagePath,
            'selfie_image_path' => $selfieImagePath,
            'status' => 'pending',
        ]);

        auth()->user()->update([
            'kyc_status' => 'pending',
        ]);

        return back()->with('success', 'KYC submitted successfully. Please wait for admin approval.');
    }
}