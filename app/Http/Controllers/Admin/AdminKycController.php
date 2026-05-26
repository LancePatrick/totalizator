<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycSubmission;
use Illuminate\Http\Request;

class AdminKycController extends Controller
{
    public function index()
    {
        return view('admin.kyc.index', [
            'kycs' => KycSubmission::with('user')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function approve(KycSubmission $kyc)
    {
        $kyc->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => null,
        ]);

        $kyc->user->update([
            'kyc_status' => 'approved',
        ]);

        return back()->with('success', 'KYC approved successfully.');
    }

    public function reject(Request $request, KycSubmission $kyc)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $kyc->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        $kyc->user->update([
            'kyc_status' => 'rejected',
        ]);

        return back()->with('success', 'KYC rejected.');
    }
}