<x-layouts.app :title="__('Player Wallet')">
    @php
        $user = auth()->user();
        $wallet = number_format($user->wallet_balance ?? 0, 2);
        $agentName = $user->assignedAgentName();
    @endphp

    <style>
        .wallet-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .wallet-hero {
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

        .wallet-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 54px 54px;
            opacity: .45;
        }

        .wallet-hero::after {
            content: "💰";
            position: absolute;
            right: 44px;
            top: 16px;
            font-size: 118px;
            transform: rotate(-10deg);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.30));
        }

        .wallet-hero-inner {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 24px;
            align-items: center;
        }

        .wallet-kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .wallet-title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .wallet-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.74);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
        }

        .wallet-subtitle span {
            color: #38bdf8;
            font-weight: 950;
        }

        .wallet-balance-box {
            border-radius: 18px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            padding: 18px;
            backdrop-filter: blur(10px);
        }

        .wallet-label {
            margin: 0;
            color: rgba(255,255,255,.65);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .wallet-value {
            margin: 8px 0 0;
            color: #facc15;
            font-size: 36px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .wallet-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 850;
        }

        .wallet-alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .wallet-alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .wallet-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .wallet-stat-card {
            min-height: 112px;
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .wallet-stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 950;
            flex-shrink: 0;
        }

        .wallet-stat-label {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 850;
        }

        .wallet-stat-value {
            margin: 6px 0 0;
            color: #172554;
            font-size: 24px;
            line-height: 1.1;
            font-weight: 950;
        }

        .wallet-stat-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
        }

        .wallet-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            align-items: start;
        }

        .wallet-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .wallet-card-head {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
        }

        .wallet-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 950;
            flex-shrink: 0;
        }

        .wallet-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .wallet-card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .wallet-form {
            display: grid;
            gap: 12px;
        }

        .wallet-input,
        .wallet-textarea {
            width: 100%;
            border-radius: 14px;
            border: 1px solid #dce6f2;
            background: #ffffff;
            padding: 0 14px;
            outline: none;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            transition: .16s ease;
        }

        .wallet-input {
            height: 48px;
        }

        .wallet-textarea {
            min-height: 100px;
            padding-top: 12px;
            resize: vertical;
        }

        .wallet-input:focus,
        .wallet-textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.12);
        }

        .wallet-btn {
            height: 48px;
            border: 0;
            border-radius: 14px;
            color: white;
            font-size: 14px;
            font-weight: 950;
            cursor: pointer;
            transition: .16s ease;
        }

        .wallet-btn:hover {
            transform: translateY(-1px);
            filter: brightness(.96);
        }

        .wallet-btn-request {
            background: #2563eb;
            box-shadow: 0 12px 24px rgba(37,99,235,.18);
        }

        .wallet-btn-withdraw {
            background: #7c3aed;
            box-shadow: 0 12px 24px rgba(124,58,237,.18);
        }

        .history-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .history-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 14px;
        }

        .history-item {
            border: 1px solid #e7edf6;
            border-radius: 14px;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            background: #ffffff;
        }

        .history-amount {
            margin: 0;
            color: #0f172a;
            font-size: 16px;
            font-weight: 950;
        }

        .history-date {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .history-status {
            border-radius: 999px;
            padding: 7px 10px;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            background: #f1f5f9;
            color: #334155;
            white-space: nowrap;
        }

        .history-status.pending {
            background: #fef3c7;
            color: #b45309;
        }

        .history-status.approved {
            background: #dcfce7;
            color: #15803d;
        }

        .history-status.rejected,
        .history-status.disapproved {
            background: #fee2e2;
            color: #dc2626;
        }

        .history-meta {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .wallet-empty {
            border-radius: 16px;
            padding: 22px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            font-weight: 800;
        }

        @media (max-width: 1200px) {
            .wallet-grid,
            .history-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .wallet-hero-inner,
            .wallet-stats {
                grid-template-columns: 1fr;
            }

            .wallet-hero::after {
                display: none;
            }
        }
    </style>

    <div class="wallet-page">
        <section class="wallet-hero">
            <div class="wallet-hero-inner">
                <div>
                    <p class="wallet-kicker">Player Wallet</p>

                    <h1 class="wallet-title">
                        Manage Your Balance
                    </h1>

                    <p class="wallet-subtitle">
                        Request funds from your assigned agent
                        <span>{{ $agentName }}</span>
                        or submit a withdrawal request.
                    </p>
                </div>

                <div class="wallet-balance-box">
                    <p class="wallet-label">Wallet Balance</p>
                    <h2 class="wallet-value">₱{{ $wallet }}</h2>
                    <p class="wallet-subtitle" style="margin-top:8px;">Available balance</p>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="wallet-alert wallet-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="wallet-alert wallet-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="wallet-stats">
            <div class="wallet-stat-card">
                <div class="wallet-stat-icon" style="background:linear-gradient(135deg,#4f46e5,#3b82f6);">
                    ₱
                </div>

                <div>
                    <p class="wallet-stat-label">Current Balance</p>
                    <h3 class="wallet-stat-value">₱{{ $wallet }}</h3>
                    <p class="wallet-stat-sub">Available funds</p>
                </div>
            </div>

            <div class="wallet-stat-card">
                <div class="wallet-stat-icon" style="background:linear-gradient(135deg,#22c55e,#10b981);">
                    ⇩
                </div>

                <div>
                    <p class="wallet-stat-label">Money Requests</p>
                    <h3 class="wallet-stat-value">{{ $moneyRequests->count() }}</h3>
                    <p class="wallet-stat-sub">Recent request records</p>
                </div>
            </div>

            <div class="wallet-stat-card">
                <div class="wallet-stat-icon" style="background:linear-gradient(135deg,#7c3aed,#a855f7);">
                    ↥
                </div>

                <div>
                    <p class="wallet-stat-label">Withdrawals</p>
                    <h3 class="wallet-stat-value">{{ $withdrawals->count() }}</h3>
                    <p class="wallet-stat-sub">Recent withdrawal records</p>
                </div>
            </div>
        </section>

        <section class="wallet-grid">
            <div class="wallet-card">
                <div class="wallet-card-head">
                    <div class="wallet-card-icon" style="background:#eff6ff;color:#2563eb;">
                        ⇩
                    </div>

                    <div>
                        <h2 class="wallet-card-title">Request Money</h2>
                        <p class="wallet-card-sub">
                            Ask your assigned agent to add balance to your wallet.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('player.wallet.request-money') }}" class="wallet-form">
                    @csrf

                    <input
                        type="number"
                        step="0.01"
                        min="1"
                        name="amount"
                        required
                        placeholder="Amount"
                        class="wallet-input"
                    >

                    <textarea
                        name="notes"
                        rows="3"
                        placeholder="Notes optional"
                        class="wallet-textarea"
                    ></textarea>

                    <button type="submit" class="wallet-btn wallet-btn-request">
                        Submit Money Request
                    </button>
                </form>
            </div>

            <div class="wallet-card">
                <div class="wallet-card-head">
                    <div class="wallet-card-icon" style="background:#f5f3ff;color:#7c3aed;">
                        ↥
                    </div>

                    <div>
                        <h2 class="wallet-card-title">Withdraw Money</h2>
                        <p class="wallet-card-sub">
                            Submit withdrawal details for agent/admin approval.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('player.wallet.withdraw') }}" class="wallet-form">
                    @csrf

                    <input
                        type="number"
                        step="0.01"
                        min="1"
                        name="amount"
                        required
                        placeholder="Amount"
                        class="wallet-input"
                    >

                    <input
                        name="payment_method"
                        required
                        placeholder="Payment Method e.g. GCash"
                        class="wallet-input"
                    >

                    <input
                        name="account_name"
                        required
                        placeholder="Account Name"
                        class="wallet-input"
                    >

                    <input
                        name="account_number"
                        required
                        placeholder="Account Number"
                        class="wallet-input"
                    >

                    <textarea
                        name="notes"
                        rows="3"
                        placeholder="Notes optional"
                        class="wallet-textarea"
                    ></textarea>

                    <button type="submit" class="wallet-btn wallet-btn-withdraw">
                        Submit Withdrawal
                    </button>
                </form>
            </div>
        </section>

        <section class="history-grid">
            <div class="wallet-card">
                <div class="wallet-card-head">
                    <div class="wallet-card-icon" style="background:#eff6ff;color:#2563eb;">
                        ☷
                    </div>

                    <div>
                        <h2 class="wallet-card-title">Money Request History</h2>
                        <p class="wallet-card-sub">Your latest balance request records.</p>
                    </div>
                </div>

                <div class="history-list">
                    @forelse($moneyRequests as $request)
                        @php
                            $requestStatus = strtolower($request->status ?? 'pending');
                        @endphp

                        <div class="history-item">
                            <div>
                                <p class="history-amount">
                                    ₱{{ number_format($request->amount, 2) }}
                                </p>

                                <p class="history-date">
                                    {{ $request->created_at->format('M d, Y h:i A') }}
                                </p>

                                @if(!empty($request->notes))
                                    <p class="history-meta">
                                        {{ $request->notes }}
                                    </p>
                                @endif
                            </div>

                            <span class="history-status {{ $requestStatus }}">
                                {{ $request->status }}
                            </span>
                        </div>
                    @empty
                        <div class="wallet-empty">
                            No money requests yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="wallet-card">
                <div class="wallet-card-head">
                    <div class="wallet-card-icon" style="background:#f5f3ff;color:#7c3aed;">
                        ⇄
                    </div>

                    <div>
                        <h2 class="wallet-card-title">Withdrawal History</h2>
                        <p class="wallet-card-sub">Your latest withdrawal request records.</p>
                    </div>
                </div>

                <div class="history-list">
                    @forelse($withdrawals as $withdrawal)
                        @php
                            $withdrawalStatus = strtolower($withdrawal->status ?? 'pending');
                        @endphp

                        <div class="history-item">
                            <div>
                                <p class="history-amount">
                                    ₱{{ number_format($withdrawal->amount, 2) }}
                                </p>

                                <p class="history-meta">
                                    {{ $withdrawal->payment_method }} - {{ $withdrawal->account_number }}
                                </p>

                                <p class="history-date">
                                    {{ $withdrawal->created_at->format('M d, Y h:i A') }}
                                </p>
                            </div>

                            <span class="history-status {{ $withdrawalStatus }}">
                                {{ $withdrawal->status }}
                            </span>
                        </div>
                    @empty
                        <div class="wallet-empty">
                            No withdrawals yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>