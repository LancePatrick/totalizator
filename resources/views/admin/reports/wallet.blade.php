<x-layouts.app :title="__('Wallet Reports')">
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
            content:"₱";
            position:absolute;
            right:48px;
            top:4px;
            font-size:130px;
            font-weight:950;
            color:rgba(250,204,21,.20);
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
            grid-template-columns:repeat(3,minmax(0,1fr));
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
            font-size:30px;
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
            grid-template-columns:1fr 1fr 1fr 1fr auto auto auto;
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
            min-width:1100px;
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

        .credit {
            color:#16a34a;
            font-size:14px;
            font-weight:950;
            white-space:nowrap;
        }

        .debit {
            color:#dc2626;
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

        .pill-credit { background:#dcfce7; color:#15803d; }
        .pill-debit { background:#fee2e2; color:#dc2626; }
        .pill-default { background:#f1f5f9; color:#475569; }

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

        @media(max-width:1200px) {
            .filter { grid-template-columns:1fr 1fr; }
            .summary-grid { grid-template-columns:1fr; }
            .card-head { flex-direction:column; }
        }

        @media(max-width:700px) {
            .filter { grid-template-columns:1fr; }
        }
    </style>

    <div class="report-page">
        <section class="report-hero">
            <div class="report-hero-inner">
                <p class="report-kicker">Admin Reports</p>
                <h1 class="report-title">Wallet Transaction Report</h1>
                <p class="report-subtitle">
                    View all credits, debits, deposits, withdrawals, bet deductions, payouts, and wallet movements.
                </p>
            </div>
        </section>

        <section class="summary-grid">
            <div class="summary-card">
                <p class="summary-label">Total Credit</p>
                <h2 class="summary-value" style="color:#16a34a;">
                    ₱{{ number_format($totalCredit ?? 0, 2) }}
                </h2>
                <p class="summary-sub">Money added to wallets</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Total Debit</p>
                <h2 class="summary-value" style="color:#dc2626;">
                    ₱{{ number_format($totalDebit ?? 0, 2) }}
                </h2>
                <p class="summary-sub">Money deducted from wallets</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Total Transactions</p>
                <h2 class="summary-value">
                    {{ number_format($totalTransactions ?? 0) }}
                </h2>
                <p class="summary-sub">Filtered transaction count</p>
            </div>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Filters</h2>
                    <p class="card-sub">Filter by transaction type, direction, and date range.</p>
                </div>

                <div class="no-print" style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="{{ route('admin.reports.games') }}" class="btn btn-dark">
                        Game Reports
                    </a>

                    <button onclick="window.print()" class="btn btn-yellow">
                        Print
                    </button>

                    <a href="{{ route('admin.reports.wallet.export', request()->query()) }}" class="btn btn-green">
                        Export CSV
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.reports.wallet') }}" class="filter no-print">
                <select name="type" class="select">
                    <option value="">All Types</option>
                    <option value="agent_money_request" @selected(request('type') === 'agent_money_request')>Agent Money Request</option>
                    <option value="agent_withdrawal" @selected(request('type') === 'agent_withdrawal')>Agent Withdrawal</option>
                    <option value="player_money_request" @selected(request('type') === 'player_money_request')>Player Money Request</option>
                    <option value="player_withdrawal" @selected(request('type') === 'player_withdrawal')>Player Withdrawal</option>
                    <option value="bet" @selected(request('type') === 'bet')>Bet</option>
                    <option value="payout" @selected(request('type') === 'payout')>Payout</option>
                    <option value="refund" @selected(request('type') === 'refund')>Refund</option>
                </select>

                <select name="direction" class="select">
                    <option value="">All Directions</option>
                    <option value="credit" @selected(request('direction') === 'credit')>Credit</option>
                    <option value="debit" @selected(request('direction') === 'debit')>Debit</option>
                </select>

                <input class="input" type="date" name="date_from" value="{{ request('date_from') }}">
                <input class="input" type="date" name="date_to" value="{{ request('date_to') }}">

                <button class="btn btn-blue">Apply</button>
                <a href="{{ route('admin.reports.wallet') }}" class="btn btn-dark">Overall</a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-dark">Back</a>
            </form>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Transaction History</h2>
                    <p class="card-sub">Complete wallet movement records.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Type</th>
                            <th>Direction</th>
                            <th>Amount</th>
                            <th>Before</th>
                            <th>After</th>
                            <th>Description</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transactions as $transaction)
                            @php
                                $direction = strtolower($transaction->direction ?? '');
                                $directionClass = $direction === 'credit'
                                    ? 'pill-credit'
                                    : ($direction === 'debit' ? 'pill-debit' : 'pill-default');
                            @endphp

                            <tr>
                                <td>
                                    <p class="muted">
                                        {{ $transaction->created_at?->format('M d, Y') }}
                                    </p>
                                    <p class="muted">
                                        {{ $transaction->created_at?->format('h:i A') }}
                                    </p>
                                </td>

                                <td>
                                    <p class="name">{{ $transaction->user?->name ?? 'Unknown User' }}</p>
                                    <p class="muted">{{ $transaction->user?->email }}</p>
                                </td>

                                <td>
                                    <p class="name">{{ strtoupper($transaction->user?->role ?? 'N/A') }}</p>
                                </td>

                                <td>
                                    <p class="name">{{ str_replace('_', ' ', strtoupper($transaction->type ?? 'N/A')) }}</p>
                                </td>

                                <td>
                                    <span class="pill {{ $directionClass }}">
                                        {{ $transaction->direction ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="{{ $direction === 'credit' ? 'credit' : 'debit' }}">
                                        {{ $direction === 'credit' ? '+' : '-' }} ₱{{ number_format($transaction->amount ?? 0, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount">
                                        ₱{{ number_format($transaction->balance_before ?? 0, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount">
                                        ₱{{ number_format($transaction->balance_after ?? 0, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <p class="muted">
                                        {{ $transaction->description ?: 'No description' }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty">
                                        No wallet transactions found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination no-print">
                {{ $transactions->links() }}
            </div>
        </section>
    </div>
</x-layouts.app>