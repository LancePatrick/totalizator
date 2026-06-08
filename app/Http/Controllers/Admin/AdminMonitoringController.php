<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameBet;
use App\Models\GameRound;
use App\Models\MoneyRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Support\PaginationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminMonitoringController extends Controller
{
    public function overview(Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $moneyRequests = $this->dateFilter(MoneyRequest::query(), $dateFrom, $dateTo);
        $withdrawals = $this->dateFilter(WithdrawalRequest::query(), $dateFrom, $dateTo);
        $bets = $this->dateFilter(GameBet::query(), $dateFrom, $dateTo);
        $games = $this->dateFilter(GameRound::query(), $dateFrom, $dateTo);
        $walletTransactions = $this->dateFilter(WalletTransaction::query(), $dateFrom, $dateTo);

        $totalLoading = (float) (clone $moneyRequests)
            ->where('status', 'approved')
            ->sum('amount');

        $totalWithdrawal = (float) (clone $withdrawals)
            ->where('status', 'approved')
            ->sum('amount');

        $totalBets = (float) (clone $bets)->sum('amount');

        $totalDrawBet = (float) (clone $bets)
            ->where('side', 'draw')
            ->sum('amount');

        $totalDrawWin = $this->drawWinAmount($dateFrom, $dateTo);

        /*
        |--------------------------------------------------------------------------
        | Commission Computation
        |--------------------------------------------------------------------------
        | Total commission = 5%
        | Company commission = 3%
        | Agent commission = 2%
        |--------------------------------------------------------------------------
        */

        $totalCommission = (float) (clone $games)->sum('commission_amount');

        if ($totalCommission <= 0) {
            $totalCommission = round($totalBets * 0.05, 2);
        }

        $companyCommission = (float) (clone $games)->sum('company_commission_amount');

        if ($companyCommission <= 0) {
            $companyCommission = round($totalBets * 0.03, 2);
        }

        $agentCommission = (float) (clone $games)->sum('agent_commission_amount');

        if ($agentCommission <= 0) {
            $agentCommission = round($totalBets * 0.02, 2);
        }

        /*
        |--------------------------------------------------------------------------
        | Commission Converted To Load
        |--------------------------------------------------------------------------
        | Agent commission converted to normal load wallet.
        | Usually this is CREDIT to wallet_transactions.
        |--------------------------------------------------------------------------
        */

        $totalConvertCommission = (float) (clone $walletTransactions)
            ->whereIn('type', [
                'commission_convert',
                'commission_converted',
                'agent_commission_convert',
                'agent_commission_converted',
                'convert_commission',
                'converted_commission',
                'commission_to_load',
            ])
            ->where('direction', 'credit')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Commission Cashout / Commission Withdrawal
        |--------------------------------------------------------------------------
        | Usually this is DEBIT from commission balance.
        |--------------------------------------------------------------------------
        */

        $totalCommissionCashOut = (float) (clone $walletTransactions)
            ->whereIn('type', [
                'commission_cashout',
                'agent_commission_cashout',
                'cashout_commission',
                'commission_withdrawal',
                'agent_commission_withdrawal',
            ])
            ->where('direction', 'debit')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Initial Wallet
        |--------------------------------------------------------------------------
        | If date_from is selected, this computes wallet at the start of that day.
        | Formula:
        | current wallet - transactions from selected day until now.
        |
        | If no date_from, it uses initial_wallet_balance if column exists.
        |--------------------------------------------------------------------------
        */

        $actualWallet = (float) User::query()->sum('wallet_balance');
        $initialWallet = $this->initialWalletAmount($dateFrom);

        $totalPayout = (float) (clone $walletTransactions)
            ->whereIn('type', ['payout', 'refund'])
            ->where('direction', 'credit')
            ->sum('amount');

        $mustTotalWallet = round(
            $initialWallet
            + $totalLoading
            - $totalWithdrawal
            - $totalBets
            + $totalPayout
            + $totalConvertCommission,
            2
        );

        $walletDifference = round($mustTotalWallet - $actualWallet, 2);

        $cards = [
            [
                'label' => 'Total Loading',
                'value' => $totalLoading,
                'sub' => 'Approved wallet loading',
                'tone' => 'blue',
            ],
            [
                'label' => 'Total Withdrawal',
                'value' => $totalWithdrawal,
                'sub' => 'Approved withdrawals',
                'tone' => 'red',
            ],
            [
                'label' => 'Total Convert Commission',
                'value' => $totalConvertCommission,
                'sub' => 'Commission converted to load',
                'tone' => 'purple',
            ],
            [
                'label' => 'Commission Cashout',
                'value' => $totalCommissionCashOut,
                'sub' => 'Commission withdrawn as cash',
                'tone' => 'red',
            ],
            [
                'label' => 'Total Bets',
                'value' => $totalBets,
                'sub' => 'All player bets',
                'tone' => 'white',
            ],
            [
                'label' => 'Total Commission',
                'value' => $totalCommission,
                'sub' => 'Total 5% commission',
                'tone' => 'green',
            ],
            [
                'label' => 'Total Agent Commission',
                'value' => $agentCommission,
                'sub' => 'Agent commission 2%',
                'tone' => 'yellow',
            ],
            [
                'label' => 'Company Commission 3%',
                'value' => $companyCommission,
                'sub' => 'Company share',
                'tone' => 'green',
            ],
            [
                'label' => 'Agent Commission Rate 2%',
                'value' => $agentCommission,
                'sub' => 'Agent share',
                'tone' => 'yellow',
            ],
            [
                'label' => 'Total of 5% Commission',
                'value' => $companyCommission + $agentCommission,
                'sub' => 'Company + agent',
                'tone' => 'green',
            ],
            [
                'label' => 'Total Draw Bet',
                'value' => $totalDrawBet,
                'sub' => 'Draw side total bets',
                'tone' => 'purple',
            ],
            [
                'label' => 'Total Draw Win',
                'value' => $totalDrawWin,
                'sub' => 'Draw payout amount',
                'tone' => 'blue',
            ],
            [
                'label' => 'Initial Wallet',
                'value' => $initialWallet,
                'sub' => $dateFrom ? 'Wallet at start of selected day' : 'Starting wallet balance',
                'tone' => 'white',
            ],
            [
                'label' => 'Actual Wallet',
                'value' => $actualWallet,
                'sub' => 'Current wallet balance',
                'tone' => 'green',
            ],
            [
                'label' => 'Must Total Wallet',
                'value' => $mustTotalWallet,
                'sub' => 'Computed expected wallet',
                'tone' => 'yellow',
            ],
            [
                'label' => 'Wallet Difference',
                'value' => $walletDifference,
                'sub' => 'Must wallet minus actual',
                'tone' => $walletDifference == 0 ? 'green' : 'red',
            ],
        ];

        return view('admin.monitoring.overview', [
            'cards' => $cards,
            'daily' => $this->dailyOverview($dateFrom, $dateTo),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function activityLogs(Request $request)
    {
        $query = WalletTransaction::with('user')->latest();

        $query = $this->dateFilter($query, $request->date_from, $request->date_to);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return view('admin.monitoring.activity-logs', [
            'logs' => $query->paginate(30)->withQueryString(),
        ]);
    }

    public function agentHierarchy(Request $request)
    {
        $playerColumns = [
            'id',
            'name',
            'email',
            'agent_id',
            'wallet_balance',
            'is_active',
            'created_at',
        ];

        if (Schema::hasColumn('users', 'phone')) {
            $playerColumns[] = 'phone';
        }

        $agents = User::query()
            ->where('role', 'agent')
            ->with([
                'players' => function ($query) use ($playerColumns) {
                    $query->select($playerColumns)
                        ->where('role', 'player')
                        ->orderBy('name');
                },
            ])
            ->withCount([
                'players as total_players_count',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('agent_code', 'like', "%{$search}%")
                        ->orWhereHas('players', function ($playerQuery) use ($search) {
                            $playerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");

                            if (Schema::hasColumn('users', 'phone')) {
                                $playerQuery->orWhere('phone', 'like', "%{$search}%");
                            }
                        });
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $agents->getCollection()->transform(function ($agent) use ($request) {
            $playerIds = $agent->players->pluck('id');

            $playerBetTotals = collect();

            if ($playerIds->isNotEmpty()) {
                $betsQuery = GameBet::query()
                    ->selectRaw('user_id, SUM(amount) as total_bets')
                    ->whereIn('user_id', $playerIds)
                    ->groupBy('user_id');

                $betsQuery = $this->dateFilter($betsQuery, $request->date_from, $request->date_to);

                $playerBetTotals = $betsQuery->pluck('total_bets', 'user_id');
            }

            $agentTotalBets = 0;

            $agent->players->transform(function ($player) use ($playerBetTotals, &$agentTotalBets) {
                $totalBet = (float) ($playerBetTotals[$player->id] ?? 0);

                $player->total_bets = $totalBet;
                $player->agent_commission_amount = round($totalBet * 0.02, 2);

                $agentTotalBets += $totalBet;

                return $player;
            });

            $agent->total_player_bets = $agentTotalBets;
            $agent->agent_commission_rate = 2;
            $agent->agent_commission_amount = round($agentTotalBets * 0.02, 2);

            return $agent;
        });

        return view('admin.monitoring.agent-hierarchy', [
            'agents' => $agents,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
        ]);
    }

    public function agentReports(Request $request)
    {
        $collection = $this->agentReportCollection($request);
        $agents = PaginationHelper::paginateCollection($collection, 20);

        return view('admin.monitoring.agent-reports', [
            'agents' => $agents,
            'totalAgentCommission' => $collection->sum('computed_agent_commission'),
            'totalPlayerBets' => $collection->sum('total_player_bets'),
        ]);
    }

    public function exportAgentReports(Request $request)
    {
        $agents = $this->agentReportCollection($request);

        $filename = 'agent-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($agents) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Agent Name',
                'Email',
                'Agent Code',
                'Players',
                'Wallet Balance',
                'Commission Balance',
                'Total Player Bets',
                'Agent Commission 2%',
                'Status',
            ]);

            foreach ($agents as $agent) {
                fputcsv($handle, [
                    $agent->name,
                    $agent->email,
                    $agent->agent_code,
                    $agent->total_players_count,
                    $agent->wallet_balance,
                    $agent->commission_balance ?? 0,
                    $agent->total_player_bets,
                    $agent->computed_agent_commission,
                    $agent->is_active ? 'Active' : 'Inactive',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function agentReportCollection(Request $request)
    {
        return User::query()
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
            ->get()
            ->map(function ($agent) use ($request) {
                $playerIds = User::where('agent_id', $agent->id)
                    ->where('role', 'player')
                    ->pluck('id');

                $betsQuery = GameBet::whereIn('user_id', $playerIds);
                $betsQuery = $this->dateFilter($betsQuery, $request->date_from, $request->date_to);

                $totalPlayerBets = (float) $betsQuery->sum('amount');

                $agent->total_player_bets = $totalPlayerBets;

                /*
                |--------------------------------------------------------------------------
                | Display Computed Agent Commission
                |--------------------------------------------------------------------------
                | If actual commission_balance exists and has value, we still show computed 2%.
                | The actual balance is shown in views as commission_balance.
                |--------------------------------------------------------------------------
                */

                $agent->computed_agent_commission = round($totalPlayerBets * 0.02, 2);

                return $agent;
            })
            ->sortByDesc('computed_agent_commission')
            ->values();
    }

    private function dailyOverview(?string $dateFrom, ?string $dateTo)
    {
        return $this->dateFilter(GameRound::query(), $dateFrom, $dateTo)
            ->get()
            ->groupBy(fn ($game) => optional($game->created_at)->format('Y-m-d') ?? 'No Date')
            ->map(function ($items, $date) {
                $totalPool = $items->sum(fn ($game) => (float) ($game->total_pool ?? 0));
                $totalBets = $items->sum(function ($game) {
                    return (float) ($game->meron_total ?? 0)
                        + (float) ($game->wala_total ?? 0)
                        + (float) ($game->draw_total ?? 0);
                });

                $commission = $items->sum(fn ($game) => (float) ($game->commission_amount ?? 0));

                if ($commission <= 0) {
                    $commission = round($totalBets * 0.05, 2);
                }

                $companyCommission = $items->sum(fn ($game) => (float) ($game->company_commission_amount ?? 0));

                if ($companyCommission <= 0) {
                    $companyCommission = round($totalBets * 0.03, 2);
                }

                $agentCommission = $items->sum(fn ($game) => (float) ($game->agent_commission_amount ?? 0));

                if ($agentCommission <= 0) {
                    $agentCommission = round($totalBets * 0.02, 2);
                }

                return [
                    'date' => $date,
                    'games' => $items->count(),
                    'total_pool' => $totalPool,
                    'company_commission' => $companyCommission,
                    'agent_commission' => $agentCommission,
                    'total_commission' => $commission,
                    'net_pool' => $items->sum(fn ($game) => (float) ($game->net_pool ?? 0)),
                    'payout_total' => $items->sum(fn ($game) => (float) ($game->payout_total ?? 0)),
                ];
            })
            ->sortByDesc('date')
            ->values();
    }

    private function drawWinAmount(?string $dateFrom, ?string $dateTo): float
    {
        $drawGames = $this->dateFilter(GameRound::query(), $dateFrom, $dateTo)
            ->where('winning_side', 'draw')
            ->pluck('id');

        if ($drawGames->isEmpty()) {
            return 0;
        }

        $betIds = GameBet::whereIn('game_round_id', $drawGames)
            ->where('side', 'draw')
            ->pluck('id');

        if ($betIds->isEmpty()) {
            return 0;
        }

        $query = WalletTransaction::query()
            ->whereIn('type', ['payout', 'refund'])
            ->where('direction', 'credit');

        if (Schema::hasColumn('wallet_transactions', 'reference_id')) {
            $query->whereIn('reference_id', $betIds);
        }

        return (float) $query->sum('amount');
    }

    private function initialWalletAmount(?string $dateFrom): float
    {
        /*
        |--------------------------------------------------------------------------
        | No Date Filter
        |--------------------------------------------------------------------------
        | Use initial_wallet_balance column if available.
        |--------------------------------------------------------------------------
        */

        if (!$dateFrom) {
            if (Schema::hasColumn('users', 'initial_wallet_balance')) {
                return (float) User::query()->sum('initial_wallet_balance');
            }

            return (float) User::query()->sum('wallet_balance');
        }

        /*
        |--------------------------------------------------------------------------
        | With Date Filter
        |--------------------------------------------------------------------------
        | Wallet at start of date_from:
        | current balance - all wallet movement from date_from 00:00:00 until now.
        |--------------------------------------------------------------------------
        */

        $actualWallet = (float) User::query()->sum('wallet_balance');

        $transactionsFromDate = WalletTransaction::query()
            ->whereDate('created_at', '>=', $dateFrom)
            ->get();

        $netMovement = $transactionsFromDate->sum(function ($transaction) {
            $amount = (float) $transaction->amount;

            if ($transaction->direction === 'credit') {
                return $amount;
            }

            if ($transaction->direction === 'debit') {
                return -$amount;
            }

            return 0;
        });

        return round($actualWallet - $netMovement, 2);
    }

    private function dateFilter($query, ?string $dateFrom, ?string $dateTo)
    {
        return $query
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            });
    }
}