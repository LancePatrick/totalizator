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
    public function index()
    {
        $currentGame = GameRound::latest()->first();

        if ($currentGame) {
            $currentGame->recalculateTotalsAndOdds();
            $currentGame->refresh();
        }

        return view('admin.games.index', [
            'currentGame' => $currentGame,
            'games' => GameRound::latest()->paginate(10),
            'logrohan' => LogrohanEntry::latest()->take(30)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'round_name' => ['required', 'string', 'max:255'],
            'round_number' => ['nullable', 'string', 'max:50'],
            'video_url' => ['nullable', 'string'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:50'],
        ]);

        GameRound::create([
            'created_by' => auth()->id(),
            'round_name' => $data['round_name'],
            'round_number' => $data['round_number'] ?? null,
            'video_url' => $data['video_url'] ?? null,
            'commission_rate' => $data['commission_rate'],
            'status' => 'waiting',
        ]);

        return back()->with('success', 'Game round created.');
    }

    public function start(GameRound $game)
    {
        $game->update([
            'status' => 'open',
            'started_at' => now(),
        ]);

        return back()->with('success', 'Game started. Betting is now open.');
    }

    public function close(GameRound $game)
    {
        $game->recalculateTotalsAndOdds();

        $game->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Betting closed.');
    }

    public function end(GameRound $game)
    {
        $game->recalculateTotalsAndOdds();

        $game->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return back()->with('success', 'Game ended. You can now declare result.');
    }

    public function declare(Request $request, GameRound $game)
    {
        $data = $request->validate([
            'winning_side' => ['required', 'in:meron,wala,draw,cancelled'],
        ]);

        DB::transaction(function () use ($game, $data) {
            $game = GameRound::whereKey($game->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($game->status === 'settled') {
                throw new \Exception('This game is already settled.');
            }

            $game->recalculateTotalsAndOdds();
            $game->refresh();

            $winningSide = $data['winning_side'];

            $totalPayout = 0;

            if ($winningSide === 'cancelled') {
                $totalPayout = $this->refundAllBets($game);
            } else {
                $totalPayout = $this->payoutWinners($game, $winningSide);
            }

            $game->update([
                'status' => 'settled',
                'winning_side' => $winningSide,
                'payout_total' => $totalPayout,
                'admin_income' => $game->commission_amount,
                'settled_at' => now(),
            ]);

            LogrohanEntry::create([
                'game_round_id' => $game->id,
                'round_number' => $game->round_number,
                'result' => $winningSide,
            ]);
        });

        return back()->with('success', 'Result declared, payouts processed, and commission saved.');
    }

    private function payoutWinners(GameRound $game, string $winningSide): float
    {
        $winningOdds = $game->oddsForSide($winningSide);

        $winningBets = GameBet::with('user')
            ->where('game_round_id', $game->id)
            ->where('side', $winningSide)
            ->get();

        $totalPayout = 0;

        foreach ($winningBets as $bet) {
            $player = User::whereKey($bet->user_id)
                ->lockForUpdate()
                ->first();

            if (!$player) {
                continue;
            }

            $payout = round((float) $bet->amount * $winningOdds, 2);

            $before = (float) $player->wallet_balance;
            $after = $before + $payout;

            $player->update([
                'wallet_balance' => $after,
            ]);

            $this->updateBetAsWon($bet, $payout, $winningOdds);

            WalletTransaction::create([
                'user_id' => $player->id,
                'admin_id' => auth()->id(),
                'type' => 'payout',
                'direction' => 'credit',
                'amount' => $payout,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference_type' => GameBet::class,
                'reference_id' => $bet->id,
                'description' => 'Game payout for winning side: ' . strtoupper($winningSide),
            ]);

            $totalPayout += $payout;
        }

        $losingBets = GameBet::where('game_round_id', $game->id)
            ->where('side', '!=', $winningSide)
            ->get();

        foreach ($losingBets as $bet) {
            $this->updateBetAsLost($bet);
        }

        return round($totalPayout, 2);
    }

    private function refundAllBets(GameRound $game): float
    {
        $bets = GameBet::with('user')
            ->where('game_round_id', $game->id)
            ->get();

        $totalRefund = 0;

        foreach ($bets as $bet) {
            $player = User::whereKey($bet->user_id)
                ->lockForUpdate()
                ->first();

            if (!$player) {
                continue;
            }

            $refund = round((float) $bet->amount, 2);

            $before = (float) $player->wallet_balance;
            $after = $before + $refund;

            $player->update([
                'wallet_balance' => $after,
            ]);

            $this->updateBetAsRefunded($bet, $refund);

            WalletTransaction::create([
                'user_id' => $player->id,
                'admin_id' => auth()->id(),
                'type' => 'refund',
                'direction' => 'credit',
                'amount' => $refund,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference_type' => GameBet::class,
                'reference_id' => $bet->id,
                'description' => 'Game cancelled. Bet refunded.',
            ]);

            $totalRefund += $refund;
        }

        return round($totalRefund, 2);
    }

    private function updateBetAsWon(GameBet $bet, float $payout, float $odds): void
    {
        $data = [];

        if (Schema::hasColumn('game_bets', 'status')) {
            $data['status'] = 'won';
        }

        if (Schema::hasColumn('game_bets', 'result')) {
            $data['result'] = 'won';
        }

        if (Schema::hasColumn('game_bets', 'odds')) {
            $data['odds'] = $odds;
        }

        if (Schema::hasColumn('game_bets', 'payout_amount')) {
            $data['payout_amount'] = $payout;
        }

        if (!empty($data)) {
            $bet->update($data);
        }
    }

    private function updateBetAsLost(GameBet $bet): void
    {
        $data = [];

        if (Schema::hasColumn('game_bets', 'status')) {
            $data['status'] = 'lost';
        }

        if (Schema::hasColumn('game_bets', 'result')) {
            $data['result'] = 'lost';
        }

        if (Schema::hasColumn('game_bets', 'payout_amount')) {
            $data['payout_amount'] = 0;
        }

        if (!empty($data)) {
            $bet->update($data);
        }
    }

    private function updateBetAsRefunded(GameBet $bet, float $refund): void
    {
        $data = [];

        if (Schema::hasColumn('game_bets', 'status')) {
            $data['status'] = 'refunded';
        }

        if (Schema::hasColumn('game_bets', 'result')) {
            $data['result'] = 'refunded';
        }

        if (Schema::hasColumn('game_bets', 'payout_amount')) {
            $data['payout_amount'] = $refund;
        }

        if (!empty($data)) {
            $bet->update($data);
        }
    }
}