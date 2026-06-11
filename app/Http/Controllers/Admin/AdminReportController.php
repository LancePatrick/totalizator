<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameBet;
use App\Models\GameRound;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function games(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $winner = $request->winner;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $gamesQuery = GameRound::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', $search)
                        ->orWhere('round_name', 'like', "%{$search}%")
                        ->orWhere('round_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('round_code', 'like', "%{$search}%");
                });
            })
            ->when($status && $status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($winner && $winner !== 'all', function ($query) use ($winner) {
                $query->where('winning_side', $winner);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest('id');

        $gamesForTotals = (clone $gamesQuery)->get();

        $gameIds = $gamesForTotals->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | Valid Bets Only
        |--------------------------------------------------------------------------
        | Cancelled/refunded bets should not be counted as real total pool.
        |--------------------------------------------------------------------------
        */

        $validBetsQuery = GameBet::query()
            ->whereIn('game_round_id', $gameIds)
            ->whereNotIn('status', ['refunded', 'cancelled'])
            ->whereNotIn('game_round_id', function ($query) {
                $query->select('id')
                    ->from('game_rounds')
                    ->where('winning_side', 'cancelled');
            });

        $totalPool = (float) (clone $validBetsQuery)->sum('amount');

        $meronTotal = (float) (clone $validBetsQuery)
            ->where('side', 'meron')
            ->sum('amount');

        $walaTotal = (float) (clone $validBetsQuery)
            ->where('side', 'wala')
            ->sum('amount');

        $drawTotal = (float) (clone $validBetsQuery)
            ->where('side', 'draw')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Valid Games Only For Earnings
        |--------------------------------------------------------------------------
        | Cancelled games should not add commission, admin income, or payout.
        |--------------------------------------------------------------------------
        */

        $validGamesForTotals = $gamesForTotals
            ->filter(function ($game) {
                return $game->winning_side !== 'cancelled';
            });

        $totalGames = $validGamesForTotals->count();

        $commissionEarned = $this->sumGameColumn($validGamesForTotals, 'commission_amount');

        if ($commissionEarned <= 0 && $totalPool > 0) {
            $commissionEarned = round($totalPool * 0.05, 2);
        }

        $adminIncome = $this->sumGameColumn($validGamesForTotals, 'admin_income');

        if ($adminIncome <= 0) {
            $adminIncome = $commissionEarned;
        }

        $netPool = $totalPool > 0
            ? round($totalPool - $commissionEarned, 2)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Payout Total
        |--------------------------------------------------------------------------
        | Only real winners. Refunds from cancelled games are not report earnings.
        |--------------------------------------------------------------------------
        */

        $payoutTotal = (float) GameBet::query()
            ->whereIn('game_round_id', $gameIds)
            ->whereIn('status', ['won', 'paid'])
            ->whereNotIn('game_round_id', function ($query) {
                $query->select('id')
                    ->from('game_rounds')
                    ->where('winning_side', 'cancelled');
            })
            ->sum('payout_amount');

        $averageIncomePerGame = $totalGames > 0
            ? round($adminIncome / $totalGames, 2)
            : 0;

        $dailyEarnings = $this->buildDailyEarnings($gameIds);

        $games = $gamesQuery
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.games', [
            'games' => $games,

            'totalGames' => $totalGames,
            'totalPool' => $totalPool,
            'commissionEarned' => $commissionEarned,
            'commissionTotal' => $commissionEarned,
            'netPool' => $netPool,
            'netPoolTotal' => $netPool,
            'payoutTotal' => $payoutTotal,
            'adminIncome' => $adminIncome,
            'averageIncomePerGame' => $averageIncomePerGame,
            'averageIncome' => $averageIncomePerGame,

            'meronTotal' => $meronTotal,
            'walaTotal' => $walaTotal,
            'drawTotal' => $drawTotal,

            'dailyEarnings' => $dailyEarnings,
            'earningsPerDay' => $dailyEarnings,

            'search' => $search,
            'status' => $status,
            'winner' => $winner,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function exportGames(Request $request): StreamedResponse
    {
        $search = $request->search;
        $status = $request->status;
        $winner = $request->winner;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $games = GameRound::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', $search)
                        ->orWhere('round_name', 'like', "%{$search}%")
                        ->orWhere('round_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('round_code', 'like', "%{$search}%");
                });
            })
            ->when($status && $status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($winner && $winner !== 'all', function ($query) use ($winner) {
                $query->where('winning_side', $winner);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest('id')
            ->get();

        $fileName = 'game-reports-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($games) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Round Name',
                'Round Number',
                'Status',
                'Winning Side',
                'Total Pool',
                'Commission',
                'Net Pool',
                'Payout Total',
                'Admin Income',
                'Created At',
            ]);

            foreach ($games as $game) {
                $isCancelled = $game->winning_side === 'cancelled';

                fputcsv($handle, [
                    $game->id,
                    $game->round_name ?? $game->title ?? 'Game',
                    $game->round_number ?? $game->round_code ?? $game->id,
                    $game->status,
                    $game->winning_side ?? 'N/A',
                    $isCancelled ? '0.00' : number_format((float) ($game->total_pool ?? 0), 2, '.', ''),
                    $isCancelled ? '0.00' : number_format((float) ($game->commission_amount ?? 0), 2, '.', ''),
                    $isCancelled ? '0.00' : number_format((float) ($game->net_pool ?? 0), 2, '.', ''),
                    $isCancelled ? '0.00' : number_format((float) ($game->payout_total ?? 0), 2, '.', ''),
                    $isCancelled ? '0.00' : number_format((float) ($game->admin_income ?? 0), 2, '.', ''),
                    optional($game->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName);
    }

    public function wallet(Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $search = $request->search;
        $type = $request->type;
        $direction = $request->direction;

        $transactions = WalletTransaction::with('user')
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($type && $type !== 'all', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($direction && $direction !== 'all', function ($query) use ($direction) {
                $query->where('direction', $direction);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('type', 'like', "%{$search}%")
                        ->orWhere('direction', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $summaryQuery = WalletTransaction::query()
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            });

        $totalCredit = (float) (clone $summaryQuery)
            ->where('direction', 'credit')
            ->sum('amount');

        $totalDebit = (float) (clone $summaryQuery)
            ->where('direction', 'debit')
            ->sum('amount');

        $netMovement = round($totalCredit - $totalDebit, 2);

        return view('admin.reports.wallet', [
            'transactions' => $transactions,
            'walletTransactions' => $transactions,
            'totalCredit' => $totalCredit,
            'totalDebit' => $totalDebit,
            'netMovement' => $netMovement,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search,
            'type' => $type,
            'direction' => $direction,
        ]);
    }

    public function exportWallet(Request $request): StreamedResponse
    {
        $transactions = WalletTransaction::with('user')
            ->latest()
            ->get();

        $fileName = 'wallet-reports-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'User',
                'Email',
                'Type',
                'Direction',
                'Amount',
                'Balance Before',
                'Balance After',
                'Description',
                'Created At',
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction->id,
                    $transaction->user->name ?? 'N/A',
                    $transaction->user->email ?? 'N/A',
                    $transaction->type,
                    $transaction->direction,
                    number_format((float) $transaction->amount, 2, '.', ''),
                    number_format((float) ($transaction->balance_before ?? 0), 2, '.', ''),
                    number_format((float) ($transaction->balance_after ?? 0), 2, '.', ''),
                    $transaction->description,
                    optional($transaction->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName);
    }

    private function buildDailyEarnings($gameIds)
    {
        if ($gameIds->isEmpty()) {
            return collect();
        }

        return GameBet::query()
            ->whereIn('game_round_id', $gameIds)
            ->whereNotIn('status', ['refunded', 'cancelled'])
            ->whereNotIn('game_round_id', function ($query) {
                $query->select('id')
                    ->from('game_rounds')
                    ->where('winning_side', 'cancelled');
            })
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(DISTINCT game_round_id) as games')
            ->selectRaw('SUM(amount) as total_pool')
            ->selectRaw('SUM(amount) * 0.05 as commission')
            ->selectRaw('SUM(amount) - (SUM(amount) * 0.05) as net_pool')
            ->selectRaw("SUM(CASE WHEN status IN ('won', 'paid') THEN payout_amount ELSE 0 END) as payout")
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) DESC')
            ->get()
            ->map(function ($day) {
                $day->admin_income = round((float) $day->commission, 2);
                $day->final_all_bet = round((float) $day->net_pool, 2);
                return $day;
            });
    }

    private function sumGameColumn($games, string $column): float
    {
        if (!Schema::hasTable('game_rounds') || !Schema::hasColumn('game_rounds', $column)) {
            return 0;
        }

        return (float) $games->sum(function ($game) use ($column) {
            return (float) ($game->{$column} ?? 0);
        });
    }
}