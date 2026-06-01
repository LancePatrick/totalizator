<x-layouts.app :title="__('Agent Requests')">
    <style>
        .ar-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .ar-hero {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            padding: 28px;
            color: white;
            background:
                radial-gradient(circle at 82% 45%, rgba(29,124,255,.65), transparent 26%),
                radial-gradient(circle at 18% 20%, rgba(250,204,21,.16), transparent 22%),
                linear-gradient(135deg, #03142f 0%, #041a4d 52%, #0848b9 100%);
            box-shadow: 0 18px 42px rgba(2,18,54,.18);
        }

        .ar-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 54px 54px;
            opacity: .45;
        }

        .ar-hero::after {
            content: "💼";
            position: absolute;
            right: 46px;
            top: 14px;
            font-size: 112px;
            transform: rotate(-8deg);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.30));
        }

        .ar-hero-inner {
            position: relative;
            z-index: 2;
            max-width: 860px;
        }

        .ar-kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .ar-title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .ar-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.74);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
        }

        .ar-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 850;
        }

        .ar-alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .ar-alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .ar-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .ar-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .ar-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .ar-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .ar-card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .ar-count-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            border-radius: 999px;
            padding: 0 13px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .ar-table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e7edf6;
            border-radius: 18px;
        }

        .ar-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .ar-table thead {
            background: #f8fbff;
        }

        .ar-table th {
            padding: 14px;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
            border-bottom: 1px solid #e7edf6;
            white-space: nowrap;
        }

        .ar-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: top;
        }

        .ar-table tbody tr:hover {
            background: #f8fbff;
        }

        .ar-player {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
        }

        .ar-amount {
            margin: 0;
            color: #0f172a;
            font-size: 16px;
            font-weight: 950;
            white-space: nowrap;
        }

        .ar-muted {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.45;
        }

        .ar-status {
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

        .ar-status.pending {
            background: #fef3c7;
            color: #b45309;
        }

        .ar-status.approved {
            background: #dcfce7;
            color: #15803d;
        }

        .ar-status.rejected,
        .ar-status.disapproved {
            background: #fee2e2;
            color: #dc2626;
        }

        .ar-status.default {
            background: #f1f5f9;
            color: #475569;
        }

        .ar-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            min-width: 170px;
        }

        .ar-btn {
            min-height: 38px;
            border: 0;
            border-radius: 12px;
            padding: 0 13px;
            color: white;
            font-size: 12px;
            font-weight: 950;
            cursor: pointer;
            text-transform: uppercase;
            transition: .16s ease;
        }

        .ar-btn:hover {
            transform: translateY(-1px);
            filter: brightness(.96);
        }

        .ar-btn-approve {
            background: #16a34a;
        }

        .ar-btn-reject {
            background: #dc2626;
        }

        .ar-reviewed {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 850;
        }

        .ar-empty {
            padding: 34px 20px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            font-weight: 850;
        }

        .ar-payment {
            margin: 0;
            color: #334155;
            font-size: 13px;
            font-weight: 850;
            line-height: 1.45;
        }

        @media (max-width: 900px) {
            .ar-hero::after {
                display: none;
            }

            .ar-card-head {
                flex-direction: column;
            }

            .ar-title {
                font-size: 28px;
            }
        }
    </style>

    <div class="ar-page">
        <section class="ar-hero">
            <div class="ar-hero-inner">
                <p class="ar-kicker">Agent Panel</p>

                <h1 class="ar-title">
                    Player Requests
                </h1>

                <p class="ar-subtitle">
                    Approve or reject player money requests and withdrawal requests.
                </p>
            </div>
        </section>

        @if(session('success'))
            <div class="ar-alert ar-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="ar-alert ar-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="ar-grid">
            <div class="ar-card">
                <div class="ar-card-head">
                    <div>
                        <h2 class="ar-card-title">Money Requests</h2>
                        <p class="ar-card-sub">
                            Player requests to add wallet balance through their assigned agent.
                        </p>
                    </div>

                    <span class="ar-count-pill">
                        {{ $moneyRequests->count() }} Requests
                    </span>
                </div>

                <div class="ar-table-wrap">
                    <table class="ar-table">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($moneyRequests as $requestMoney)
                                @php
                                    $moneyStatus = strtolower($requestMoney->status ?? 'default');

                                    if (!in_array($moneyStatus, ['pending', 'approved', 'rejected', 'disapproved'])) {
                                        $moneyStatus = 'default';
                                    }

                                    $moneyPlayer = $requestMoney->user ?? $requestMoney->player ?? null;
                                @endphp

                                <tr>
                                    <td>
                                        <p class="ar-player">
                                            {{ $moneyPlayer?->name ?? 'Unknown Player' }}
                                        </p>

                                        <p class="ar-muted">
                                            {{ $moneyPlayer?->email ?? 'No email' }}
                                        </p>
                                    </td>

                                    <td>
                                        <p class="ar-amount">
                                            ₱{{ number_format($requestMoney->amount ?? 0, 2) }}
                                        </p>
                                    </td>

                                    <td>
                                        <span class="ar-status {{ $moneyStatus }}">
                                            {{ strtoupper($requestMoney->status ?? 'N/A') }}
                                        </span>
                                    </td>

                                    <td>
                                        <p class="ar-muted">
                                            {{ $requestMoney->notes ?: 'No notes' }}
                                        </p>
                                    </td>

                                    <td>
                                        @if($requestMoney->status === 'pending')
                                            <div class="ar-actions">
                                                <form method="POST" action="{{ route('agent.requests.money.approve', $requestMoney) }}">
                                                    @csrf

                                                    <button type="submit" class="ar-btn ar-btn-approve">
                                                        Approve
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('agent.requests.money.reject', $requestMoney) }}">
                                                    @csrf

                                                    <button type="submit" class="ar-btn ar-btn-reject">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="ar-reviewed">
                                                Reviewed
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="ar-empty">
                                            No money requests.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="ar-card">
                <div class="ar-card-head">
                    <div>
                        <h2 class="ar-card-title">Withdrawal Requests</h2>
                        <p class="ar-card-sub">
                            Review player withdrawals and payment details.
                        </p>
                    </div>

                    <span class="ar-count-pill">
                        {{ $withdrawals->count() }} Requests
                    </span>
                </div>

                <div class="ar-table-wrap">
                    <table class="ar-table">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($withdrawals as $withdrawal)
                                @php
                                    $withdrawalStatus = strtolower($withdrawal->status ?? 'default');

                                    if (!in_array($withdrawalStatus, ['pending', 'approved', 'rejected', 'disapproved'])) {
                                        $withdrawalStatus = 'default';
                                    }

                                    $withdrawalPlayer = $withdrawal->user ?? $withdrawal->player ?? null;
                                @endphp

                                <tr>
                                    <td>
                                        <p class="ar-player">
                                            {{ $withdrawalPlayer?->name ?? 'Unknown Player' }}
                                        </p>

                                        <p class="ar-muted">
                                            {{ $withdrawalPlayer?->email ?? 'No email' }}
                                        </p>
                                    </td>

                                    <td>
                                        <p class="ar-amount">
                                            ₱{{ number_format($withdrawal->amount ?? 0, 2) }}
                                        </p>
                                    </td>

                                    <td>
                                        <p class="ar-payment">
                                            {{ $withdrawal->payment_method ?: 'N/A' }}
                                        </p>

                                        <p class="ar-muted">
                                            Account Name: {{ $withdrawal->account_name ?: 'N/A' }}
                                        </p>

                                        <p class="ar-muted">
                                            Account No: {{ $withdrawal->account_number ?: 'N/A' }}
                                        </p>
                                    </td>

                                    <td>
                                        <span class="ar-status {{ $withdrawalStatus }}">
                                            {{ strtoupper($withdrawal->status ?? 'N/A') }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($withdrawal->status === 'pending')
                                            <div class="ar-actions">
                                                <form method="POST" action="{{ route('agent.requests.withdrawals.approve', $withdrawal) }}">
                                                    @csrf

                                                    <button type="submit" class="ar-btn ar-btn-approve">
                                                        Approve
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('agent.requests.withdrawals.reject', $withdrawal) }}">
                                                    @csrf

                                                    <button type="submit" class="ar-btn ar-btn-reject">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="ar-reviewed">
                                                Reviewed
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="ar-empty">
                                            No withdrawal requests.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>