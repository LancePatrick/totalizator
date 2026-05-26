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
        abort_if($player->role !== 'player', 404);

        $player->update([
            'is_active' => true,
        ]);

        return back()->with('success', 'Player activated.');
    }

    public function deactivate(User $player)
    {
        abort_if($player->role !== 'player', 404);

        $player->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'Player deactivated.');
    }
}