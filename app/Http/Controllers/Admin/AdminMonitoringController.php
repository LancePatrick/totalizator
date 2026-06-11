<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameBet;
use App\Models\GameRound;
use App\Models\MoneyRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminMonitoringController extends Controller
{
    public function overview(Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $moneyRequests = $this->dateFilter($this->safeQuery(MoneyRequest::class), $dateFrom, $dateTo);
        $withdrawals = $this->dateFilter($this->safeQuery(WithdrawalRequest::class), $dateFrom, $dateTo);
        $bets = $this->dateFilter($this->safeQuery(GameBet::class), $dateFrom, $dateTo);
        $games = $this->dateFilter($this->safeQuery(GameRound::class), $dateFrom, $dateTo);
        $walletTransactions = $this->dateFilter($this->safeQuery(WalletTransaction::class), $dateFrom, $dateTo);

        $totalLoading = (float) (clone $moneyRequests)
            ->where('status', 'approved')
            ->sum('amount');

        $totalWithdrawal = (float) (clone $withdrawals)
            ->where('status', 'approved')
            ->sum('amount');

        $validBets = (clone $bets)
            ->whereNotIn('status', ['refunded', 'cancelled'])
            ->when(Schema::hasTable('game_rounds'), function ($query) {
                $query->whereNotIn('game_round_id', function ($subQuery) {
                    $subQuery->select('id')
                        ->from('game_rounds')
                        ->where('winning_side', 'cancelled');
                });
            });

        $totalBets = (float) (clone $validBets)->sum('amount');

        $totalDrawBet = (float) (clone $validBets)
            ->where('side', 'draw')
            ->sum('amount');

        $totalDrawWin = (float) (clone $bets)
            ->where('side', 'draw')
            ->whereIn('status', ['won', 'paid'])
            ->when(Schema::hasTable('game_rounds'), function ($query) {
                $query->whereNotIn('game_round_id', function ($subQuery) {
                    $subQuery->select('id')
                        ->from('game_rounds')
                        ->where('winning_side', 'cancelled');
                });
            })
            ->sum('payout_amount');

       $companyCommission = round($totalBets * 0.03, 2);
$agentCommission = round($totalBets * 0.02, 2);
$totalCommission = round($totalBets * 0.05, 2);
$totalFivePercentCommission = round($companyCommission + $agentCommission, 2);

        $totalConvertCommission = (float) (clone $walletTransactions)
            ->whereIn('type', [
                'commission_convert',
                'commission_converted',
                'agent_commission_convert',
                'agent_commission_converted',
                'convert_commission',
                'converted_commission',
                'commission_to_load',
                'commission_converted_to_load',
            ])
            ->where('direction', 'credit')
            ->sum('amount');

        $totalCommissionCashOut = (float) (clone $walletTransactions)
            ->whereIn('type', [
                'commission_cashout',
                'agent_commission_cashout',
                'cashout_commission',
                'commission_withdrawal',
                'agent_commission_withdrawal',
                'commission_withdrawal_request',
            ])
            ->where('direction', 'debit')
            ->sum('amount');

        $actualWallet = Schema::hasTable('users') && Schema::hasColumn('users', 'wallet_balance')
            ? (float) User::query()->sum('wallet_balance')
            : 0;

        $initialWallet = $this->initialWalletAmount($dateFrom);

      $totalPayout = (float) (clone $walletTransactions)
    ->whereIn('type', [
        'payout',
        'refund',
        'bet_refund',
    ])
    ->where('direction', 'credit')
    ->sum('amount');

$totalDrawBet = $totalBets;
$totalDrawWin = round($totalBets - ($totalBets * 0.05), 2);
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
                'sub' => 'All valid player bets',
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
                'value' => $totalFivePercentCommission,
                'sub' => 'Company + agent',
                'tone' => 'green',
            ],
            [
                'label' => 'Total Draw Bet',
                'value' => $totalDrawBet,
                'sub' => 'Draw side valid bets',
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

        $daily = $this->dailyOverview($dateFrom, $dateTo);

        return view('admin.monitoring.overview', [
            'cards' => $cards,
            'daily' => $daily,
            'dailyOverview' => $daily,

            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,

            'totalLoading' => $totalLoading,
            'totalWithdrawal' => $totalWithdrawal,
            'totalConvertCommission' => $totalConvertCommission,
            'totalCommissionCashOut' => $totalCommissionCashOut,
            'totalBets' => $totalBets,
            'totalCommission' => $totalCommission,
            'totalAgentCommission' => $agentCommission,
            'agentCommission' => $agentCommission,
            'companyCommission' => $companyCommission,
            'totalFivePercentCommission' => $totalFivePercentCommission,
            'totalDrawBet' => $totalDrawBet,
            'totalDrawWin' => $totalDrawWin,
            'initialWallet' => $initialWallet,
            'actualWallet' => $actualWallet,
            'mustTotalWallet' => $mustTotalWallet,
            'walletDifference' => $walletDifference,
        ]);
    }

    public function activityLogs(Request $request)
    {
        $query = WalletTransaction::with('user')->latest();

        $query = $this->dateFilter($query, $request->date_from, $request->date_to);

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        if ($request->filled('search')) {
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
        }

        $transactions = $query
            ->paginate(30)
            ->withQueryString();

        return view('admin.monitoring.activity-logs', [
            'logs' => $transactions,
            'transactions' => $transactions,
            'walletTransactions' => $transactions,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
            'search' => $request->search,
        ]);
    }

    public function agentHierarchy(Request $request)
    {
        $agents = User::query()
            ->where('role', 'agent')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $matchingPlayerAgentIds = User::query()
                    ->where('role', 'player')
                    ->where(function ($playerQuery) use ($search) {
                        $playerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->whereNotNull('agent_id')
                    ->pluck('agent_id');

                $query->where(function ($q) use ($search, $matchingPlayerAgentIds) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('agent_code', 'like', "%{$search}%")
                        ->orWhereIn('id', $matchingPlayerAgentIds);
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        foreach ($agents as $agent) {
            $players = User::query()
                ->where('role', 'player')
                ->where('agent_id', $agent->id)
                ->orderBy('name')
                ->get()
                ->map(function ($player) {
                    $playerTotalBets = $this->validBetsForPlayerIds(collect([$player->id]))->sum('amount');

                    $player->total_bets = (float) $playerTotalBets;
                    $player->total_player_bets = (float) $playerTotalBets;
                    $player->agent_commission_amount = round((float) $playerTotalBets * 0.02, 2);

                    return $player;
                });

            $agentTotalBets = (float) $players->sum('total_bets');
            $agentCommission = round($agentTotalBets * 0.02, 2);

            $agent->players = $players;
            $agent->players_count = $players->count();
            $agent->total_players_count = $players->count();

            $agent->players_wallet_sum = Schema::hasColumn('users', 'wallet_balance')
                ? (float) $players->sum('wallet_balance')
                : 0;

            $agent->players_total_bets = $agentTotalBets;
            $agent->total_player_bets = $agentTotalBets;
            $agent->computed_agent_commission = $agentCommission;
            $agent->agent_commission_amount = $agentCommission;

            $agent->wallet_balance = (float) ($agent->wallet_balance ?? 0);
            $agent->commission_balance = (float) ($agent->commission_balance ?? 0);
            $agent->is_active = (bool) ($agent->is_active ?? true);
        }

        return view('admin.monitoring.agent-hierarchy', [
            'agents' => $agents,
            'totalAgents' => method_exists($agents, 'total') ? $agents->total() : $agents->count(),
            'visiblePlayers' => collect($agents->items())->sum('total_players_count'),
            'visibleAgentWallet' => collect($agents->items())->sum('wallet_balance'),
            'visiblePlayerBets' => collect($agents->items())->sum('total_player_bets'),
            'visibleAgentCommission' => collect($agents->items())->sum('computed_agent_commission'),
        ]);
    }

    public function agentReports(Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $agents = User::where('role', 'agent')
            ->latest()
            ->get()
            ->map(function ($agent) use ($dateFrom, $dateTo) {
                $playerIds = User::where('role', 'player')
                    ->where('agent_id', $agent->id)
                    ->pluck('id');

                $validBets = $this->validBetsForPlayerIds($playerIds, $dateFrom, $dateTo);

                $totalBets = (float) $validBets->sum('amount');
                $agentCommission = round($totalBets * 0.02, 2);

                return [
                    'agent' => $agent,
                    'players_count' => $playerIds->count(),
                    'total_bets' => $totalBets,
                    'agent_commission' => $agentCommission,
                ];
            });

        $totalPlayerBets = (float) $agents->sum('total_bets');
        $totalAgentCommission = (float) $agents->sum('agent_commission');

        return view('admin.monitoring.agent-reports', [
            'agents' => $agents,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalPlayerBets' => $totalPlayerBets,
            'totalAgentCommission' => $totalAgentCommission,
        ]);
    }

    public function exportAgentReports(Request $request): StreamedResponse
    {
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $fileName = 'agent-reports-' . now()->format('Y-m-d-His') . '.csv';

        $agents = User::where('role', 'agent')
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($agents, $dateFrom, $dateTo) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Agent',
                'Email',
                'Players Count',
                'Total Bets',
                'Agent Commission',
            ]);

            foreach ($agents as $agent) {
                $playerIds = User::where('role', 'player')
                    ->where('agent_id', $agent->id)
                    ->pluck('id');

                $validBets = $this->validBetsForPlayerIds($playerIds, $dateFrom, $dateTo);

                $totalBets = (float) $validBets->sum('amount');
                $agentCommission = round($totalBets * 0.02, 2);

                fputcsv($handle, [
                    $agent->name,
                    $agent->email,
                    $playerIds->count(),
                    number_format($totalBets, 2, '.', ''),
                    number_format($agentCommission, 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, $fileName);
    }

    private function validBetsForPlayerIds($playerIds, $dateFrom = null, $dateTo = null)
    {
        if (!Schema::hasTable('game_bets') || collect($playerIds)->isEmpty()) {
            return collect();
        }

        $query = GameBet::query();

        if (Schema::hasColumn('game_bets', 'user_id') && Schema::hasColumn('game_bets', 'player_id')) {
            $query->where(function ($q) use ($playerIds) {
                $q->whereIn('user_id', $playerIds)
                    ->orWhereIn('player_id', $playerIds);
            });
        } elseif (Schema::hasColumn('game_bets', 'user_id')) {
            $query->whereIn('user_id', $playerIds);
        } elseif (Schema::hasColumn('game_bets', 'player_id')) {
            $query->whereIn('player_id', $playerIds);
        }

        if (Schema::hasColumn('game_bets', 'status')) {
            $query->whereNotIn('status', ['refunded', 'cancelled']);
        }

        if (
            Schema::hasTable('game_rounds') &&
            Schema::hasColumn('game_bets', 'game_round_id') &&
            Schema::hasColumn('game_rounds', 'winning_side')
        ) {
            $query->whereNotIn('game_round_id', function ($subQuery) {
                $subQuery->select('id')
                    ->from('game_rounds')
                    ->where('winning_side', 'cancelled');
            });
        }

        $query = $this->dateFilter($query, $dateFrom, $dateTo);

        return $query->get();
    }

    private function dateFilter($query, $dateFrom = null, $dateTo = null)
    {
        return $query
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            });
    }

    private function initialWalletAmount($dateFrom = null): float
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'wallet_balance')) {
            return 0;
        }

        if (!$dateFrom) {
            if (Schema::hasColumn('users', 'initial_wallet_balance')) {
                return (float) User::query()->sum('initial_wallet_balance');
            }

            return (float) User::query()->sum('wallet_balance');
        }

        $currentWallet = (float) User::query()->sum('wallet_balance');

        if (!Schema::hasTable('wallet_transactions')) {
            return $currentWallet;
        }

        $transactionsAfterDate = WalletTransaction::query()
            ->whereDate('created_at', '>=', $dateFrom)
            ->get();

        $movement = 0;

        foreach ($transactionsAfterDate as $transaction) {
            $amount = (float) $transaction->amount;

            if ($transaction->direction === 'credit') {
                $movement += $amount;
            }

            if ($transaction->direction === 'debit') {
                $movement -= $amount;
            }
        }

        return round($currentWallet - $movement, 2);
    }

    private function drawWinAmount($dateFrom = null, $dateTo = null): float
    {
        return (float) $this->dateFilter(GameBet::query(), $dateFrom, $dateTo)
            ->where('side', 'draw')
            ->whereIn('status', ['won', 'paid'])
            ->when(Schema::hasTable('game_rounds'), function ($query) {
                $query->whereNotIn('game_round_id', function ($subQuery) {
                    $subQuery->select('id')
                        ->from('game_rounds')
                        ->where('winning_side', 'cancelled');
                });
            })
            ->sum('payout_amount');
    }

    private function dailyOverview($dateFrom = null, $dateTo = null)
    {
        if (!Schema::hasTable('game_bets')) {
            return collect();
        }

        return $this->dateFilter(GameBet::query(), $dateFrom, $dateTo)
            ->when(Schema::hasTable('game_rounds'), function ($query) {
                $query->whereNotIn('game_round_id', function ($subQuery) {
                    $subQuery->select('id')
                        ->from('game_rounds')
                        ->where('winning_side', 'cancelled');
                });
            })
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw("SUM(CASE WHEN status NOT IN ('refunded', 'cancelled') THEN amount ELSE 0 END) as total_bets")
            ->selectRaw("SUM(CASE WHEN side = 'draw' AND status NOT IN ('refunded', 'cancelled') THEN amount ELSE 0 END) as total_draw_bet")
            ->selectRaw("SUM(CASE WHEN side = 'draw' AND status IN ('won', 'paid') THEN payout_amount ELSE 0 END) as total_draw_win")
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) DESC')
            ->get();
    }

    private function safeQuery(string $modelClass)
    {
        $model = new $modelClass;
        $table = $model->getTable();

        if (!Schema::hasTable($table)) {
            return $modelClass::query()->whereRaw('1 = 0');
        }

        return $modelClass::query();
    }

    private function sumIfColumnExists($query, string $table, string $column): float
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return 0;
        }

        return (float) (clone $query)->sum($column);
    }
}