<x-layouts.app :title="__('Agent Commissions')">
    <style>
        .ac-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .ac-hero {
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

        .ac-hero::after {
            content: "💰";
            position: absolute;
            right: 46px;
            top: 12px;
            font-size: 110px;
            transform: rotate(-8deg);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.28));
        }

        .ac-hero-inner {
            position: relative;
            z-index: 2;
            max-width: 850px;
        }

        .ac-kicker {
            margin: 0;
            color: #86efac;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .ac-title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .ac-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.78);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
        }

        .ac-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 850;
        }

        .ac-alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .ac-alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .ac-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .ac-stat {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .ac-stat-label {
            margin: 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .ac-stat-value {
            margin: 8px 0 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .ac-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 18px;
            align-items: start;
        }

        .ac-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .ac-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .ac-card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .ac-form {
            margin-top: 16px;
            display: grid;
            gap: 12px;
        }

        .ac-label {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-size: 13px;
            font-weight: 900;
        }

        .ac-input {
            width: 100%;
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

        .ac-textarea {
            width: 100%;
            min-height: 90px;
            border-radius: 14px;
            border: 1px solid #dce6f2;
            background: #ffffff;
            padding: 12px 14px;
            outline: none;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            resize: vertical;
        }

        .ac-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ac-btn {
            min-height: 46px;
            border: 0;
            border-radius: 14px;
            padding: 0 16px;
            color: white;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .ac-btn-green {
            background: #16a34a;
        }

        .ac-btn-blue {
            background: #2563eb;
        }

        .ac-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .ac-filter .ac-input {
            max-width: 190px;
        }

        .ac-table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e7edf6;
            border-radius: 18px;
            margin-top: 16px;
        }

        .ac-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .ac-table thead {
            background: #f8fbff;
        }

        .ac-table th {
            padding: 14px;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
            border-bottom: 1px solid #e7edf6;
            white-space: nowrap;
        }

        .ac-table td {
            padding: 15px 14px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: top;
        }

        .ac-amount {
            margin: 0;
            color: #0f172a;
            font-size: 15px;
            font-weight: 950;
            white-space: nowrap;
        }

        .ac-muted {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.45;
        }

        .ac-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .ac-pill.credit,
        .ac-pill.approved {
            background: #dcfce7;
            color: #15803d;
        }

        .ac-pill.debit,
        .ac-pill.rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .ac-pill.pending {
            background: #fef3c7;
            color: #b45309;
        }

        .ac-empty {
            padding: 30px 20px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            font-weight: 850;
        }

        @media (max-width: 1100px) {
            .ac-stats,
            .ac-grid {
                grid-template-columns: 1fr;
            }

            .ac-hero::after {
                display: none;
            }

            .ac-title {
                font-size: 28px;
            }
        }
    </style>

    <div class="ac-page">
        <section class="ac-hero">
            <div class="ac-hero-inner">
                <p class="ac-kicker">Agent Panel</p>

                <h1 class="ac-title">
                    Commission Wallet
                </h1>

                <p class="ac-subtitle">
                    Track your commission earnings, convert commission to load wallet, or request commission cash withdrawal.
                </p>
            </div>
        </section>

        @if(session('success'))
            <div class="ac-alert ac-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="ac-alert ac-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="ac-stats">
            <div class="ac-stat">
                <p class="ac-stat-label">Commission Balance</p>
                <p class="ac-stat-value">₱{{ number_format($agent->commission_balance ?? 0, 2) }}</p>
            </div>

            <div class="ac-stat">
                <p class="ac-stat-label">Load Wallet</p>
                <p class="ac-stat-value">₱{{ number_format($agent->wallet_balance ?? 0, 2) }}</p>
            </div>

            <div class="ac-stat">
                <p class="ac-stat-label">Player Bets</p>
                <p class="ac-stat-value">₱{{ number_format($totalPlayerBets ?? 0, 2) }}</p>
            </div>

            <div class="ac-stat">
                <p class="ac-stat-label">Computed 2%</p>
                <p class="ac-stat-value">₱{{ number_format($computedCommission ?? 0, 2) }}</p>
            </div>
        </section>

        <section class="ac-grid">
            <div class="ac-card">
                <h2 class="ac-card-title">Commission Report</h2>
                <p class="ac-card-sub">
                    Filter by date to check player bet volume and estimated commission.
                </p>

                <form method="GET" action="{{ route('agent.commissions.index') }}" class="ac-filter">
                    <input
                        type="date"
                        name="date_from"
                        value="{{ $dateFrom }}"
                        class="ac-input"
                    >

                    <input
                        type="date"
                        name="date_to"
                        value="{{ $dateTo }}"
                        class="ac-input"
                    >

                    <button type="submit" class="ac-btn ac-btn-blue">
                        Filter
                    </button>

                    <a
                        href="{{ route('agent.commissions.index') }}"
                        class="ac-btn"
                        style="background:#64748b;text-decoration:none;display:inline-flex;align-items:center;"
                    >
                        Reset
                    </a>
                </form>

                <div class="ac-table-wrap">
                    <table class="ac-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Direction</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <p class="ac-amount">
                                            {{ $transaction->created_at?->format('M d, Y') }}
                                        </p>

                                        <p class="ac-muted">
                                            {{ $transaction->created_at?->format('h:i A') }}
                                        </p>
                                    </td>

                                    <td>
                                        <p class="ac-amount">
                                            {{ strtoupper(str_replace('_', ' ', $transaction->type)) }}
                                        </p>
                                    </td>

                                    <td>
                                        <span class="ac-pill {{ $transaction->direction }}">
                                            {{ $transaction->direction }}
                                        </span>
                                    </td>

                                    <td>
                                        <p class="ac-amount">
                                            ₱{{ number_format($transaction->amount, 2) }}
                                        </p>
                                    </td>

                                    <td>
                                        <p class="ac-muted">
                                            Before: ₱{{ number_format($transaction->balance_before, 2) }}
                                        </p>

                                        <p class="ac-muted">
                                            After: ₱{{ number_format($transaction->balance_after, 2) }}
                                        </p>
                                    </td>

                                    <td>
                                        <p class="ac-muted">
                                            {{ $transaction->description ?: 'No description' }}
                                        </p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="ac-empty">
                                            No commission transactions yet.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="display:grid;gap:18px;">
                <div class="ac-card">
                    <h2 class="ac-card-title">Convert Commission to Load</h2>
                    <p class="ac-card-sub">
                        Move commission balance into your normal load wallet.
                    </p>

                    <form method="POST" action="{{ route('agent.commissions.convert-to-load') }}" class="ac-form">
                        @csrf

                        <div>
                            <label class="ac-label">Amount</label>
                            <input
                                type="number"
                                name="amount"
                                min="1"
                                step="0.01"
                                class="ac-input"
                                placeholder="Enter amount"
                                required
                            >
                        </div>

                        <button type="submit" class="ac-btn ac-btn-green">
                            Convert to Load
                        </button>
                    </form>
                </div>

                <div class="ac-card">
                    <h2 class="ac-card-title">Withdraw Commission to Cash</h2>
                    <p class="ac-card-sub">
                        Request admin approval to cash out your commission.
                    </p>

                    <form method="POST" action="{{ route('agent.commissions.withdraw-cash') }}" class="ac-form">
                        @csrf

                        <div>
                            <label class="ac-label">Amount</label>
                            <input
                                type="number"
                                name="amount"
                                min="1"
                                step="0.01"
                                class="ac-input"
                                placeholder="Enter amount"
                                required
                            >
                        </div>

                        <div>
                            <label class="ac-label">Payment Method</label>
                            <input
                                type="text"
                                name="payment_method"
                                class="ac-input"
                                placeholder="GCash / Maya / Bank"
                                required
                            >
                        </div>

                        <div>
                            <label class="ac-label">Account Name</label>
                            <input
                                type="text"
                                name="account_name"
                                class="ac-input"
                                placeholder="Account name"
                                required
                            >
                        </div>

                        <div>
                            <label class="ac-label">Account Number</label>
                            <input
                                type="text"
                                name="account_number"
                                class="ac-input"
                                placeholder="Account number"
                                required
                            >
                        </div>

                        <div>
                            <label class="ac-label">Notes</label>
                            <textarea
                                name="notes"
                                class="ac-textarea"
                                placeholder="Optional notes"
                            ></textarea>
                        </div>

                        <button type="submit" class="ac-btn ac-btn-blue">
                            Request Cashout
                        </button>
                    </form>
                </div>

                <div class="ac-card">
                    <h2 class="ac-card-title">Cashout Requests</h2>
                    <p class="ac-card-sub">
                        Your latest commission withdrawal requests.
                    </p>

                    <div class="ac-table-wrap">
                        <table class="ac-table" style="min-width:620px;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($withdrawals as $withdrawal)
                                    <tr>
                                        <td>
                                            <p class="ac-amount">
                                                {{ $withdrawal->created_at?->format('M d, Y') }}
                                            </p>
                                        </td>

                                        <td>
                                            <p class="ac-amount">
                                                ₱{{ number_format($withdrawal->amount, 2) }}
                                            </p>
                                        </td>

                                        <td>
                                            <p class="ac-muted">
                                                {{ $withdrawal->payment_method }}
                                            </p>

                                            <p class="ac-muted">
                                                {{ $withdrawal->account_number }}
                                            </p>
                                        </td>

                                        <td>
                                            <span class="ac-pill {{ strtolower($withdrawal->status) }}">
                                                {{ $withdrawal->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="ac-empty">
                                                No cashout requests yet.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>