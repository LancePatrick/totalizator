<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameBet;
use App\Models\GameRound;
use App\Models\LogrohanEntry;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminGameController extends Controller
{
    public function index(Request $request)
    {
        $gameList = $this->visibleAdminRooms();

        $currentGame = null;

        if ($request->filled('game_id')) {
            $currentGame = GameRound::whereKey($request->game_id)
                ->whereIn('status', ['waiting', 'open', 'closed', 'ended'])
                ->first();
        }

        if (!$currentGame && $request->filled('game_id')) {
            return redirect()
                ->route('admin.games.index')
                ->withErrors(['game' => 'Selected game room is already settled or not available anymore.']);
        }

        return view('admin.games.index', [
            'currentGame' => $currentGame,
            'gameList' => $gameList,
            'logrohan' => LogrohanEntry::latest()->take(50)->get(),
        ]);
    }

    public function liveData(Request $request)
    {
        $currentGame = null;

        if ($request->filled('game_id')) {
            $currentGame = GameRound::whereKey($request->game_id)
                ->whereIn('status', ['waiting', 'open', 'closed', 'ended'])
                ->first();
        }

        $gameList = $this->visibleAdminRooms()
            ->map(function ($game) {
                return [
                    'id' => $game->id,
                    'title' => $game->title ?? $game->round_name ?? 'Game Room',
                    'round_code' => $game->round_code ?? $game->round_number ?? $game->id,
                    'status' => $game->status,
                    'total_pool' => (float) ($game->total_pool ?? 0),
                    'created_at' => optional($game->created_at)->format('M d, Y h:i A'),
                ];
            });

        $logrohan = LogrohanEntry::latest()
            ->take(50)
            ->get()
            ->map(function ($entry) {
                $result = strtolower($entry->result ?? $entry->winning_side ?? 'cancelled');

                if (in_array($result, ['cancel', 'canceled'])) {
                    $result = 'cancelled';
                }

                if (!in_array($result, ['meron', 'wala', 'draw', 'cancelled'])) {
                    $result = 'cancelled';
                }

                return [
                    'id' => $entry->id,
                    'round_number' => $entry->round_number ?? $entry->round_code ?? 'Room',
                    'result' => strtoupper($result),
                    'result_key' => $result,
                    'created_at' => optional($entry->created_at)->format('M d, Y h:i A'),
                ];
            });

        if (!$currentGame) {
            return response()->json([
                'has_game' => false,
                'games' => $gameList,
                'logrohan' => $logrohan,
            ]);
        }

        return response()->json([
            'has_game' => true,
            'game' => [
                'id' => $currentGame->id,
                'title' => $currentGame->title ?? $currentGame->round_name ?? 'Game Room',
                'round_code' => $currentGame->round_code ?? $currentGame->round_number ?? $currentGame->id,
                'status' => $currentGame->status,
                'video_url' => $currentGame->video_url,

                'meron_total' => (float) ($currentGame->meron_total ?? 0),
                'wala_total' => (float) ($currentGame->wala_total ?? 0),
                'draw_total' => (float) ($currentGame->draw_total ?? 0),
                'total_pool' => (float) ($currentGame->total_pool ?? 0),
                'net_pool' => (float) ($currentGame->net_pool ?? 0),

                'meron_odds' => (float) ($currentGame->meron_odds ?? 0),
                'wala_odds' => (float) ($currentGame->wala_odds ?? 0),
                'draw_odds' => (float) ($currentGame->draw_odds ?? 0),

                'winning_side' => $currentGame->winning_side,
            ],
            'games' => $gameList,
            'logrohan' => $logrohan,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'game_title' => ['required', 'string', 'max:255'],
            'round_number' => ['nullable', 'string', 'max:100'],
            'video_url' => ['nullable', 'string', 'max:2000'],
        ]);

        $payload = [
            'title' => $data['game_title'],
            'round_name' => $data['game_title'],
            'round_code' => $data['round_number'] ?? null,
            'round_number' => $data['round_number'] ?? null,
            'video_url' => $data['video_url'] ?? null,

            'status' => 'waiting',
            'winning_side' => null,

            'meron_total' => 0,
            'wala_total' => 0,
            'draw_total' => 0,
            'total_pool' => 0,
            'net_pool' => 0,

            'meron_odds' => 0,
            'wala_odds' => 0,
            'draw_odds' => 0,

            'commission_rate' => 5,
            'commission_amount' => 0,
            'company_commission_rate' => 3,
            'agent_commission_rate' => 2,
            'company_commission_amount' => 0,
            'agent_commission_amount' => 0,

            'payout_total' => 0,
            'admin_income' => 0,
        ];

        $game = GameRound::create($this->onlyExistingColumns('game_rounds', $payload));

        if (empty($data['round_number'])) {
            $autoCode = 'GAME-' . str_pad((string) $game->id, 4, '0', STR_PAD_LEFT);

            $game->update($this->onlyExistingColumns('game_rounds', [
                'round_code' => $autoCode,
                'round_number' => $autoCode,
            ]));
        }

        return redirect()
            ->route('admin.games.index', ['game_id' => $game->id])
            ->with('success', 'Game room created. You are now inside this room.');
    }

    public function start(GameRound $game)
    {
        if ($game->status === 'open') {
            return redirect()
                ->route('admin.games.index', ['game_id' => $game->id])
                ->withErrors(['game' => 'This game room is already open.']);
        }

        if (in_array($game->status, ['ended', 'settled'])) {
            return redirect()
                ->route('admin.games.index')
                ->withErrors(['game' => 'This game room is already ended. Please create or choose another room.']);
        }

        $game->update([
            'status' => 'open',
        ]);

        return redirect()
            ->route('admin.games.index', ['game_id' => $game->id])
            ->with('success', 'Betting is now open for this room.');
    }

    public function close(GameRound $game)
    {
        if ($game->status !== 'open') {
            return redirect()
                ->route('admin.games.index', ['game_id' => $game->id])
                ->withErrors(['game' => 'Only open game rooms can be closed.']);
        }

        $game->update([
            'status' => 'closed',
        ]);

        return redirect()
            ->route('admin.games.index', ['game_id' => $game->id])
            ->with('success', 'Betting has been closed for this room.');
    }

    public function end(GameRound $game)
    {
        if (!in_array($game->status, ['open', 'closed'])) {
            return redirect()
                ->route('admin.games.index', ['game_id' => $game->id])
                ->withErrors(['game' => 'Only open or closed game rooms can be ended.']);
        }

        $game->update([
            'status' => 'ended',
        ]);

        return redirect()
            ->route('admin.games.index', ['game_id' => $game->id])
            ->with('success', 'Game ended. You may now declare the result.');
    }

    public function declare(Request $request, GameRound $game)
    {
        $data = $request->validate([
            'winning_side' => ['required', 'in:meron,wala,draw,cancelled'],
        ]);

        if (!in_array($game->status, ['open', 'closed', 'ended'])) {
            return redirect()
                ->route('admin.games.index')
                ->withErrors(['game' => 'This game room cannot be declared.']);
        }

        return DB::transaction(function () use ($game, $data) {
            $game = GameRound::whereKey($game->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($game->status === 'settled') {
                return redirect()
                    ->route('admin.games.index')
                    ->withErrors(['game' => 'This game room is already settled.']);
            }

            if ($data['winning_side'] === 'cancelled') {
                $this->cancelGame($game);

                return redirect()
                    ->route('admin.games.index')
                    ->with('success', 'Game room cancelled and hidden from active room list.');
            }

            $this->settleGame($game, $data['winning_side']);

            return redirect()
                ->route('admin.games.index')
                ->with('success', 'Game result declared successfully. Room is now hidden from active room list.');
        });
    }

    private function cancelGame(GameRound $game): void
    {
        $bets = GameBet::where('game_round_id', $game->id)
            ->where('status', 'pending')
            ->lockForUpdate()
            ->get();

        foreach ($bets as $bet) {
            $player = User::whereKey($bet->user_id)
                ->lockForUpdate()
                ->first();

            if (!$player) {
                continue;
            }

            $balanceBefore = (float) $player->wallet_balance;
            $balanceAfter = $balanceBefore + (float) $bet->amount;

            $player->update([
                'wallet_balance' => $balanceAfter,
            ]);

            $bet->update([
                'status' => 'refunded',
                'payout_amount' => (float) $bet->amount,
            ]);

            WalletTransaction::create([
                'user_id' => $player->id,
                'admin_id' => auth()->id(),
                'type' => 'refund',
                'direction' => 'credit',
                'amount' => (float) $bet->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => GameBet::class,
                'reference_id' => $bet->id,
                'description' => 'Bet refunded because game room was cancelled.',
            ]);
        }

        $game->update($this->onlyExistingColumns('game_rounds', [
            'winning_side' => 'cancelled',
            'status' => 'settled',
        ]));

        $this->createLogrohan($game, 'cancelled');
    }

    private function settleGame(GameRound $game, string $winningSide): void
    {
        $game->recalculateTotalsAndOdds();
        $game->refresh();

        $winningOdds = match ($winningSide) {
            'meron' => (float) ($game->meron_odds ?? 0),
            'wala' => (float) ($game->wala_odds ?? 0),
            'draw' => (float) ($game->draw_odds ?? 0),
        };

        $bets = GameBet::where('game_round_id', $game->id)
            ->where('status', 'pending')
            ->lockForUpdate()
            ->get();

        $totalPayout = 0;
        $totalCommission = 0;
        $companyCommission = 0;
        $agentCommission = 0;

        foreach ($bets as $bet) {
            $player = User::whereKey($bet->user_id)
                ->lockForUpdate()
                ->first();

            if (!$player) {
                continue;
            }

            if ($bet->side === $winningSide) {
                $grossPayout = round((float) $bet->amount * $winningOdds, 2);
                $commissionAmount = round($grossPayout * 0.05, 2);
                $companyAmount = round($grossPayout * 0.03, 2);
                $agentAmount = round($grossPayout * 0.02, 2);
                $netPayout = round($grossPayout - $commissionAmount, 2);

                $balanceBefore = (float) $player->wallet_balance;
                $balanceAfter = $balanceBefore + $netPayout;

                $player->update([
                    'wallet_balance' => $balanceAfter,
                ]);

                $bet->update([
                    'status' => 'won',
                    'payout_amount' => $netPayout,
                ]);

                WalletTransaction::create([
                    'user_id' => $player->id,
                    'admin_id' => auth()->id(),
                    'type' => 'payout',
                    'direction' => 'credit',
                    'amount' => $netPayout,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => GameBet::class,
                    'reference_id' => $bet->id,
                    'description' => 'Winning payout for ' . strtoupper($winningSide) . ' less 5% commission.',
                ]);

                $this->creditAgentCommission($player, $bet, $agentAmount);

                $totalPayout += $netPayout;
                $totalCommission += $commissionAmount;
                $companyCommission += $companyAmount;
                $agentCommission += $agentAmount;
            } else {
                $bet->update([
                    'status' => 'lost',
                    'payout_amount' => 0,
                ]);
            }
        }

        $game->update($this->onlyExistingColumns('game_rounds', [
            'winning_side' => $winningSide,
            'status' => 'settled',
            'payout_total' => round($totalPayout, 2),
            'commission_rate' => 5,
            'commission_amount' => round($totalCommission, 2),
            'company_commission_rate' => 3,
            'agent_commission_rate' => 2,
            'company_commission_amount' => round($companyCommission, 2),
            'agent_commission_amount' => round($agentCommission, 2),
            'admin_income' => round($companyCommission, 2),
        ]));

        $this->createLogrohan($game, $winningSide);
    }

    private function creditAgentCommission(User $player, GameBet $bet, float $agentAmount): void
    {
        if (!$player->agent_id || $agentAmount <= 0) {
            return;
        }

        $agent = User::whereKey($player->agent_id)
            ->lockForUpdate()
            ->first();

        if (!$agent) {
            return;
        }

        $before = (float) ($agent->commission_balance ?? 0);
        $after = $before + $agentAmount;

        $agent->update([
            'commission_balance' => $after,
        ]);

        if (class_exists(\App\Models\CommissionTransaction::class)) {
            \App\Models\CommissionTransaction::create([
                'agent_id' => $agent->id,
                'player_id' => $player->id,
                'game_round_id' => $bet->game_round_id,
                'game_bet_id' => $bet->id,
                'type' => 'player_bet_commission',
                'direction' => 'credit',
                'amount' => $agentAmount,
                'balance_before' => $before,
                'balance_after' => $after,
                'description' => '2% agent commission from winning payout.',
            ]);
        }
    }

    private function createLogrohan(GameRound $game, string $result): void
    {
        if (LogrohanEntry::where('game_round_id', $game->id)->exists()) {
            return;
        }

        LogrohanEntry::create($this->onlyExistingColumns('logrohan_entries', [
            'game_round_id' => $game->id,
            'round_number' => $game->round_code ?? $game->round_number ?? $game->id,
            'round_code' => $game->round_code ?? $game->round_number ?? $game->id,
            'result' => $result,
            'winning_side' => $result,
        ]));
    }

    private function visibleAdminRooms()
    {
        return GameRound::whereIn('status', ['waiting', 'open', 'closed'])
            ->latest('id')
            ->take(50)
            ->get();
    }

    private function onlyExistingColumns(string $table, array $payload): array
    {
        if (!Schema::hasTable($table)) {
            return $payload;
        }

        $columns = Schema::getColumnListing($table);

        return collect($payload)
            ->only($columns)
            ->toArray();
    }
}