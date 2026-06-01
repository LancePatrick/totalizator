<x-layouts.app :title="__('Game Reports')">
    <style>
        .report-page { display:flex; flex-direction:column; gap:18px; }

        .report-hero {
            position:relative;
            overflow:hidden;
            border-radius:22px;
            padding:28px;
            color:white;
            background:linear-gradient(135deg,#03142f 0%,#041a4d 52%,#0848b9 100%);
            box-shadow:0 18px 42px rgba(2,18,54,.18);
        }

        .report-hero::after {
            content:"RP";
            position:absolute;
            right:44px;
            top:12px;
            font-size:110px;
            font-weight:950;
            color:rgba(250,204,21,.18);
            transform:rotate(-8deg);
        }

        .report-hero-inner { position:relative; z-index:2; }

        .report-kicker {
            margin:0;
            color:#38bdf8;
            font-size:12px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.18em;
        }

        .report-title {
            margin:8px 0 0;
            color:white;
            font-size:34px;
            font-weight:950;
            letter-spacing:-.04em;
        }

        .report-subtitle {
            margin:10px 0 0;
            color:rgba(255,255,255,.74);
            font-size:14px;
            font-weight:700;
            line-height:1.6;
        }

        .summary-grid {
            display:grid;
            grid-template-columns:repeat(4,minmax(0,1fr));
            gap:14px;
        }

        .summary-card {
            background:white;
            border:1px solid #dce6f2;
            border-radius:20px;
            padding:18px;
            box-shadow:0 10px 24px rgba(15,23,42,.045);
        }

        .summary-label {
            margin:0;
            color:#64748b;
            font-size:13px;
            font-weight:850;
        }

        .summary-value {
            margin:10px 0 0;
            color:#0f172a;
            font-size:28px;
            font-weight:950;
            letter-spacing:-.04em;
        }

        .summary-sub {
            margin:7px 0 0;
            color:#64748b;
            font-size:12px;
            font-weight:750;
        }

        .card {
            background:white;
            border:1px solid #dce6f2;
            border-radius:20px;
            padding:20px;
            box-shadow:0 10px 24px rgba(15,23,42,.045);
        }

        .card-head {
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:14px;
            margin-bottom:18px;
        }

        .card-title {
            margin:0;
            color:#0f172a;
            font-size:22px;
            font-weight:950;
        }

        .card-sub {
            margin:6px 0 0;
            color:#64748b;
            font-size:13px;
            font-weight:700;
        }

        .filter {
            display:grid;
            grid-template-columns:2fr 1fr 1fr 1fr 1fr auto auto auto;
            gap:10px;
            margin-bottom:18px;
        }

        .input,
        .select {
            width:100%;
            height:42px;
            border-radius:12px;
            border:1px solid #dce6f2;
            background:white;
            padding:0 12px;
            color:#0f172a;
            font-size:13px;
            font-weight:800;
            outline:none;
        }

        .btn {
            min-height:42px;
            border:0;
            border-radius:12px;
            padding:0 14px;
            font-size:12px;
            font-weight:950;
            cursor:pointer;
            text-transform:uppercase;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            white-space:nowrap;
        }

        .btn-blue { background:#2563eb; color:white; }
        .btn-dark { background:#0f172a; color:white; }
        .btn-green { background:#16a34a; color:white; }
        .btn-yellow { background:#facc15; color:#0f172a; }

        .table-wrap {
            width:100%;
            overflow-x:auto;
            border:1px solid #e7edf6;
            border-radius:18px;
        }

        table {
            width:100%;
            min-width:1320px;
            border-collapse:collapse;
            text-align:left;
            font-size:14px;
        }

        thead { background:#f8fbff; }

        th {
            padding:14px;
            color:#64748b;
            font-size:12px;
            font-weight:950;
            text-transform:uppercase;
            letter-spacing:.08em;
            border-bottom:1px solid #e7edf6;
            white-space:nowrap;
        }

        td {
            padding:16px 14px;
            border-bottom:1px solid #eef2f7;
            vertical-align:middle;
        }

        tbody tr:hover { background:#f8fbff; }

        .name {
            margin:0;
            color:#0f172a;
            font-size:14px;
            font-weight:950;
        }

        .muted {
            margin:5px 0 0;
            color:#64748b;
            font-size:12px;
            font-weight:700;
        }

        .amount {
            color:#0f172a;
            font-size:14px;
            font-weight:950;
            white-space:nowrap;
        }

        .income {
            color:#16a34a;
            font-size:14px;
            font-weight:950;
            white-space:nowrap;
        }

        .payout {
            color:#7c3aed;
            font-size:14px;
            font-weight:950;
            white-space:nowrap;
        }

        .commission {
            color:#ea580c;
            font-size:14px;
            font-weight:950;
            white-space:nowrap;
        }

        .pill {
            display:inline-flex;
            border-radius:999px;
            padding:7px 11px;
            font-size:11px;
            font-weight:950;
            text-transform:uppercase;
            white-space:nowrap;
        }

        .pill-open { background:#dcfce7; color:#15803d; }
        .pill-waiting { background:#fef3c7; color:#b45309; }
        .pill-closed { background:#dbeafe; color:#2563eb; }
        .pill-ended { background:#fee2e2; color:#dc2626; }
        .pill-settled { background:#ede9fe; color:#7c3aed; }
        .pill-default { background:#f1f5f9; color:#475569; }

        .daily-grid {
            display:grid;
            grid-template-columns:repeat(3,minmax(0,1fr));
            gap:14px;
        }

        .daily-card {
            border:1px solid #e7edf6;
            border-radius:18px;
            padding:16px;
            background:#f8fbff;
        }

        .daily-date {
            margin:0;
            color:#0f172a;
            font-size:16px;
            font-weight:950;
        }

        .daily-row {
            display:flex;
            justify-content:space-between;
            gap:12px;
            margin-top:10px;
            color:#64748b;
            font-size:13px;
            font-weight:800;
        }

        .daily-row strong {
            color:#0f172a;
            font-weight:950;
        }

        .empty {
            padding:34px 20px;
            text-align:center;
            color:#64748b;
            font-weight:850;
        }

        .pagination { margin-top:16px; }

        @media print {
            body { background:white !important; }
            .no-print { display:none !important; }
            .card, .summary-card { box-shadow:none; }
        }

        @media(max-width:1400px) {
            .filter { grid-template-columns:1fr 1fr 1fr; }
            .summary-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .daily-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }

        @media(max-width:800px) {
            .filter,
            .summary-grid,
            .daily-grid {
                grid-template-columns:1fr;
            }

            .card-head {
                flex-direction:column;
            }
        }
    </style>

    <div class="report-page">
        <section class="report-hero">
            <div class="report-hero-inner">
                <p class="report-kicker">Admin Reports</p>

                <h1 class="report-title">
                    Commission & Game Earnings Report
                </h1>

                <p class="report-subtitle">
                    View total pool, commission/plasada, final all bet, payouts, admin income, earnings per day, and full game history.
                </p>
            </div>
        </section>

        <section class="summary-grid">
            <div class="summary-card">
                <p class="summary-label">Total Games</p>
                <h2 class="summary-value">{{ number_format($totalGames ?? 0) }}</h2>
                <p class="summary-sub">Filtered game count</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Total Pool</p>
                <h2 class="summary-value">₱{{ number_format($totalPool ?? 0, 2) }}</h2>
                <p class="summary-sub">Meron + Wala + Draw</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Commission / Plasada</p>
                <h2 class="summary-value" style="color:#ea580c;">
                    ₱{{ number_format($commissionEarned ?? 0, 2) }}
                </h2>
                <p class="summary-sub">Total pool × commission rate</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Final All Bet / Net Pool</p>
                <h2 class="summary-value">₱{{ number_format($netPool ?? 0, 2) }}</h2>
                <p class="summary-sub">Total pool minus commission</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Payout Total</p>
                <h2 class="summary-value" style="color:#7c3aed;">
                    ₱{{ number_format($payoutTotal ?? 0, 2) }}
                </h2>
                <p class="summary-sub">Total paid to winners/refunds</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Admin Income</p>
                <h2 class="summary-value" style="color:#16a34a;">
                    ₱{{ number_format($adminIncome ?? $commissionEarned ?? 0, 2) }}
                </h2>
                <p class="summary-sub">Commission earnings</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Average Income/Game</p>
                <h2 class="summary-value">
                    ₱{{ number_format($averageIncomePerGame ?? 0, 2) }}
                </h2>
                <p class="summary-sub">Admin income average</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Bet Breakdown</p>
                <h2 class="summary-value" style="font-size:18px;line-height:1.5;">
                    M ₱{{ number_format($meronTotal ?? 0, 2) }}<br>
                    W ₱{{ number_format($walaTotal ?? 0, 2) }}<br>
                    D ₱{{ number_format($drawTotal ?? 0, 2) }}
                </h2>
                <p class="summary-sub">Meron / Wala / Draw pools</p>
            </div>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Filters</h2>
                    <p class="card-sub">Filter by date, game/round, status, winning side, or reset to overall.</p>
                </div>

                <div class="no-print" style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="{{ route('admin.reports.wallet') }}" class="btn btn-dark">
                        Wallet Reports
                    </a>

                    <button onclick="window.print()" class="btn btn-yellow">
                        Print
                    </button>

                    <a href="{{ route('admin.reports.games.export', request()->query()) }}" class="btn btn-green">
                        Export CSV
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.reports.games') }}" class="filter no-print">
                <input
                    class="input"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search round name, number, or ID"
                >

                <select name="status" class="select">
                    <option value="">All Status</option>
                    <option value="waiting" @selected(request('status') === 'waiting')>Waiting</option>
                    <option value="open" @selected(request('status') === 'open')>Open</option>
                    <option value="closed" @selected(request('status') === 'closed')>Closed</option>
                    <option value="ended" @selected(request('status') === 'ended')>Ended</option>
                    <option value="settled" @selected(request('status') === 'settled')>Settled</option>
                </select>

                <select name="winning_side" class="select">
                    <option value="">All Winners</option>
                    <option value="meron" @selected(request('winning_side') === 'meron')>Meron</option>
                    <option value="wala" @selected(request('winning_side') === 'wala')>Wala</option>
                    <option value="draw" @selected(request('winning_side') === 'draw')>Draw</option>
                    <option value="cancelled" @selected(request('winning_side') === 'cancelled')>Cancelled</option>
                </select>

                <input class="input" type="date" name="date_from" value="{{ request('date_from') }}">
                <input class="input" type="date" name="date_to" value="{{ request('date_to') }}">

                <a href="{{ route('admin.reports.games') }}" class="btn btn-dark">Overall</a>
                <button class="btn btn-blue">Apply</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-dark">Back</a>
            </form>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Earnings Per Day</h2>
                    <p class="card-sub">Daily commission, payouts, and admin income based on current filters.</p>
                </div>
            </div>

            <div class="daily-grid">
                @forelse($dailyEarnings as $day)
                    <div class="daily-card">
                        <p class="daily-date">{{ $day['date'] }}</p>

                        <div class="daily-row">
                            <span>Games</span>
                            <strong>{{ number_format($day['games']) }}</strong>
                        </div>

                        <div class="daily-row">
                            <span>Total Pool</span>
                            <strong>₱{{ number_format($day['total_pool'], 2) }}</strong>
                        </div>

                        <div class="daily-row">
                            <span>Final All Bet</span>
                            <strong>₱{{ number_format($day['net_pool'], 2) }}</strong>
                        </div>

                        <div class="daily-row">
                            <span>Commission</span>
                            <strong style="color:#ea580c;">₱{{ number_format($day['commission'], 2) }}</strong>
                        </div>

                        <div class="daily-row">
                            <span>Payout</span>
                            <strong style="color:#7c3aed;">₱{{ number_format($day['payout_total'] ?? 0, 2) }}</strong>
                        </div>

                        <div class="daily-row">
                            <span>Admin Income</span>
                            <strong style="color:#16a34a;">₱{{ number_format($day['admin_income'] ?? $day['commission'], 2) }}</strong>
                        </div>
                    </div>
                @empty
                    <div class="empty">No daily earnings yet.</div>
                @endforelse
            </div>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Game History</h2>
                    <p class="card-sub">Detailed game report with total pool, commission, final all bet, payout, and admin income.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Game / Round</th>
                            <th>Status</th>
                            <th>Winner</th>
                            <th>Meron</th>
                            <th>Wala</th>
                            <th>Draw</th>
                            <th>Total Pool</th>
                            <th>Rate</th>
                            <th>Commission</th>
                            <th>Final All Bet</th>
                            <th>Payout</th>
                            <th>Admin Income</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($games as $game)
                            @php
                                $statusClass = match ($game->status) {
                                    'open' => 'pill-open',
                                    'waiting' => 'pill-waiting',
                                    'closed' => 'pill-closed',
                                    'ended' => 'pill-ended',
                                    'settled' => 'pill-settled',
                                    default => 'pill-default',
                                };

                                $commissionAmount = (float) ($game->commission_amount ?? max(0, ($game->total_pool ?? 0) - ($game->net_pool ?? 0)));
                                $adminGameIncome = (float) ($game->admin_income ?? $commissionAmount);
                            @endphp

                            <tr>
                                <td>
                                    <p class="muted">{{ $game->created_at?->format('M d, Y') }}</p>
                                    <p class="muted">{{ $game->created_at?->format('h:i A') }}</p>
                                </td>

                                <td>
                                    <p class="name">{{ $game->round_name ?: 'Game #' . $game->id }}</p>
                                    <p class="muted">Round: {{ $game->round_number ?: $game->id }}</p>
                                </td>

                                <td>
                                    <span class="pill {{ $statusClass }}">
                                        {{ $game->status ?: 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    <p class="name">{{ strtoupper($game->winning_side ?: 'N/A') }}</p>
                                </td>

                                <td><span class="amount">₱{{ number_format($game->meron_total ?? 0, 2) }}</span></td>
                                <td><span class="amount">₱{{ number_format($game->wala_total ?? 0, 2) }}</span></td>
                                <td><span class="amount">₱{{ number_format($game->draw_total ?? 0, 2) }}</span></td>

                                <td>
                                    <span class="amount">₱{{ number_format($game->total_pool ?? 0, 2) }}</span>
                                </td>

                                <td>
                                    <span class="amount">{{ number_format($game->commission_rate ?? 0, 2) }}%</span>
                                </td>

                                <td>
                                    <span class="commission">₱{{ number_format($commissionAmount, 2) }}</span>
                                </td>

                                <td>
                                    <span class="amount">₱{{ number_format($game->net_pool ?? 0, 2) }}</span>
                                </td>

                                <td>
                                    <span class="payout">₱{{ number_format($game->payout_total ?? 0, 2) }}</span>
                                </td>

                                <td>
                                    <span class="income">₱{{ number_format($adminGameIncome, 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13">
                                    <div class="empty">No game reports found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination no-print">
                {{ $games->links() }}
            </div>
        </section>
    </div>
</x-layouts.app>