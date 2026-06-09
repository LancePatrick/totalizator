<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPlayerController extends Controller
{
    public function index(Request $request)
    {
        $agents = User::where('role', 'agent')
            ->orderBy('name')
            ->get();

        $players = User::query()
            ->where('role', 'player')
            ->with('agent')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('agent_id'), function ($query) use ($request) {
                $query->where('agent_id', $request->agent_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                }

                if ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->when($request->filled('kyc_status'), function ($query) use ($request) {
                $query->where('kyc_status', $request->kyc_status);
            })
            ->when($request->filled('appeal_status'), function ($query) use ($request) {
                $query->where('appeal_status', $request->appeal_status);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.players.index', [
            'players' => $players,
            'agents' => $agents,
        ]);
    }

    public function activate(User $player)
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        abort_if($player->role !== 'player', 404);

        $player->update([
            'is_active' => true,

            'deactivation_reason' => null,
            'deactivated_at' => null,
            'deactivated_by' => null,

            'appeal_status' => 'approved',
            'appeal_admin_note' => 'Account activated by admin.',
            'appeal_reviewed_at' => now(),
            'appeal_reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Player activated successfully.');
    }

    public function deactivate(Request $request, User $player)
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        abort_if($player->role !== 'player', 404);

        $data = $request->validate([
            'deactivation_reason' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $player->update([
            'is_active' => false,

            'deactivation_reason' => $data['deactivation_reason'],
            'deactivated_at' => now(),
            'deactivated_by' => auth()->id(),

            'appeal_message' => null,
            'appeal_proof' => null,
            'appeal_status' => null,
            'appeal_admin_note' => null,
            'appeal_submitted_at' => null,
            'appeal_reviewed_at' => null,
            'appeal_reviewed_by' => null,
        ]);

        return back()->with('success', 'Player deactivated successfully.');
    }

    public function approveAppeal(User $player)
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        abort_if($player->role !== 'player', 404);

        $player->update([
            'is_active' => true,

            'deactivation_reason' => null,
            'deactivated_at' => null,
            'deactivated_by' => null,

            'appeal_status' => 'approved',
            'appeal_admin_note' => 'Appeal approved. Account activated again.',
            'appeal_reviewed_at' => now(),
            'appeal_reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Appeal approved. Player activated successfully.');
    }

    public function rejectAppeal(Request $request, User $player)
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        abort_if($player->role !== 'player', 404);

        $data = $request->validate([
            'appeal_admin_note' => ['nullable', 'string', 'max:1000'],
            'appeal_reject_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $reason = $data['appeal_admin_note']
            ?? $data['appeal_reject_reason']
            ?? 'Your appeal was rejected by admin. Please submit valid proof.';

        $player->update([
            'is_active' => false,

            'appeal_status' => 'rejected',
            'appeal_admin_note' => $reason,
            'appeal_reviewed_at' => now(),
            'appeal_reviewed_by' => auth()->id(),

            'deactivation_reason' => $reason,
        ]);

        return back()->with('success', 'Player appeal rejected.');
    }
}