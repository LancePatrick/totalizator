<x-layouts.app :title="__('Activity Logs')">
    <style>
        .page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .hero {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 28px;
            background: linear-gradient(135deg, #03142f 0%, #041a4d 55%, #064e3b 100%);
            color: white;
            box-shadow: 0 18px 42px rgba(15, 23, 42, .12);
        }

        .hero::after {
            content: "LG";
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

        .kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .subtitle {
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

        .filter {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto auto;
            gap: 10px;
        }

        .input,
        .select {
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

        .input:focus,
        .select:focus {
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
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .summary-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .045);
        }

        .summary-label {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 850;
        }

        .summary-value {
            margin: 8px 0 0;
            color: #0f172a;
            font-size: 28px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .summary-sub {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
        }

        .green {
            color: #16a34a !important;
        }

        .red {
            color: #dc2626 !important;
        }

        .blue {
            color: #2563eb !important;
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
            min-width: 1120px;
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
            vertical-align: top;
        }

        tbody tr:hover {
            background: #f8fbff;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .name {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
        }

        .muted {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
        }

        .amount {
            font-weight: 950;
            color: #0f172a;
            white-space: nowrap;
        }

        .amount-credit {
            font-weight: 950;
            color: #16a34a;
            white-space: nowrap;
        }

        .amount-debit {
            font-weight: 950;
            color: #dc2626;
            white-space: nowrap;
        }

        .pill {
            min-height: 32px;
            border-radius: 999px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 950;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        .pill-credit {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .pill-debit {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .pill-type {
            background: #dbeafe;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .description {
            max-width: 360px;
            color: #334155;
            line-height: 1.5;
            font-weight: 750;
        }

        .empty {
            padding: 34px 20px;
            text-align: center;
            color: #64748b;
            font-weight: 850;
        }

        .pagination {
            margin-top: 16px;
        }

        @media(max-width: 1200px) {
            .filter {
                grid-template-columns: 1fr 1fr;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .card-head {
                flex-direction: column;
            }
        }

        @media(max-width: 700px) {
            .filter {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $logs = $logs ?? $activityLogs ?? $transactions ?? collect();

        $visibleLogs = method_exists($logs, 'items')
            ? collect($logs->items())
            : collect($logs);

        $visibleCredit = $visibleLogs
            ->where('direction', 'credit')
            ->sum(fn ($log) => (float) data_get($log, 'amount', 0));

        $visibleDebit = $visibleLogs
            ->where('direction', 'debit')
            ->sum(fn ($log) => (float) data_get($log, 'amount', 0));

        $visibleCount = method_exists($logs, 'total')
            ? $logs->total()
            : $visibleLogs->count();
    @endphp

    <div class="page">
        <section class="hero">
            <div class="hero-content">
                <p class="kicker">Admin Monitoring</p>
                <h1 class="title">Activity Logs</h1>
                <p class="subtitle">
                    Track wallet movements, payouts, refunds, loading, withdrawal, commission conversion, and system activity.
                </p>
            </div>
        </section>

        <section class="summary-grid">
            <div class="summary-card">
                <p class="summary-label">Total Logs</p>
                <h2 class="summary-value">{{ number_format($visibleCount) }}</h2>
                <p class="summary-sub">Filtered activity log count</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Visible Credit</p>
                <h2 class="summary-value green">₱{{ number_format($visibleCredit, 2) }}</h2>
                <p class="summary-sub">Credit total on this page</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Visible Debit</p>
                <h2 class="summary-value red">₱{{ number_format($visibleDebit, 2) }}</h2>
                <p class="summary-sub">Debit total on this page</p>
            </div>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Filters</h2>
                    <p class="card-sub">
                        Search by user, type, description, direction, and date range.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="filter">
                <input
                    class="input"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search user, type, description"
                >

                <select class="select" name="direction">
                    <option value="">All Directions</option>
                    <option value="credit" @selected(request('direction') === 'credit')>Credit</option>
                    <option value="debit" @selected(request('direction') === 'debit')>Debit</option>
                </select>

                <input
                    class="input"
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                >

                <input
                    class="input"
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                >

                <button class="btn btn-blue">Filter</button>

                <a class="btn btn-dark" href="{{ route('admin.activity-logs.index') }}">
                    Reset
                </a>
            </form>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Transaction Activity</h2>
                    <p class="card-sub">
                        Complete list of wallet transaction activities and descriptions.
                    </p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Direction</th>
                            <th>Amount</th>
                            <th>Before</th>
                            <th>After</th>
                            <th>Description</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($visibleLogs as $log)
                            @php
                                $direction = strtolower((string) data_get($log, 'direction', ''));
                                $isCredit = $direction === 'credit';

                                $createdAt = data_get($log, 'created_at');
                                $userName = data_get($log, 'user.name', 'Unknown User');
                                $userEmail = data_get($log, 'user.email', 'No email');
                                $type = data_get($log, 'type', 'N/A');
                                $amount = (float) data_get($log, 'amount', 0);
                                $before = (float) data_get($log, 'balance_before', 0);
                                $after = (float) data_get($log, 'balance_after', 0);
                                $description = data_get($log, 'description', 'No description');
                            @endphp

                            <tr>
                                <td>
                                    <p class="name">
                                        {{ $createdAt ? \Illuminate\Support\Carbon::parse($createdAt)->format('M d, Y') : 'N/A' }}
                                    </p>
                                    <p class="muted">
                                        {{ $createdAt ? \Illuminate\Support\Carbon::parse($createdAt)->format('h:i A') : '' }}
                                    </p>
                                </td>

                                <td>
                                    <p class="name">{{ $userName }}</p>
                                    <p class="muted">{{ $userEmail }}</p>
                                </td>

                                <td>
                                    <span class="pill pill-type">
                                        {{ strtoupper(str_replace('_', ' ', $type)) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="pill {{ $isCredit ? 'pill-credit' : 'pill-debit' }}">
                                        {{ strtoupper($direction ?: 'N/A') }}
                                    </span>
                                </td>

                                <td>
                                    <span class="{{ $isCredit ? 'amount-credit' : 'amount-debit' }}">
                                        {{ $isCredit ? '+' : '-' }} ₱{{ number_format($amount, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount">
                                        ₱{{ number_format($before, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount">
                                        ₱{{ number_format($after, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="description">
                                        {{ $description ?: 'No description' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty">
                                        No activity logs found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($logs, 'links'))
                <div class="pagination">
                    {{ $logs->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>