<x-layouts.app :title="__('Commission Reports')">
    <style>
        .cr-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .cr-hero {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            padding: 28px;
            color: white;
            background:
                radial-gradient(circle at 82% 45%, rgba(34,197,94,.55), transparent 26%),
                radial-gradient(circle at 18% 20%, rgba(250,204,21,.18), transparent 22%),
                linear-gradient(135deg, #03142f 0%, #064e3b 52%, #16a34a 100%);
            box-shadow: 0 18px 42px rgba(2,18,54,.18);
        }

        .cr-hero::after {
            content: "📊";
            position: absolute;
            right: 46px;
            top: 14px;
            font-size: 108px;
            transform: rotate(-8deg);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.28));
        }

        .cr-hero-inner {
            position: relative;
            z-index: 2;
            max-width: 900px;
        }

        .cr-kicker {
            margin: 0;
            color: #86efac;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .cr-title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .cr-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.78);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
        }

        .cr-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .cr-stat {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
            transition: .18s ease;
        }

        .cr-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 34px rgba(15,23,42,.08);
        }

        .cr-stat-label {
            margin: 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .cr-stat-value {
            margin: 8px 0 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .cr-stat-note {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
            line-height: 1.45;
        }

        .cr-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .cr-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
        }

        .cr-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .cr-card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .cr-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .cr-input {
            width: 100%;
            max-width: 230px;
            height: 48px;
            border-radius: 14px;
            border: 1px solid #dce6f2;
            background: #ffffff;
            padding: 0 14px;
            outline: none;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .cr-btn {
            min-height: 48px;
            border: 0;
            border-radius: 14px;
            padding: 0 16px;
            color: white;
            background: #2563eb;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .cr-btn-gray {
            background: #64748b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .cr-table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e7edf6;
            border-radius: 18px;
        }

        .cr-table {
            width: 100%;
            min-width: 1200px;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .cr-table thead {
            background: #f8fbff;
        }

        .cr-table th {
            padding: 14px;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
            border-bottom: 1px solid #e7edf6;
            white-space: nowrap;
        }

        .cr-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: top;
        }

        .cr-table tbody tr:hover {
            background: #f8fbff;
        }

        .cr-name {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
        }

        .cr-muted {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.45;
        }

        .cr-amount {
            margin: 0;
            color: #0f172a;
            font-size: 15px;
            font-weight: 950;
            white-space: nowrap;
        }

        .cr-green { color: #15803d; }
        .cr-red { color: #dc2626; }
        .cr-blue { color: #2563eb; }
        .cr-orange { color: #b45309; }
        .cr-purple { color: #7c3aed; }

        .cr-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            white-space: nowrap;
            background: #eff6ff;
            color: #2563eb;
        }

        .cr-pill.green {
            background: #dcfce7;
            color: #15803d;
        }

        .cr-pill.red {
            background: #fee2e2;
            color: #dc2626;
        }

        .cr-pill.orange {
            background: #fef3c7;
            color: #b45309;
        }

        .cr-pill.purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .cr-pill.gray {
            background: #f1f5f9;
            color: #475569;
        }

        .cr-info {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
        }

        .cr-info-btn {
            border: 0;
            background: transparent;
            padding: 0;
            cursor: pointer;
            color: inherit;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .cr-info-icon {
            width: 19px;
            height: 19px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 950;
            border: 1px solid #bfdbfe;
        }

        .cr-popover {
            position: absolute;
            left: 0;
            top: 28px;
            z-index: 50;
            width: 280px;
            background: #0f172a;
            color: white;
            border-radius: 16px;
            padding: 13px;
            box-shadow: 0 18px 36px rgba(15,23,42,.25);
            opacity: 0;
            transform: translateY(-5px);
            visibility: hidden;
            pointer-events: none;
            transition: .16s ease;
        }

        .cr-info:hover .cr-popover,
        .cr-info:focus-within .cr-popover {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }

        .cr-popover-title {
            margin: 0;
            font-size: 13px;
            font-weight: 950;
            color: #ffffff;
        }

        .cr-popover-text {
            margin: 7px 0 0;
            color: rgba(255,255,255,.78);
            font-size: 12px;
            font-weight: 700;
            line-height: 1.55;
        }

        .cr-help {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .cr-help-item {
            border: 1px solid #dce6f2;
            background: #ffffff;
            border-radius: 18px;
            padding: 15px;
            box-shadow: 0 10px 24px rgba(15,23,42,.035);
        }

        .cr-help-title {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
        }

        .cr-help-text {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.5;
        }

        .cr-details {
            border: 1px solid #e7edf6;
            border-radius: 16px;
            background: #f8fbff;
            overflow: hidden;
            min-width: 300px;
        }

        .cr-details summary {
            list-style: none;
            cursor: pointer;
            padding: 12px 14px;
            color: #2563eb;
            font-size: 13px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .cr-details summary::-webkit-details-marker {
            display: none;
        }

        .cr-details summary::after {
            content: " +";
        }

        .cr-details[open] summary::after {
            content: " -";
        }

        .cr-details-body {
            border-top: 1px solid #e7edf6;
            padding: 14px;
            background: white;
        }

        .cr-mini-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .cr-mini-card {
            border: 1px solid #e7edf6;
            border-radius: 14px;
            padding: 12px;
            background: #ffffff;
        }

        .cr-mini-label {
            margin: 0;
            color: #64748b;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .cr-mini-value {
            margin: 5px 0 0;
            color: #0f172a;
            font-size: 15px;
            font-weight: 950;
        }

        .cr-record {
            border: 1px solid #e7edf6;
            border-radius: 14px;
            padding: 12px;
            background: #f8fbff;
            margin-bottom: 9px;
        }

        .cr-record:last-child {
            margin-bottom: 0;
        }

        .cr-empty {
            padding: 34px 20px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            font-weight: 850;
        }

        @media (max-width: 1200px) {
            .cr-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .cr-help {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .cr-mini-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .cr-hero::after {
                display: none;
            }
        }

        @media (max-width: 700px) {
            .cr-stats,
            .cr-help,
            .cr-mini-grid {
                grid-template-columns: 1fr;
            }

            .cr-title {
                font-size: 28px;
            }

            .cr-input {
                max-width: 100%;
            }

            .cr-popover {
                left: auto;
                right: 0;
                width: 250px;
            }
        }
    </style>

    <div class="cr-page">
        <section class="cr-hero">
            <div class="cr-hero-inner">
                <p class="cr-kicker">Admin Panel</p>

                <h1 class="cr-title">
                    Commission Reports
                </h1>

                <p class="cr-subtitle">
                    Monitor agent commission earnings, current balances, commission converted to load, and commission cash withdrawal requests.
                </p>
            </div>
        </section>

        <section class="cr-stats">
            <div class="cr-stat">
                <p class="cr-stat-label">Commission Earned</p>
                <p class="cr-stat-value cr-blue">₱{{ number_format($totalCommissionEarned ?? 0, 2) }}</p>
                <p class="cr-stat-note">Total 2% commission credited from player bets.</p>
            </div>

            <div class="cr-stat">
                <p class="cr-stat-label">Current Commission Balance</p>
                <p class="cr-stat-value cr-green">₱{{ number_format($totalCommissionBalance ?? 0, 2) }}</p>
                <p class="cr-stat-note">Available commission remaining across all agents.</p>
            </div>

            <div class="cr-stat">
                <p class="cr-stat-label">Converted to Load</p>
                <p class="cr-stat-value cr-purple">₱{{ number_format($totalConvertedToLoad ?? 0, 2) }}</p>
                <p class="cr-stat-note">Commission converted to agent load wallet.</p>
            </div>

            <div class="cr-stat">
                <p class="cr-stat-label">Cashout Requested</p>
                <p class="cr-stat-value cr-red">₱{{ number_format($totalCashoutRequested ?? 0, 2) }}</p>
                <p class="cr-stat-note">Total commission requested as cash.</p>
            </div>

            <div class="cr-stat">
                <p class="cr-stat-label">Pending Cashout</p>
                <p class="cr-stat-value cr-orange">₱{{ number_format($totalPendingCashout ?? 0, 2) }}</p>
                <p class="cr-stat-note">Cashout requests waiting for admin approval.</p>
            </div>

            <div class="cr-stat">
                <p class="cr-stat-label">Approved Cashout</p>
                <p class="cr-stat-value cr-green">₱{{ number_format($totalApprovedCashout ?? 0, 2) }}</p>
                <p class="cr-stat-note">Commission cashout already approved.</p>
            </div>

            <div class="cr-stat">
                <p class="cr-stat-label">Rejected Cashout</p>
                <p class="cr-stat-value cr-red">₱{{ number_format($totalRejectedCashout ?? 0, 2) }}</p>
                <p class="cr-stat-note">Rejected cashouts returned to commission balance.</p>
            </div>
        </section>

        <section class="cr-help">
            <div class="cr-help-item">
                <p class="cr-help-title">Commission Balance</p>
                <p class="cr-help-text">
                    Available commission of the agent. This can be converted to load or requested as cashout.
                </p>
            </div>

            <div class="cr-help-item">
                <p class="cr-help-title">Converted to Load</p>
                <p class="cr-help-text">
                    Amount deducted from commission balance and added to the agent wallet balance.
                </p>
            </div>

            <div class="cr-help-item">
                <p class="cr-help-title">Cashout Requested</p>
                <p class="cr-help-text">
                    Amount deducted from commission balance and sent to admin for cash withdrawal approval.
                </p>
            </div>

            <div class="cr-help-item">
                <p class="cr-help-title">Rejected Cashout</p>
                <p class="cr-help-text">
                    If rejected, the commission amount returns back to the agent commission balance.
                </p>
            </div>
        </section>

        <section class="cr-card">
            <div class="cr-card-head">
                <div>
                    <h2 class="cr-card-title">Agent Commission Summary</h2>
                    <p class="cr-card-sub">
                        Shows each agent’s commission balance, converted load amount, cashout amount, and latest records.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.commission-reports.index') }}" class="cr-filter">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="cr-input"
                    placeholder="Search agent/code/email"
                >

                <input
                    type="date"
                    name="date_from"
                    value="{{ $dateFrom }}"
                    class="cr-input"
                >

                <input
                    type="date"
                    name="date_to"
                    value="{{ $dateTo }}"
                    class="cr-input"
                >

                <button type="submit" class="cr-btn">
                    Filter
                </button>

                <a href="{{ route('admin.commission-reports.index') }}" class="cr-btn cr-btn-gray">
                    Reset
                </a>
            </form>

            <div class="cr-table-wrap">
                <table class="cr-table">
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Commission Balance</th>
                            <th>Commission Earned</th>
                            <th>Converted to Load</th>
                            <th>Cash Withdrawal</th>
                            <th>Status Breakdown</th>
                            <th>Details</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($agents as $agent)
                            @php
                                $commissionBalance = (float) ($agent->commission_balance ?? 0);
                                $walletBalance = (float) ($agent->wallet_balance ?? 0);
                                $earned = (float) ($agent->total_commission_earned ?? 0);
                                $converted = (float) ($agent->total_converted_to_load ?? 0);
                                $cashoutRequested = (float) ($agent->total_commission_cashout_requested ?? 0);
                                $pendingCashout = (float) ($agent->pending_commission_withdrawals ?? 0);
                                $approvedCashout = (float) ($agent->approved_commission_withdrawals ?? 0);
                                $rejectedCashout = (float) ($agent->rejected_commission_withdrawals ?? 0);
                                $totalUsed = $converted + $cashoutRequested;
                            @endphp

                            <tr>
                                <td>
                                    <p class="cr-name">{{ $agent->name }}</p>
                                    <p class="cr-muted">{{ $agent->email }}</p>
                                    <p class="cr-muted">Code: {{ $agent->agent_code ?? 'N/A' }}</p>
                                    <p class="cr-muted">Load Wallet: ₱{{ number_format($walletBalance, 2) }}</p>
                                </td>

                                <td>
                                    <div class="cr-info">
                                        <button type="button" class="cr-info-btn">
                                            <p class="cr-amount cr-green">
                                                ₱{{ number_format($commissionBalance, 2) }}
                                            </p>
                                            <span class="cr-info-icon">i</span>
                                        </button>

                                        <div class="cr-popover">
                                            <p class="cr-popover-title">Current Commission Balance</p>
                                            <p class="cr-popover-text">
                                                This is the available commission of the agent.
                                            </p>
                                            <p class="cr-popover-text">
                                                Agent can convert this to load or request commission cashout.
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="cr-info">
                                        <button type="button" class="cr-info-btn">
                                            <p class="cr-amount cr-blue">
                                                ₱{{ number_format($earned, 2) }}
                                            </p>
                                            <span class="cr-info-icon">i</span>
                                        </button>

                                        <div class="cr-popover">
                                            <p class="cr-popover-title">Commission Earned</p>
                                            <p class="cr-popover-text">
                                                Total 2% commission earned from player bets under this agent.
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="cr-info">
                                        <button type="button" class="cr-info-btn">
                                            <p class="cr-amount cr-purple">
                                                ₱{{ number_format($converted, 2) }}
                                            </p>
                                            <span class="cr-info-icon">i</span>
                                        </button>

                                        <div class="cr-popover">
                                            <p class="cr-popover-title">Converted to Load</p>
                                            <p class="cr-popover-text">
                                                Commission converted into the agent's normal load wallet.
                                            </p>
                                            <p class="cr-popover-text">
                                                This increases wallet balance and decreases commission balance.
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="cr-info">
                                        <button type="button" class="cr-info-btn">
                                            <p class="cr-amount cr-red">
                                                ₱{{ number_format($cashoutRequested, 2) }}
                                            </p>
                                            <span class="cr-info-icon">i</span>
                                        </button>

                                        <div class="cr-popover">
                                            <p class="cr-popover-title">Cash Withdrawal Request</p>
                                            <p class="cr-popover-text">
                                                Commission requested by agent as cash withdrawal.
                                            </p>
                                            <p class="cr-popover-text">
                                                This is deducted from commission balance while pending.
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <p class="cr-muted">
                                        <span class="cr-pill orange">Pending</span>
                                        ₱{{ number_format($pendingCashout, 2) }}
                                    </p>

                                    <p class="cr-muted">
                                        <span class="cr-pill green">Approved</span>
                                        ₱{{ number_format($approvedCashout, 2) }}
                                    </p>

                                    <p class="cr-muted">
                                        <span class="cr-pill red">Rejected</span>
                                        ₱{{ number_format($rejectedCashout, 2) }}
                                    </p>
                                </td>

                                <td>
                                    <details class="cr-details">
                                        <summary>View Details</summary>

                                        <div class="cr-details-body">
                                            <div class="cr-mini-grid">
                                                <div class="cr-mini-card">
                                                    <p class="cr-mini-label">Load Wallet</p>
                                                    <p class="cr-mini-value">₱{{ number_format($walletBalance, 2) }}</p>
                                                </div>

                                                <div class="cr-mini-card">
                                                    <p class="cr-mini-label">Commission Balance</p>
                                                    <p class="cr-mini-value">₱{{ number_format($commissionBalance, 2) }}</p>
                                                </div>

                                                <div class="cr-mini-card">
                                                    <p class="cr-mini-label">Total Earned</p>
                                                    <p class="cr-mini-value">₱{{ number_format($earned, 2) }}</p>
                                                </div>

                                                <div class="cr-mini-card">
                                                    <p class="cr-mini-label">Converted to Load</p>
                                                    <p class="cr-mini-value">₱{{ number_format($converted, 2) }}</p>
                                                </div>

                                                <div class="cr-mini-card">
                                                    <p class="cr-mini-label">Cashout Requested</p>
                                                    <p class="cr-mini-value">₱{{ number_format($cashoutRequested, 2) }}</p>
                                                </div>

                                                <div class="cr-mini-card">
                                                    <p class="cr-mini-label">Total Used</p>
                                                    <p class="cr-mini-value">₱{{ number_format($totalUsed, 2) }}</p>
                                                </div>

                                                <div class="cr-mini-card">
                                                    <p class="cr-mini-label">Pending Cashout</p>
                                                    <p class="cr-mini-value">₱{{ number_format($pendingCashout, 2) }}</p>
                                                </div>

                                                <div class="cr-mini-card">
                                                    <p class="cr-mini-label">Approved Cashout</p>
                                                    <p class="cr-mini-value">₱{{ number_format($approvedCashout, 2) }}</p>
                                                </div>

                                                <div class="cr-mini-card">
                                                    <p class="cr-mini-label">Rejected Cashout</p>
                                                    <p class="cr-mini-value">₱{{ number_format($rejectedCashout, 2) }}</p>
                                                </div>
                                            </div>

                                            <p class="cr-name" style="margin-bottom:10px;">
                                                Latest Commission Transactions
                                            </p>

                                            @forelse($agent->latest_commission_transactions as $transaction)
                                                @php
                                                    $direction = strtolower($transaction->direction ?? 'default');
                                                    $pillClass = $direction === 'credit' ? 'green' : ($direction === 'debit' ? 'red' : 'gray');

                                                    if ($transaction->type === 'convert_to_load') {
                                                        $pillClass = 'purple';
                                                    }

                                                    if ($transaction->type === 'commission_withdrawal_request') {
                                                        $pillClass = 'red';
                                                    }
                                                @endphp

                                                <div class="cr-record">
                                                    <span class="cr-pill {{ $pillClass }}">
                                                        {{ strtoupper(str_replace('_', ' ', $transaction->type)) }}
                                                    </span>

                                                    <p class="cr-muted">
                                                        {{ strtoupper($transaction->direction) }}
                                                        —
                                                        ₱{{ number_format($transaction->amount, 2) }}
                                                    </p>

                                                    <p class="cr-muted">
                                                        Before: ₱{{ number_format($transaction->balance_before, 2) }}
                                                        |
                                                        After: ₱{{ number_format($transaction->balance_after, 2) }}
                                                    </p>

                                                    <p class="cr-muted">
                                                        {{ $transaction->description ?: 'No description' }}
                                                    </p>

                                                    <p class="cr-muted">
                                                        {{ $transaction->created_at?->format('M d, Y h:i A') }}
                                                    </p>
                                                </div>
                                            @empty
                                                <p class="cr-muted">
                                                    No commission transaction records yet.
                                                </p>
                                            @endforelse

                                            <p class="cr-name" style="margin:16px 0 10px;">
                                                Latest Cashout Requests
                                            </p>

                                            @forelse($agent->latest_commission_withdrawals as $withdrawal)
                                                @php
                                                    $status = strtolower($withdrawal->status ?? 'pending');

                                                    $statusClass = match ($status) {
                                                        'approved' => 'green',
                                                        'rejected' => 'red',
                                                        'pending' => 'orange',
                                                        default => 'gray',
                                                    };
                                                @endphp

                                                <div class="cr-record">
                                                    <span class="cr-pill {{ $statusClass }}">
                                                        {{ strtoupper($withdrawal->status) }}
                                                    </span>

                                                    <p class="cr-muted">
                                                        Amount: ₱{{ number_format($withdrawal->amount, 2) }}
                                                    </p>

                                                    <p class="cr-muted">
                                                        Method: {{ $withdrawal->payment_method }}
                                                    </p>

                                                    <p class="cr-muted">
                                                        Account: {{ $withdrawal->account_name }} / {{ $withdrawal->account_number }}
                                                    </p>

                                                    <p class="cr-muted">
                                                        Requested: {{ $withdrawal->created_at?->format('M d, Y h:i A') }}
                                                    </p>

                                                    @if($withdrawal->reviewed_at)
                                                        <p class="cr-muted">
                                                            Reviewed: {{ $withdrawal->reviewed_at?->format('M d, Y h:i A') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @empty
                                                <p class="cr-muted">
                                                    No cashout request records yet.
                                                </p>
                                            @endforelse
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="cr-empty">
                                        No agent commission records found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px;">
                {{ $agents->links() }}
            </div>
        </section>
    </div>
</x-layouts.app>