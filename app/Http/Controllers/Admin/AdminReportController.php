<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameBet;
use App\Models\GameRound;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function games(Request $request)
    {
        $gamesQuery = $this->gameReportQuery($request);

        $games = (clone $gamesQuery)
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $allGames = (clone $gamesQuery)->get();

        $totalGames = $allGames->count();

        $meronTotal = 0;
        $walaTotal = 0;
        $drawTotal = 0;

        $totalPool = 0;
        $commissionEarned = 0;
        $netPool = 0;
        $payoutTotal = 0;
        $adminIncome = 0;

        foreach ($allGames as $game) {
            $computed = $this->computeGameAmounts($game);

            $meronTotal += $computed['meron_total'];
            $walaTotal += $computed['wala_total'];
            $drawTotal += $computed['draw_total'];

            $totalPool += $computed['total_pool'];
            $commissionEarned += $computed['commission_amount'];
            $netPool += $computed['net_pool'];
            $payoutTotal += $computed['payout_total'];
            $adminIncome += $computed['admin_income'];

            /*
            |--------------------------------------------------------------------------
            | Attach computed values to each game
            |--------------------------------------------------------------------------
            | This makes your existing blade display correct values even if the
            | database columns are still 0.
            */
            $game->meron_total = $computed['meron_total'];
            $game->wala_total = $computed['wala_total'];
            $game->draw_total = $computed['draw_total'];
            $game->total_pool = $computed['total_pool'];
            $game->commission_amount = $computed['commission_amount'];
            $game->net_pool = $computed['net_pool'];
            $game->payout_total = $computed['payout_total'];
            $game->admin_income = $computed['admin_income'];
        }

        /*
        |--------------------------------------------------------------------------
        | Also update paginated games with computed values
        |--------------------------------------------------------------------------
        */
        $games->getCollection()->transform(function ($game) {
            $computed = $this->computeGameAmounts($game);

            $game->meron_total = $computed['meron_total'];
            $game->wala_total = $computed['wala_total'];
            $game->draw_total = $computed['draw_total'];
            $game->total_pool = $computed['total_pool'];
            $game->commission_amount = $computed['commission_amount'];
            $game->net_pool = $computed['net_pool'];
            $game->payout_total = $computed['payout_total'];
            $game->admin_income = $computed['admin_income'];

            return $game;
        });

        $averageIncomePerGame = $totalGames > 0
            ? round($adminIncome / $totalGames, 2)
            : 0;

        $dailyEarnings = $allGames
            ->groupBy(function ($game) {
                return optional($game->created_at)->format('Y-m-d') ?? 'No Date';
            })
            ->map(function ($items, $date) {
                $gamesCount = $items->count();

                $totalPool = 0;
                $commission = 0;
                $netPool = 0;
                $payoutTotal = 0;
                $adminIncome = 0;

                foreach ($items as $game) {
                    $computed = $this->computeGameAmounts($game);

                    $totalPool += $computed['total_pool'];
                    $commission += $computed['commission_amount'];
                    $netPool += $computed['net_pool'];
                    $payoutTotal += $computed['payout_total'];
                    $adminIncome += $computed['admin_income'];
                }

                return [
                    'date' => $date,
                    'games' => $gamesCount,
                    'total_pool' => round($totalPool, 2),
                    'commission' => round($commission, 2),
                    'net_pool' => round($netPool, 2),
                    'payout_total' => round($payoutTotal, 2),
                    'admin_income' => round($adminIncome, 2),
                ];
            })
            ->sortByDesc('date')
            ->values();

        return view('admin.reports.games', [
            'games' => $games,

            /*
            |--------------------------------------------------------------------------
            | These variable names match your Blade
            |--------------------------------------------------------------------------
            */
            'totalGames' => $totalGames,
            'totalPool' => round($totalPool, 2),
            'commissionEarned' => round($commissionEarned, 2),
            'netPool' => round($netPool, 2),
            'payoutTotal' => round($payoutTotal, 2),
            'adminIncome' => round($adminIncome, 2),
            'averageIncomePerGame' => round($averageIncomePerGame, 2),

            'meronTotal' => round($meronTotal, 2),
            'walaTotal' => round($walaTotal, 2),
            'drawTotal' => round($drawTotal, 2),

            'dailyEarnings' => $dailyEarnings,

            /*
            |--------------------------------------------------------------------------
            | Extra aliases para hindi mag-error kung may ibang blade variable
            |--------------------------------------------------------------------------
            */
            'commissionTotal' => round($commissionEarned, 2),
            'netPoolTotal' => round($netPool, 2),
            'averageIncome' => round($averageIncomePerGame, 2),
            'earningsPerDay' => $dailyEarnings,
        ]);
    }

    public function wallet(Request $request)
    {
        $transactions = WalletTransaction::query()
            ->with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

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

        $totalCredit = (float) (clone $transactions)->where('direction', 'credit')->sum('amount');
        $totalDebit = (float) (clone $transactions)->where('direction', 'debit')->sum('amount');

        return view('admin.reports.wallet', [
            'transactions' => $transactions->latest()->paginate(30)->withQueryString(),
            'totalCredit' => round($totalCredit, 2),
            'totalDebit' => round($totalDebit, 2),
            'netMovement' => round($totalCredit - $totalDebit, 2),
        ]);
    }

    public function exportGames(Request $request): StreamedResponse
    {
        $games = $this->gameReportQuery($request)
            ->latest('id')
            ->get();

        $fileName = 'game_reports_' . now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($games) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Round Name',
                'Round Number',
                'Status',
                'Winner',
                'Meron Total',
                'Wala Total',
                'Draw Total',
                'Total Pool',
                'Commission Rate',
                'Commission',
                'Final All Bet / Net Pool',
                'Payout Total',
                'Admin Income',
                'Created At',
                'Declared At',
            ]);

            foreach ($games as $game) {
                $computed = $this->computeGameAmounts($game);

                fputcsv($handle, [
                    $game->id,
                    $game->round_name ?? $game->title ?? 'Game Room',
                    $game->round_number ?? $game->round_code ?? $game->id,
                    $game->status,
                    $game->winning_side,
                    $computed['meron_total'],
                    $computed['wala_total'],
                    $computed['draw_total'],
                    $computed['total_pool'],
                    $game->commission_rate ?? 5,
                    $computed['commission_amount'],
                    $computed['net_pool'],
                    $computed['payout_total'],
                    $computed['admin_income'],
                    optional($game->created_at)->format('Y-m-d H:i:s'),
                    optional($game->settled_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName);
    }

    public function exportWallet(Request $request): StreamedResponse
    {
        $transactions = WalletTransaction::query()
            ->with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

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
            })
            ->latest()
            ->get();

        $fileName = 'wallet_reports_' . now()->format('Y_m_d_His') . '.csv';

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
                'Date',
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction->id,
                    $transaction->user?->name ?? 'N/A',
                    $transaction->user?->email ?? 'N/A',
                    $transaction->type,
                    $transaction->direction,
                    $transaction->amount,
                    $transaction->balance_before,
                    $transaction->balance_after,
                    $transaction->description,
                    optional($transaction->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName);
    }

    private function gameReportQuery(Request $request)
    {
        return GameRound::query()
            ->withCount('bets')
            ->withSum('bets as bets_total_amount', 'amount')
            ->where(function ($query) {
                /*
                |--------------------------------------------------------------------------
                | New logic:
                |--------------------------------------------------------------------------
                | Since declared rooms stay status=open, reports must include:
                | - games with winning_side
                | - games with bets
                */
                $query->whereNotNull('winning_side')
                    ->orWhereHas('bets');
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('id', $search)
                        ->orWhere('round_name', 'like', "%{$search}%")
                        ->orWhere('round_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('round_code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status !== 'all') {
                    $query->where('status', $request->status);
                }
            })
            ->when($request->filled('winner'), function ($query) use ($request) {
                if ($request->winner !== 'all') {
                    $query->where('winning_side', $request->winner);
                }
            })
            ->when($request->filled('winning_side'), function ($query) use ($request) {
                if ($request->winning_side !== 'all') {
                    $query->where('winning_side', $request->winning_side);
                }
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            });
    }

    private function computeGameAmounts(GameRound $game): array
    {
        $betsQuery = GameBet::where('game_round_id', $game->id);

        /*
        |--------------------------------------------------------------------------
        | Get totals from actual bets first
        |--------------------------------------------------------------------------
        */
        $meronTotal = (float) (clone $betsQuery)
            ->where('side', 'meron')
            ->sum('amount');

        $walaTotal = (float) (clone $betsQuery)
            ->where('side', 'wala')
            ->sum('amount');

        $drawTotal = (float) (clone $betsQuery)
            ->where('side', 'draw')
            ->sum('amount');

        $betsTotal = $meronTotal + $walaTotal + $drawTotal;

        /*
        |--------------------------------------------------------------------------
        | Fallback to game_rounds totals if no bet records found
        |--------------------------------------------------------------------------
        */
        $gameMeron = (float) ($game->meron_total ?? 0);
        $gameWala = (float) ($game->wala_total ?? 0);
        $gameDraw = (float) ($game->draw_total ?? 0);

        if ($betsTotal <= 0 && ($gameMeron + $gameWala + $gameDraw) > 0) {
            $meronTotal = $gameMeron;
            $walaTotal = $gameWala;
            $drawTotal = $gameDraw;
            $betsTotal = $meronTotal + $walaTotal + $drawTotal;
        }

        $totalPool = (float) ($game->total_pool ?? 0);

        if ($totalPool <= 0) {
            $totalPool = $betsTotal;
        }

        /*
        |--------------------------------------------------------------------------
        | Commission rate
        |--------------------------------------------------------------------------
        */
        $commissionRate = (float) ($game->commission_rate ?? 5);

        if ($commissionRate > 1) {
            $commissionRate = $commissionRate / 100;
        }

        $commissionAmount = (float) ($game->commission_amount ?? 0);

        if ($commissionAmount <= 0 && $totalPool > 0) {
            $commissionAmount = round($totalPool * $commissionRate, 2);
        }

        /*
        |--------------------------------------------------------------------------
        | Net pool / Final all bet
        |--------------------------------------------------------------------------
        */
        $netPool = (float) ($game->net_pool ?? 0);

        if ($netPool <= 0 && $totalPool > 0) {
            $netPool = round($totalPool - $commissionAmount, 2);
        }

        /*
        |--------------------------------------------------------------------------
        | Payout total
        |--------------------------------------------------------------------------
        */
        $payoutTotal = (float) ($game->payout_total ?? 0);

        if ($payoutTotal <= 0) {
            $payoutTotal = (float) GameBet::where('game_round_id', $game->id)
                ->whereIn('status', ['won', 'paid', 'refunded'])
                ->sum('payout_amount');
        }

        /*
        |--------------------------------------------------------------------------
        | Admin income
        |--------------------------------------------------------------------------
        */
        $adminIncome = (float) ($game->admin_income ?? 0);

        if ($adminIncome <= 0) {
            $adminIncome = $commissionAmount;
        }

        return [
            'meron_total' => round($meronTotal, 2),
            'wala_total' => round($walaTotal, 2),
            'draw_total' => round($drawTotal, 2),
            'total_pool' => round($totalPool, 2),
            'commission_amount' => round($commissionAmount, 2),
            'net_pool' => round($netPool, 2),
            'payout_total' => round($payoutTotal, 2),
            'admin_income' => round($adminIncome, 2),
        ];
    }
}