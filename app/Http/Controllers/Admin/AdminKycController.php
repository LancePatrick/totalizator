<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminKycController extends Controller
{
    public function index()
    {
        $kycUsers = User::where('role', 'player')
            ->whereNotNull('kyc_status')
            ->whereIn('kyc_status', ['pending', 'approved', 'rejected'])
            ->latest('kyc_submitted_at')
            ->latest('id')
            ->get();

        return view('admin.kyc.index', [
            'kycUsers' => $kycUsers,
        ]);
    }

    public function approve(User $kyc)
    {
        if ($kyc->role !== 'player') {
            return back()->withErrors([
                'kyc' => 'Only player KYC can be approved.',
            ]);
        }

        $kyc->update([
            'kyc_status' => 'approved',
            'kyc_rejection_reason' => null,
            'kyc_reviewed_at' => now(),
            'kyc_reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Player KYC approved successfully.');
    }

    public function reject(Request $request, User $kyc)
    {
        if ($kyc->role !== 'player') {
            return back()->withErrors([
                'kyc' => 'Only player KYC can be rejected.',
            ]);
        }

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $kyc->update([
            'kyc_status' => 'rejected',
            'kyc_rejection_reason' => $data['reason'] ?? 'KYC rejected. Please submit another KYC.',
            'kyc_reviewed_at' => now(),
            'kyc_reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Player KYC rejected successfully.');
    }
}