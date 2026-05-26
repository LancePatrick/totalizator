<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAgentController extends Controller
{
    public function index(Request $request)
    {
        $agents = User::query()
            ->where('role', 'agent')
            ->withCount([
                'players as total_players_count',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('agent_code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                }

                if ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
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

        return view('admin.agents.index', [
            'agents' => $agents,
        ]);
    }

    public function activate(User $agent)
    {
        abort_if($agent->role !== 'agent', 404);

        $agent->update([
            'is_active' => true,
        ]);

        return back()->with('success', 'Agent activated.');
    }

    public function deactivate(User $agent)
    {
        abort_if($agent->role !== 'agent', 404);

        $agent->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'Agent deactivated.');
    }
}