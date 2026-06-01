<x-layouts.app :title="__('Commission Withdrawals')">
    <style>
        .cw-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .cw-hero {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            padding: 28px;
            color: white;
            background:
                radial-gradient(circle at 82% 45%, rgba(250,204,21,.52), transparent 26%),
                radial-gradient(circle at 18% 20%, rgba(59,130,246,.22), transparent 22%),
                linear-gradient(135deg, #03142f 0%, #422006 52%, #ca8a04 100%);
            box-shadow: 0 18px 42px rgba(2,18,54,.18);
        }

        .cw-hero::after {
            content: "🏦";
            position: absolute;
            right: 46px;
            top: 14px;
            font-size: 110px;
            transform: rotate(-8deg);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.28));
        }

        .cw-hero-inner {
            position: relative;
            z-index: 2;
            max-width: 860px;
        }

        .cw-kicker {
            margin: 0;
            color: #fde68a;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .cw-title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .cw-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.78);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
        }

        .cw-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 850;
        }

        .cw-alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .cw-alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .cw-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .cw-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
        }

        .cw-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .cw-card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .cw-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            border-radius: 999px;
            padding: 0 13px;
            background: #fffbeb;
            color: #ca8a04;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .cw-table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e7edf6;
            border-radius: 18px;
        }

        .cw-table {
            width: 100%;
            min-width: 1050px;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .cw-table thead {
            background: #f8fbff;
        }

        .cw-table th {
            padding: 14px;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
            border-bottom: 1px solid #e7edf6;
            white-space: nowrap;
        }

        .cw-table td {
            padding: 15px 14px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: top;
        }

        .cw-name {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
        }

        .cw-muted {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.45;
        }

        .cw-amount {
            margin: 0;
            color: #0f172a;
            font-size: 16px;
            font-weight: 950;
            white-space: nowrap;
        }

        .cw-pill {
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

        .cw-pill.pending {
            background: #fef3c7;
            color: #b45309;
        }

        .cw-pill.approved {
            background: #dcfce7;
            color: #15803d;
        }

        .cw-pill.rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .cw-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            min-width: 170px;
        }

        .cw-btn {
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

        .cw-btn-green {
            background: #16a34a;
        }

        .cw-btn-red {
            background: #dc2626;
        }

        .cw-reviewed {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 850;
        }

        .cw-empty {
            padding: 34px 20px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            font-weight: 850;
        }

        @media (max-width: 900px) {
            .cw-hero::after {
                display: none;
            }

            .cw-card-head {
                flex-direction: column;
            }

            .cw-title {
                font-size: 28px;
            }
        }
    </style>

    <div class="cw-page">
        <section class="cw-hero">
            <div class="cw-hero-inner">
                <p class="cw-kicker">Admin Panel</p>

                <h1 class="cw-title">
                    Commission Withdrawals
                </h1>

                <p class="cw-subtitle">
                    Review and approve or reject agent commission cashout requests.
                </p>
            </div>
        </section>

        @if(session('success'))
            <div class="cw-alert cw-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="cw-alert cw-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="cw-card">
            <div class="cw-card-head">
                <div>
                    <h2 class="cw-card-title">Requests</h2>
                    <p class="cw-card-sub">
                        Pending requests are deducted from agent commission balance while waiting for admin review.
                    </p>
                </div>

                <span class="cw-count">
                    {{ $withdrawals->total() }} Requests
                </span>
            </div>

            <div class="cw-table-wrap">
                <table class="cw-table">
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th>Reviewed</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($withdrawals as $withdrawal)
                            @php
                                $status = strtolower($withdrawal->status ?? 'pending');
                            @endphp

                            <tr>
                                <td>
                                    <p class="cw-name">
                                        {{ $withdrawal->agent?->name ?? 'Unknown Agent' }}
                                    </p>

                                    <p class="cw-muted">
                                        {{ $withdrawal->agent?->email ?? 'No email' }}
                                    </p>

                                    <p class="cw-muted">
                                        Code: {{ $withdrawal->agent?->agent_code ?? 'N/A' }}
                                    </p>
                                </td>

                                <td>
                                    <p class="cw-amount">
                                        ₱{{ number_format($withdrawal->amount ?? 0, 2) }}
                                    </p>
                                </td>

                                <td>
                                    <p class="cw-name">
                                        {{ $withdrawal->payment_method }}
                                    </p>

                                    <p class="cw-muted">
                                        Name: {{ $withdrawal->account_name }}
                                    </p>

                                    <p class="cw-muted">
                                        No: {{ $withdrawal->account_number }}
                                    </p>

                                    <p class="cw-muted">
                                        {{ $withdrawal->notes ?: 'No notes' }}
                                    </p>
                                </td>

                                <td>
                                    <span class="cw-pill {{ $status }}">
                                        {{ $withdrawal->status }}
                                    </span>
                                </td>

                                <td>
                                    <p class="cw-name">
                                        {{ $withdrawal->created_at?->format('M d, Y') }}
                                    </p>

                                    <p class="cw-muted">
                                        {{ $withdrawal->created_at?->format('h:i A') }}
                                    </p>
                                </td>

                                <td>
                                    @if($withdrawal->reviewed_at)
                                        <p class="cw-name">
                                            {{ $withdrawal->reviewed_at?->format('M d, Y') }}
                                        </p>

                                        <p class="cw-muted">
                                            {{ $withdrawal->reviewed_at?->format('h:i A') }}
                                        </p>
                                    @else
                                        <p class="cw-muted">
                                            Not reviewed yet
                                        </p>
                                    @endif
                                </td>

                                <td>
                                    @if($withdrawal->status === 'pending')
                                        <div class="cw-actions">
                                            <form method="POST" action="{{ route('admin.commission-withdrawals.approve', $withdrawal) }}">
                                                @csrf

                                                <button type="submit" class="cw-btn cw-btn-green">
                                                    Approve
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.commission-withdrawals.reject', $withdrawal) }}">
                                                @csrf

                                                <button type="submit" class="cw-btn cw-btn-red">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="cw-reviewed">
                                            Reviewed
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="cw-empty">
                                        No commission withdrawal requests yet.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px;">
                {{ $withdrawals->links() }}
            </div>
        </section>
    </div>
</x-layouts.app>