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
                <div class="metric {{ $card['tone'] }}">
                    <p class="label">{{ $card['label'] }}</p>
                    <h2 class="value">₱{{ number_format($card['value'], 2) }}</h2>
                    <p class="sub">{{ $card['sub'] }}</p>
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
                        @forelse($daily as $day)
                            <tr>
                                <td>
                                    <span class="amount">{{ $day['date'] }}</span>
                                </td>

                                <td>
                                    {{ number_format($day['games']) }}
                                </td>

                                <td>
                                    <span class="amount-blue">
                                        ₱{{ number_format($day['total_pool'], 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount-green">
                                        ₱{{ number_format($day['company_commission'], 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount-orange">
                                        ₱{{ number_format($day['agent_commission'], 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount-green">
                                        ₱{{ number_format($day['total_commission'], 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount">
                                        ₱{{ number_format($day['net_pool'], 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount-purple">
                                        ₱{{ number_format($day['payout_total'], 2) }}
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