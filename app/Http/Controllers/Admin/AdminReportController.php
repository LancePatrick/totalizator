<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameRound;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function games(Request $request)
    {
        $gamesQuery = $this->filteredGames($request);

        $games = (clone $gamesQuery)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summaryGames = (clone $gamesQuery)->get();

        $totalGames = $summaryGames->count();

        $totalPool = $summaryGames->sum(fn ($game) => (float) ($game->total_pool ?? 0));
        $netPool = $summaryGames->sum(fn ($game) => (float) ($game->net_pool ?? 0));

        $commissionEarned = $summaryGames->sum(function ($game) {
            return $this->commissionAmount($game);
        });

        $adminIncome = $summaryGames->sum(function ($game) {
            return (float) ($game->admin_income ?? $this->commissionAmount($game));
        });

        $payoutTotal = $summaryGames->sum(fn ($game) => (float) ($game->payout_total ?? 0));

        $meronTotal = $summaryGames->sum(fn ($game) => (float) ($game->meron_total ?? 0));
        $walaTotal = $summaryGames->sum(fn ($game) => (float) ($game->wala_total ?? 0));
        $drawTotal = $summaryGames->sum(fn ($game) => (float) ($game->draw_total ?? 0));

        $averageIncomePerGame = $totalGames > 0
            ? round($adminIncome / $totalGames, 2)
            : 0;

        $dailyEarnings = $summaryGames
            ->groupBy(fn ($game) => optional($game->created_at)->format('Y-m-d') ?? 'No Date')
            ->map(function ($items, $date) {
                $totalPool = $items->sum(fn ($game) => (float) ($game->total_pool ?? 0));
                $netPool = $items->sum(fn ($game) => (float) ($game->net_pool ?? 0));
                $payoutTotal = $items->sum(fn ($game) => (float) ($game->payout_total ?? 0));

                $commission = $items->sum(function ($game) {
                    return $this->commissionAmount($game);
                });

                $adminIncome = $items->sum(function ($game) {
                    return (float) ($game->admin_income ?? $this->commissionAmount($game));
                });

                return [
                    'date' => $date,
                    'games' => $items->count(),
                    'total_pool' => $totalPool,
                    'net_pool' => $netPool,
                    'commission' => $commission,
                    'payout_total' => $payoutTotal,
                    'admin_income' => $adminIncome,
                ];
            })
            ->sortByDesc('date')
            ->values();

        return view('admin.reports.games', [
            'games' => $games,

            'totalGames' => $totalGames,
            'totalPool' => $totalPool,
            'netPool' => $netPool,

            'commissionEarned' => $commissionEarned,
            'adminIncome' => $adminIncome,
            'payoutTotal' => $payoutTotal,
            'averageIncomePerGame' => $averageIncomePerGame,

            'meronTotal' => $meronTotal,
            'walaTotal' => $walaTotal,
            'drawTotal' => $drawTotal,

            'dailyEarnings' => $dailyEarnings,
        ]);
    }

    public function exportGames(Request $request)
    {
        $games = $this->filteredGames($request)
            ->latest()
            ->get();

        $filename = 'game-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($games) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Date',
                'Round Name',
                'Round Number',
                'Status',
                'Winning Side',
                'Meron Total',
                'Wala Total',
                'Draw Total',
                'Total Pool',
                'Commission Rate',
                'Commission / Plasada',
                'Net Pool / Final All Bet',
                'Payout Total',
                'Admin Income',
            ]);

            foreach ($games as $game) {
                $commissionAmount = $this->commissionAmount($game);
                $adminIncome = (float) ($game->admin_income ?? $commissionAmount);

                fputcsv($handle, [
                    optional($game->created_at)->format('Y-m-d H:i:s'),
                    $game->round_name,
                    $game->round_number,
                    $game->status,
                    $game->winning_side,
                    $game->meron_total,
                    $game->wala_total,
                    $game->draw_total,
                    $game->total_pool,
                    $game->commission_rate,
                    $commissionAmount,
                    $game->net_pool,
                    $game->payout_total,
                    $adminIncome,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function wallet(Request $request)
    {
        $transactions = $this->filteredWalletTransactions($request)
            ->with('user')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $summaryQuery = $this->filteredWalletTransactions($request);

        return view('admin.reports.wallet', [
            'transactions' => $transactions,
            'totalCredit' => (clone $summaryQuery)->where('direction', 'credit')->sum('amount'),
            'totalDebit' => (clone $summaryQuery)->where('direction', 'debit')->sum('amount'),
            'totalTransactions' => (clone $summaryQuery)->count(),
        ]);
    }

    public function exportWallet(Request $request)
    {
        $transactions = $this->filteredWalletTransactions($request)
            ->with('user')
            ->latest()
            ->get();

        $filename = 'wallet-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Date',
                'User',
                'Role',
                'Type',
                'Direction',
                'Amount',
                'Balance Before',
                'Balance After',
                'Description',
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    optional($transaction->created_at)->format('Y-m-d H:i:s'),
                    $transaction->user?->name,
                    $transaction->user?->role,
                    $transaction->type,
                    $transaction->direction,
                    $transaction->amount,
                    $transaction->balance_before,
                    $transaction->balance_after,
                    $transaction->description,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function filteredGames(Request $request)
    {
        return GameRound::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('round_name', 'like', "%{$search}%")
                        ->orWhere('round_number', 'like', "%{$search}%");

                    if (is_numeric($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('winning_side'), function ($query) use ($request) {
                $query->where('winning_side', $request->winning_side);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            });
    }

    private function filteredWalletTransactions(Request $request)
    {
        return WalletTransaction::query()
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->filled('direction'), function ($query) use ($request) {
                $query->where('direction', $request->direction);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            });
    }

    private function commissionAmount(GameRound $game): float
    {
        if (!is_null($game->commission_amount)) {
            return (float) $game->commission_amount;
        }

        $totalPool = (float) ($game->total_pool ?? 0);
        $netPool = (float) ($game->net_pool ?? 0);

        if ($totalPool > 0 && $netPool > 0) {
            return max(0, round($totalPool - $netPool, 2));
        }

        $rate = (float) ($game->commission_rate ?? 0);

        if ($rate > 1) {
            $rate = $rate / 100;
        }

        return round($totalPool * $rate, 2);
    }
}