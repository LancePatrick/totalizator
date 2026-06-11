<x-layouts.app :title="__('Overview Report')">
    <style>
        .mon-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .mon-hero {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 28px;
            background: linear-gradient(135deg, #03142f 0%, #041a4d 55%, #064e3b 100%);
            color: white;
            box-shadow: 0 18px 42px rgba(15, 23, 42, .12);
        }

        .mon-hero::after {
            content: "OV";
            position: absolute;
            right: 42px;
            top: 8px;
            font-size: 110px;
            font-weight: 950;
            color: rgba(255, 255, 255, .10);
            transform: rotate(-8deg);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .mon-kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .mon-title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .mon-sub {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, .74);
            font-size: 14px;
            font-weight: 750;
            line-height: 1.6;
        }

        .card {
            background: #ffffff;
            border: 1px solid #dce6f2;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .045);
        }

        .filter {
            display: grid;
            grid-template-columns: 1fr 1fr auto auto;
            gap: 10px;
        }

        .input {
            width: 100%;
            height: 46px;
            border-radius: 14px;
            border: 1px solid #dce6f2;
            background: #ffffff;
            padding: 0 14px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            outline: none;
        }

        .input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
        }

        .btn {
            min-height: 46px;
            border: 0;
            border-radius: 14px;
            padding: 0 18px;
            font-size: 12px;
            font-weight: 950;
            cursor: pointer;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .btn-blue {
            background: #2563eb;
            color: white;
            box-shadow: 0 10px 22px rgba(37, 99, 235, .18);
        }

        .btn-dark {
            background: #0f172a;
            color: white;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
        }

        .metric {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .045);
        }

        .label {
            margin: 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .value {
            margin: 10px 0 0;
            color: #0f172a;
            font-size: 26px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
            line-height: 1.4;
        }

        .blue .value { color: #2563eb; }
        .red .value { color: #dc2626; }
        .green .value { color: #16a34a; }
        .yellow .value { color: #ca8a04; }
        .purple .value { color: #7c3aed; }
        .white .value { color: #0f172a; }

        .card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
        }

        .card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            background: white;
        }

        table {
            width: 100%;
            min-width: 1050px;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            padding: 14px;
            background: #f8fbff;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        td {
            padding: 16px 14px;
            border-bottom: 1px solid #edf2f7;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #f8fbff;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .amount {
            color: #0f172a;
            font-weight: 950;
            white-space: nowrap;
        }

        .amount-green {
            color: #16a34a;
            font-weight: 950;
            white-space: nowrap;
        }

        .amount-blue {
            color: #2563eb;
            font-weight: 950;
            white-space: nowrap;
        }

        .amount-orange {
            color: #ea580c;
            font-weight: 950;
            white-space: nowrap;
        }

        .amount-purple {
            color: #7c3aed;
            font-weight: 950;
            white-space: nowrap;
        }

        .empty {
            padding: 34px 20px;
            text-align: center;
            color: #64748b;
            font-weight: 850;
        }

        @media(max-width: 1500px) {
            .summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media(max-width: 1000px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter {
                grid-template-columns: 1fr 1fr;
            }

            .card-head {
                flex-direction: column;
            }
        }

        @media(max-width: 700px) {
            .summary-grid,
            .filter {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $dateFrom = $dateFrom ?? request('date_from');
        $dateTo = $dateTo ?? request('date_to');

        $safeMoney = function ($value) {
            return (float) ($value ?? 0);
        };

        /*
        |--------------------------------------------------------------------------
        | Safe Card Values
        |--------------------------------------------------------------------------
        | The controller sends $cards. This blade only fixes display values for:
        | Total Draw Bet = Total Bets
        | Total Draw Win = Total Bets - Total Commission
        |--------------------------------------------------------------------------
        */

        $safeTotalBets = $safeMoney($totalBets ?? 0);
        $safeTotalCommission = $safeMoney($totalCommission ?? 0);

        if ($safeTotalCommission <= 0 && $safeTotalBets > 0) {
            $safeTotalCommission = round($safeTotalBets * 0.05, 2);
        }

        $safeDrawBet = $safeTotalBets;
        $safeDrawWin = round($safeTotalBets - $safeTotalCommission, 2);

        if ($safeDrawWin < 0) {
            $safeDrawWin = 0;
        }

        $cards = collect($cards ?? []);

        if ($cards->isEmpty()) {
            $companyCommission = $safeMoney($companyCommission ?? 0);
            $agentCommission = $safeMoney($agentCommission ?? $totalAgentCommission ?? 0);
            $totalFivePercentCommission = $safeMoney($totalFivePercentCommission ?? ($companyCommission + $agentCommission));

            $cards = collect([
                [
                    'label' => 'Total Loading',
                    'value' => $safeMoney($totalLoading ?? 0),
                    'sub' => 'Approved wallet loading',
                    'tone' => 'blue',
                ],
                [
                    'label' => 'Total Withdrawal',
                    'value' => $safeMoney($totalWithdrawal ?? 0),
                    'sub' => 'Approved withdrawals',
                    'tone' => 'red',
                ],
                [
                    'label' => 'Total Convert Commission',
                    'value' => $safeMoney($totalConvertCommission ?? 0),
                    'sub' => 'Commission converted to load',
                    'tone' => 'purple',
                ],
                [
                    'label' => 'Commission Cashout',
                    'value' => $safeMoney($totalCommissionCashOut ?? 0),
                    'sub' => 'Commission withdrawn as cash',
                    'tone' => 'red',
                ],
                [
                    'label' => 'Total Bets',
                    'value' => $safeTotalBets,
                    'sub' => 'All valid player bets',
                    'tone' => 'white',
                ],
                [
                    'label' => 'Total Commission',
                    'value' => $safeTotalCommission,
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
                    'value' => $safeDrawBet,
                    'sub' => 'Same as total pool / all valid player bets',
                    'tone' => 'purple',
                ],
                [
                    'label' => 'Total Draw Win',
                    'value' => $safeDrawWin,
                    'sub' => 'Total bets minus total commission',
                    'tone' => 'blue',
                ],
                [
                    'label' => 'Initial Wallet',
                    'value' => $safeMoney($initialWallet ?? 0),
                    'sub' => $dateFrom ? 'Wallet at start of selected day' : 'Starting wallet balance',
                    'tone' => 'white',
                ],
                [
                    'label' => 'Actual Wallet',
                    'value' => $safeMoney($actualWallet ?? 0),
                    'sub' => 'Current wallet balance',
                    'tone' => 'green',
                ],
                [
                    'label' => 'Must Total Wallet',
                    'value' => $safeMoney($mustTotalWallet ?? 0),
                    'sub' => 'Computed expected wallet',
                    'tone' => 'yellow',
                ],
                [
                    'label' => 'Wallet Difference',
                    'value' => $safeMoney($walletDifference ?? 0),
                    'sub' => 'Must wallet minus actual',
                    'tone' => ($safeMoney($walletDifference ?? 0) == 0) ? 'green' : 'red',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Safe Daily Overview
        |--------------------------------------------------------------------------
        */

        $dailyRows = collect($daily ?? $dailyOverview ?? []);
    @endphp

    <div class="mon-page">
        <section class="mon-hero">
            <div class="hero-content">
                <p class="mon-kicker">Admin Monitoring</p>
                <h1 class="mon-title">Overview Report</h1>
                <p class="mon-sub">
                    Total loading, withdrawals, convert commission, total bets, company commission, agent commission, wallet audit, and daily overview.
                </p>
            </div>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Filters</h2>
                    <p class="card-sub">Filter overview totals by date range.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.monitoring.overview') }}" class="filter">
                <input
                    class="input"
                    type="date"
                    name="date_from"
                    value="{{ $dateFrom }}"
                >

                <input
                    class="input"
                    type="date"
                    name="date_to"
                    value="{{ $dateTo }}"
                >

                <button class="btn btn-blue">
                    Apply Filter
                </button>

                <a class="btn btn-dark" href="{{ route('admin.monitoring.overview') }}">
                    Reset
                </a>
            </form>
        </section>

        <section class="summary-grid">
            @foreach($cards as $card)
                @php
                    $label = data_get($card, 'label', 'Metric');
                    $value = (float) data_get($card, 'value', 0);
                    $sub = data_get($card, 'sub', '');
                    $tone = data_get($card, 'tone', 'white');

                    /*
                    |--------------------------------------------------------------------------
                    | Force correct Overview display only for these two cards.
                    |--------------------------------------------------------------------------
                    */

                    if ($label === 'Total Draw Bet') {
                        $value = $safeDrawBet;
                        $sub = 'Same as total pool / all valid player bets';
                    }

                    if ($label === 'Total Draw Win') {
                        $value = $safeDrawWin;
                        $sub = 'Total bets minus total commission';
                    }
                @endphp

                <div class="metric {{ $tone }}">
                    <p class="label">{{ $label }}</p>
                    <h2 class="value">₱{{ number_format($value, 2) }}</h2>
                    <p class="sub">{{ $sub }}</p>
                </div>
            @endforeach
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Daily Overview</h2>
                    <p class="card-sub">
                        Daily game pool, company 3%, agent 2%, total 5% commission, net pool, and payout total.
                    </p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Games</th>
                            <th>Total Pool</th>
                            <th>Company 3%</th>
                            <th>Agent 2%</th>
                            <th>Total 5%</th>
                            <th>Net Pool</th>
                            <th>Payout</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($dailyRows as $day)
                            @php
                                $date = data_get($day, 'date', 'N/A');

                                $games = (int) data_get(
                                    $day,
                                    'games',
                                    data_get($day, 'total_games', data_get($day, 'game_count', 0))
                                );

                                $totalPool = (float) data_get(
                                    $day,
                                    'total_pool',
                                    data_get($day, 'total_bets', data_get($day, 'pool', 0))
                                );

                                $companyCommission = (float) data_get(
                                    $day,
                                    'company_commission',
                                    data_get($day, 'company_3', round($totalPool * 0.03, 2))
                                );

                                $agentCommission = (float) data_get(
                                    $day,
                                    'agent_commission',
                                    data_get($day, 'agent_2', round($totalPool * 0.02, 2))
                                );

                                $totalCommission = (float) data_get(
                                    $day,
                                    'total_commission',
                                    data_get($day, 'total_5', round($companyCommission + $agentCommission, 2))
                                );

                                $netPool = (float) data_get(
                                    $day,
                                    'net_pool',
                                    round($totalPool - $totalCommission, 2)
                                );

                                $payoutTotal = (float) data_get(
                                    $day,
                                    'payout_total',
                                    data_get($day, 'payout', data_get($day, 'total_payout', 0))
                                );
                            @endphp

                            <tr>
                                <td>
                                    <span class="amount">{{ $date }}</span>
                                </td>

                                <td>
                                    {{ number_format($games) }}
                                </td>

                                <td>
                                    <span class="amount-blue">
                                        ₱{{ number_format($totalPool, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount-green">
                                        ₱{{ number_format($companyCommission, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount-orange">
                                        ₱{{ number_format($agentCommission, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount-green">
                                        ₱{{ number_format($totalCommission, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount">
                                        ₱{{ number_format($netPool, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount-purple">
                                        ₱{{ number_format($payoutTotal, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty">
                                        No daily data found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>