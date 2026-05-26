<x-layouts.app :title="__('Player Dashboard')">
    @php
        $user = auth()->user();
        $playerUser = $player ?? $user;

        $name = $playerUser->name ?? $user->name;
        $initials = strtoupper(substr($name, 0, 2));
        $wallet = number_format($user->wallet_balance ?? 0, 2);
        $agentName = $user->assignedAgentName();
        $status = $user->statusLabel();
        $kyc = $user->kycLabel();
    @endphp

    <style>
        .pd-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .pd-hero {
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

        .pd-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 54px 54px;
            opacity: .45;
        }

        .pd-hero::after {
            content: "💳";
            position: absolute;
            right: 42px;
            top: 16px;
            font-size: 118px;
            transform: rotate(-12deg);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.30));
        }

        .pd-hero-inner {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 24px;
            align-items: center;
        }

        .pd-profile {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .pd-avatar {
            width: 82px;
            height: 82px;
            border-radius: 999px;
            background: linear-gradient(135deg, #1d7cff, #5a5ff6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: 950;
            border: 4px solid rgba(255,255,255,.14);
            box-shadow: 0 16px 30px rgba(29,124,255,.35);
        }

        .pd-kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .pd-title {
            margin: 8px 0 0;
            color: white;
            font-size: 32px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .pd-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.74);
            font-size: 14px;
            font-weight: 700;
        }

        .pd-subtitle span {
            color: #38bdf8;
            font-weight: 950;
        }

        .pd-wallet-box {
            border-radius: 18px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            padding: 18px;
            backdrop-filter: blur(10px);
        }

        .pd-wallet-label {
            margin: 0;
            color: rgba(255,255,255,.65);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .pd-wallet-value {
            margin: 8px 0 0;
            color: #facc15;
            font-size: 34px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .pd-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .pd-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .pd-stat-card {
            min-height: 118px;
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
            transition: .16s ease;
        }

        .pd-stat-card:hover {
            transform: translateY(-2px);
            border-color: #bfdbfe;
            box-shadow: 0 16px 30px rgba(15,23,42,.07);
        }

        .pd-stat-main {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .pd-stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 950;
        }

        .pd-stat-label {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 850;
        }

        .pd-stat-value {
            margin: 6px 0 0;
            color: #172554;
            font-size: 25px;
            line-height: 1.1;
            font-weight: 950;
        }

        .pd-stat-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
        }

        .pd-stat-arrow {
            color: #94a3b8;
            font-size: 28px;
        }

        .pd-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 380px;
            gap: 18px;
            align-items: start;
        }

        .pd-section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
        }

        .pd-section-title {
            margin: 0;
            color: #0f172a;
            font-size: 20px;
            font-weight: 950;
        }

        .pd-section-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .pd-section-link {
            color: #1d7cff;
            font-size: 13px;
            font-weight: 950;
        }

        .pd-actions-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pd-action-row {
            display: grid;
            grid-template-columns: 50px 1fr auto;
            gap: 14px;
            align-items: center;
            min-height: 66px;
            border: 1px solid #e7edf6;
            border-radius: 14px;
            padding: 10px 12px;
            background: white;
            transition: .16s ease;
        }

        .pd-action-row:hover {
            transform: translateY(-1px);
            border-color: #cbdcf5;
            box-shadow: 0 12px 22px rgba(15,23,42,.05);
        }

        .pd-action-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 950;
        }

        .pd-action-name {
            margin: 0;
            color: #111827;
            font-size: 16px;
            font-weight: 950;
        }

        .pd-action-desc {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
        }

        .pd-action-btn {
            min-width: 148px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 950;
        }

        .pd-right-stack {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .pd-activity-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .pd-activity {
            display: grid;
            grid-template-columns: 42px 1fr auto;
            gap: 12px;
            align-items: center;
            border-bottom: 1px solid #edf2f7;
            padding-bottom: 14px;
        }

        .pd-activity:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .pd-activity-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 950;
        }

        .pd-activity-title {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
        }

        .pd-activity-desc {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
        }

        .pd-activity-value {
            text-align: right;
            font-size: 13px;
            font-weight: 950;
        }

        .pd-activity-date {
            color: #64748b;
            font-size: 12px;
            margin-top: 4px;
        }

        .pd-quick-box {
            border: 1px solid #e7edf6;
            border-radius: 14px;
            padding: 18px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            align-items: center;
        }

        .pd-quick-label {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 850;
        }

        .pd-quick-value {
            margin: 8px 0 0;
            color: #111827;
            font-size: 22px;
            font-weight: 950;
        }

        .pd-quick-change {
            margin: 6px 0 0;
            color: #16a34a;
            font-size: 12px;
            font-weight: 950;
        }

        .pd-footer {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            margin-top: 4px;
            padding: 8px 4px 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
        }

        .pd-footer a {
            color: #2563eb;
            font-weight: 850;
        }

        .pd-footer-links {
            display: flex;
            gap: 24px;
        }

        @media (max-width: 1300px) {
            .pd-main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 980px) {
            .pd-hero-inner,
            .pd-stats-grid {
                grid-template-columns: 1fr;
            }

            .pd-hero::after {
                display: none;
            }
        }

        @media (max-width: 720px) {
            .pd-profile {
                flex-direction: column;
                align-items: flex-start;
            }

            .pd-title {
                font-size: 26px;
            }

            .pd-action-row {
                grid-template-columns: 50px 1fr;
            }

            .pd-action-btn {
                grid-column: 1 / -1;
                width: 100%;
            }

            .pd-footer {
                flex-direction: column;
            }

            .pd-footer-links {
                flex-wrap: wrap;
                gap: 12px;
            }
        }
    </style>

    <div class="pd-page">
        <section class="pd-hero">
            <div class="pd-hero-inner">
                <div class="pd-profile">
                    <div class="pd-avatar">{{ $initials }}</div>

                    <div>
                        <p class="pd-kicker">Player Dashboard</p>

                        <h1 class="pd-title">
                            Welcome back, {{ $name }}
                        </h1>

                        <p class="pd-subtitle">
                            Your assigned agent:
                            <span>{{ $agentName }}</span>
                        </p>
                    </div>
                </div>

                <div class="pd-wallet-box">
                    <p class="pd-wallet-label">Wallet Balance</p>
                    <h2 class="pd-wallet-value">₱{{ $wallet }}</h2>
                    <p class="pd-subtitle" style="margin-top:8px;">Available balance</p>
                </div>
            </div>
        </section>

        <section class="pd-stats-grid">
            <a href="{{ route('player.wallet.index') }}" class="pd-stat-card">
                <div class="pd-stat-main">
                    <div class="pd-stat-icon" style="background:linear-gradient(135deg,#4f46e5,#3b82f6);">
                        ₱
                    </div>

                    <div>
                        <p class="pd-stat-label">Wallet Balance</p>
                        <h3 class="pd-stat-value">₱{{ $wallet }}</h3>
                        <p class="pd-stat-sub">Available balance</p>
                    </div>
                </div>

                <div class="pd-stat-arrow">›</div>
            </a>

            <div class="pd-stat-card">
                <div class="pd-stat-main">
                    <div class="pd-stat-icon" style="background:linear-gradient(135deg,#22c55e,#10b981);">
                        👤
                    </div>

                    <div>
                        <p class="pd-stat-label">Account Status</p>
                        <h3 class="pd-stat-value" style="color:#16a34a;">{{ $status }}</h3>
                        <p class="pd-stat-sub">Player account status</p>
                    </div>
                </div>

                <div class="pd-stat-arrow">›</div>
            </div>

            <a href="{{ route('player.kyc.index') }}" class="pd-stat-card">
                <div class="pd-stat-main">
                    <div class="pd-stat-icon" style="background:linear-gradient(135deg,#22c55e,#34d399);">
                        ✓
                    </div>

                    <div>
                        <p class="pd-stat-label">KYC Status</p>
                        <h3 class="pd-stat-value" style="color:#16a34a;">{{ $kyc }} ✓</h3>
                        <p class="pd-stat-sub">Verification status</p>
                    </div>
                </div>

                <div class="pd-stat-arrow">›</div>
            </a>
        </section>

        <section class="pd-main-grid">
            <div class="pd-card">
                <div class="pd-section-head">
                    <div>
                        <h2 class="pd-section-title">⚡ Player Actions</h2>
                        <p class="pd-section-sub">
                            Request funds, submit KYC, play the game, and view your activity.
                        </p>
                    </div>
                </div>

                <div class="pd-actions-list">
                    <a href="{{ route('player.wallet.index') }}" class="pd-action-row">
                        <div class="pd-action-icon" style="background:#eff6ff;color:#1d7cff;">⇩</div>
                        <div>
                            <h3 class="pd-action-name">Request Money</h3>
                            <p class="pd-action-desc">Request wallet funds from your assigned agent.</p>
                        </div>
                        <span class="pd-action-btn" style="background:#eff6ff;color:#1d4ed8;">Request Funds ›</span>
                    </a>

                    <a href="{{ route('player.wallet.index') }}" class="pd-action-row">
                        <div class="pd-action-icon" style="background:#f5f3ff;color:#6d28d9;">↥</div>
                        <div>
                            <h3 class="pd-action-name">Withdraw Money</h3>
                            <p class="pd-action-desc">Request withdrawal from your wallet balance.</p>
                        </div>
                        <span class="pd-action-btn" style="background:#f5f3ff;color:#6d28d9;">Withdraw Funds ›</span>
                    </a>

                    <a href="{{ route('player.kyc.index') }}" class="pd-action-row">
                        <div class="pd-action-icon" style="background:#fff1ed;color:#ea580c;">◇</div>
                        <div>
                            <h3 class="pd-action-name">Submit KYC</h3>
                            <p class="pd-action-desc">Upload your verification details for approval.</p>
                        </div>
                        <span class="pd-action-btn" style="background:#fff1ed;color:#ea580c;">Submit KYC ›</span>
                    </a>

                    <a href="{{ route('player.game.index') }}" class="pd-action-row">
                        <div class="pd-action-icon" style="background:#ecfdf5;color:#16a34a;">🎮</div>
                        <div>
                            <h3 class="pd-action-name">Play Game</h3>
                            <p class="pd-action-desc">Open the Totalizator game and place your bet.</p>
                        </div>
                        <span class="pd-action-btn" style="background:#ecfdf5;color:#16a34a;">Play Now ›</span>
                    </a>

                    <a href="{{ route('player.game.index') }}" class="pd-action-row">
                        <div class="pd-action-icon" style="background:#fffbeb;color:#d97706;">▤</div>
                        <div>
                            <h3 class="pd-action-name">My Bets</h3>
                            <p class="pd-action-desc">View your latest bet records and result status.</p>
                        </div>
                        <span class="pd-action-btn" style="background:#fffbeb;color:#d97706;">View Bets ›</span>
                    </a>

                    <a href="{{ route('player.wallet.index') }}" class="pd-action-row">
                        <div class="pd-action-icon" style="background:#eff6ff;color:#2563eb;">◴</div>
                        <div>
                            <h3 class="pd-action-name">Transaction History</h3>
                            <p class="pd-action-desc">View wallet credits, debits, bets, and payouts.</p>
                        </div>
                        <span class="pd-action-btn" style="background:#eff6ff;color:#2563eb;">View History ›</span>
                    </a>
                </div>
            </div>

            <div class="pd-right-stack">
                <div class="pd-card">
                    <div class="pd-section-head">
                        <h2 class="pd-section-title">Recent Activity</h2>
                        <a href="{{ route('player.wallet.index') }}" class="pd-section-link">View All</a>
                    </div>

                    <div class="pd-activity-list">
                        <div class="pd-activity">
                            <div class="pd-activity-icon" style="background:#dcfce7;color:#16a34a;">✓</div>
                            <div>
                                <p class="pd-activity-title">KYC Verification</p>
                                <p class="pd-activity-desc">Verification status</p>
                            </div>
                            <div class="pd-activity-value" style="color:#16a34a;">
                                {{ $kyc }}
                                <div class="pd-activity-date">Current</div>
                            </div>
                        </div>

                        <div class="pd-activity">
                            <div class="pd-activity-icon" style="background:#dcfce7;color:#16a34a;">₱</div>
                            <div>
                                <p class="pd-activity-title">Wallet Balance</p>
                                <p class="pd-activity-desc">Available funds</p>
                            </div>
                            <div class="pd-activity-value" style="color:#16a34a;">
                                ₱{{ $wallet }}
                                <div class="pd-activity-date">Current</div>
                            </div>
                        </div>

                        <div class="pd-activity">
                            <div class="pd-activity-icon" style="background:#f5f3ff;color:#6d28d9;">🎮</div>
                            <div>
                                <p class="pd-activity-title">Totalizator Game</p>
                                <p class="pd-activity-desc">Meron / Wala / Draw</p>
                            </div>
                            <div class="pd-activity-value" style="color:#1d7cff;">
                                Ready
                                <div class="pd-activity-date">Now</div>
                            </div>
                        </div>

                        <div class="pd-activity">
                            <div class="pd-activity-icon" style="background:#fce7f3;color:#db2777;">↥</div>
                            <div>
                                <p class="pd-activity-title">Withdrawal Request</p>
                                <p class="pd-activity-desc">Request from wallet page</p>
                            </div>
                            <div class="pd-activity-value" style="color:#64748b;">
                                Open
                                <div class="pd-activity-date">Anytime</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pd-card">
                    <div class="pd-section-head">
                        <h2 class="pd-section-title">Quick Stats</h2>
                        <span class="pd-section-link">This Week ▾</span>
                    </div>

                    <div class="pd-quick-box">
                        <div>
                            <p class="pd-quick-label">Wallet</p>
                            <h3 class="pd-quick-value">₱{{ $wallet }}</h3>
                            <p class="pd-quick-change">▲ Available</p>
                        </div>

                        <div>
                            <p class="pd-quick-label">Account</p>
                            <h3 class="pd-quick-value">{{ $status }}</h3>
                            <p class="pd-quick-change">▲ Active profile</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="pd-footer">
            <div>© 2026 JL. All rights reserved.</div>

            <div class="pd-footer-links">
                <a href="#">Terms &amp; Conditions</a>
                <a href="#">Privacy Policy</a>
            </div>
        </div>
    </div>
</x-layouts.app>