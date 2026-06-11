<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameBet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminAgentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $agents = User::query()
            ->where('role', 'agent')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('agent_code', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $agents->getCollection()->transform(function ($agent) {
            $playerIds = User::where('role', 'player')
                ->where('agent_id', $agent->id)
                ->pluck('id');

            $agent->players_count = $playerIds->count();

            $agent->total_player_bets = Schema::hasTable('game_bets')
                ? (float) GameBet::whereIn('user_id', $playerIds)
                    ->whereNotIn('status', ['refunded', 'cancelled'])
                    ->when(Schema::hasTable('game_rounds'), function ($query) {
                        $query->whereNotIn('game_round_id', function ($subQuery) {
                            $subQuery->select('id')
                                ->from('game_rounds')
                                ->where('winning_side', 'cancelled');
                        });
                    })
                    ->sum('amount')
                : 0;

            $agent->computed_commission = round($agent->total_player_bets * 0.02, 2);

            $agent->available_commission = Schema::hasColumn('users', 'commission_balance')
                ? (float) ($agent->commission_balance ?? 0)
                : $agent->computed_commission;

            $agent->wallet_amount = Schema::hasColumn('users', 'wallet_balance')
                ? (float) ($agent->wallet_balance ?? 0)
                : 0;

            return $agent;
        });

        return view('admin.agents.index', [
            'agents' => $agents,
            'search' => $search,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function activate(User $agent)
    {
        if ($agent->role !== 'agent') {
            return back()->withErrors([
                'agent' => 'Selected user is not an agent.',
            ]);
        }

        $agent->update([
            'is_active' => true,
        ]);

        return back()->with('success', 'Agent activated successfully.');
    }

    public function deactivate(User $agent)
    {
        if ($agent->role !== 'agent') {
            return back()->withErrors([
                'agent' => 'Selected user is not an agent.',
            ]);
        }

        $agent->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'Agent deactivated successfully.');
    }

    public function updatePassword(Request $request, User $agent)
    {
        if ($agent->role !== 'agent') {
            return back()->withErrors([
                'agent' => 'Selected user is not an agent.',
            ]);
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $agent->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'Agent password changed successfully.');
    }
}