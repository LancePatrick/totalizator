<x-layouts.app :title="__('Admin Dashboard')">
    @php
        $user = auth()->user();
        $current = $currentGame ?? null;

        $gameStatus = $current?->status ?? 'No Game';

        $statusColor = match ($current?->status) {
            'open' => '#16a34a',
            'waiting' => '#d97706',
            'closed' => '#2563eb',
            'ended' => '#dc2626',
            'settled' => '#7c3aed',
            default => '#64748b',
        };

        $statusBg = match ($current?->status) {
            'open' => '#dcfce7',
            'waiting' => '#fef3c7',
            'closed' => '#dbeafe',
            'ended' => '#fee2e2',
            'settled' => '#f3e8ff',
            default => '#f1f5f9',
        };
    @endphp

    <style>
        .admin-page { display:flex; flex-direction:column; gap:18px; }

        .admin-hero {
            position:relative; overflow:hidden; border-radius:22px; padding:28px; color:white;
            background:
                radial-gradient(circle at 82% 45%, rgba(29,124,255,.65), transparent 26%),
                radial-gradient(circle at 18% 20%, rgba(250,204,21,.16), transparent 22%),
                linear-gradient(135deg, #03142f 0%, #041a4d 52%, #0848b9 100%);
            box-shadow:0 18px 42px rgba(2,18,54,.18);
        }

        .admin-hero::before {
            content:""; position:absolute; inset:0;
            background:
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
            background-size:54px 54px; opacity:.45;
        }

        .admin-hero::after {
            content:"⚙️"; position:absolute; right:42px; top:12px; font-size:112px;
            transform:rotate(-10deg); filter:drop-shadow(0 18px 24px rgba(0,0,0,.30));
        }

        .admin-hero-inner {
            position:relative; z-index:2; display:grid;
            grid-template-columns:minmax(0,1fr) 280px; gap:24px; align-items:center;
        }

        .admin-kicker {
            margin:0; color:#38bdf8; font-size:12px; font-weight:900;
            text-transform:uppercase; letter-spacing:.18em;
        }

        .admin-title {
            margin:8px 0 0; color:white; font-size:34px; line-height:1.1;
            font-weight:950; letter-spacing:-.04em;
        }

        .admin-subtitle {
            margin:10px 0 0; color:rgba(255,255,255,.74); font-size:14px;
            font-weight:700; line-height:1.6;
        }

        .admin-user-box {
            border-radius:18px; background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.15); padding:18px; backdrop-filter:blur(10px);
        }

        .admin-user-label {
            margin:0; color:rgba(255,255,255,.65); font-size:12px; font-weight:900;
            text-transform:uppercase; letter-spacing:.12em;
        }

        .admin-user-name {
            margin:8px 0 0; color:#facc15; font-size:24px; font-weight:950;
        }

        .admin-user-role {
            margin:4px 0 0; color:rgba(255,255,255,.76); font-size:13px; font-weight:800;
        }

        .admin-stats-grid {
            display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:14px;
        }

        .admin-stat-card {
            background:white; border:1px solid #dce6f2; border-radius:20px; padding:18px;
            box-shadow:0 10px 24px rgba(15,23,42,.045); min-height:122px; transition:.16s ease;
        }

        .admin-stat-card:hover {
            transform:translateY(-2px); border-color:#bfdbfe; box-shadow:0 16px 30px rgba(15,23,42,.07);
        }

        .admin-stat-label { margin:0; color:#64748b; font-size:13px; font-weight:850; }
        .admin-stat-value { margin:12px 0 0; color:#0f172a; font-size:34px; line-height:1; font-weight:950; letter-spacing:-.04em; }
        .admin-stat-sub { margin:8px 0 0; color:#64748b; font-size:12px; font-weight:750; }

        .admin-main-grid {
            display:grid; grid-template-columns:minmax(0,1fr) 380px; gap:18px; align-items:start;
        }

        .admin-card {
            background:white; border:1px solid #dce6f2; border-radius:20px; padding:20px;
            box-shadow:0 10px 24px rgba(15,23,42,.045);
        }

        .admin-section-head {
            display:flex; align-items:flex-start; justify-content:space-between; gap:14px; margin-bottom:18px;
        }

        .admin-section-title { margin:0; color:#0f172a; font-size:22px; font-weight:950; }
        .admin-section-sub { margin:6px 0 0; color:#64748b; font-size:13px; font-weight:700; line-height:1.5; }

        .admin-primary-btn {
            min-height:42px; border-radius:12px; background:#2563eb; color:white; padding:0 16px;
            display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:950;
            box-shadow:0 12px 24px rgba(37,99,235,.18); text-decoration:none;
        }

        .admin-actions-grid {
            display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px;
        }

        .admin-action {
            border:1px solid #e7edf6; border-radius:18px; padding:18px; background:#ffffff;
            transition:.16s ease; text-decoration:none; color:inherit;
        }

        .admin-action:hover {
            transform:translateY(-2px); border-color:#bfdbfe; box-shadow:0 14px 26px rgba(15,23,42,.06);
        }

        .admin-action-icon {
            width:46px; height:46px; border-radius:14px; display:flex; align-items:center;
            justify-content:center; color:white; font-size:14px; font-weight:950;
        }

        .admin-action-title { margin:14px 0 0; color:#0f172a; font-size:17px; font-weight:950; }
        .admin-action-text { margin:7px 0 0; color:#64748b; font-size:13px; font-weight:700; line-height:1.5; }

        .admin-right-stack { display:flex; flex-direction:column; gap:18px; }

        .admin-game-status {
            display:inline-flex; border-radius:999px; padding:7px 12px; font-size:12px;
            font-weight:950; text-transform:uppercase;
        }

        .admin-pool-list { display:flex; flex-direction:column; gap:12px; margin-top:16px; }
        .admin-pool-item { border-radius:16px; padding:16px; border:1px solid #e7edf6; }
        .admin-pool-head { display:flex; align-items:center; justify-content:space-between; gap:12px; }
        .admin-pool-label { margin:0; font-size:14px; font-weight:950; }
        .admin-pool-odds { border-radius:999px; padding:5px 10px; font-size:12px; font-weight:950; }
        .admin-pool-value { margin:10px 0 0; color:#0f172a; font-size:24px; font-weight:950; }

        .admin-total-pool {
            margin-top:12px; border-radius:18px; background:#0f172a; color:white; padding:18px;
        }

        .admin-total-label {
            margin:0; color:rgba(255,255,255,.55); font-size:12px; font-weight:900;
            text-transform:uppercase; letter-spacing:.12em;
        }

        .admin-total-value { margin:8px 0 0; color:#facc15; font-size:30px; font-weight:950; }

        .admin-status-list { display:flex; flex-direction:column; gap:12px; margin-top:14px; }

        .admin-status-row {
            display:flex; align-items:center; justify-content:space-between; gap:12px;
            border-bottom:1px solid #eef2f7; padding-bottom:12px;
        }

        .admin-status-row:last-child { border-bottom:0; padding-bottom:0; }
        .admin-status-name { color:#64748b; font-size:13px; font-weight:850; }

        .admin-online-pill {
            border-radius:999px; padding:6px 10px; background:#dcfce7; color:#15803d;
            font-size:11px; font-weight:950; text-transform:uppercase;
        }

        @media (max-width:1300px) {
            .admin-stats-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .admin-main-grid { grid-template-columns:1fr; }
        }

        @media (max-width:900px) {
            .admin-hero-inner, .admin-actions-grid { grid-template-columns:1fr; }
            .admin-hero::after { display:none; }
            .admin-section-head { flex-direction:column; }
        }

        @media (max-width:600px) {
            .admin-stats-grid { grid-template-columns:1fr; }
            .admin-title { font-size:28px; }
        }
    </style>

    <div class="admin-page">
        <section class="admin-hero">
            <div class="admin-hero-inner">
                <div>
                    <p class="admin-kicker">Admin Panel</p>
                    <h1 class="admin-title">Full System Access</h1>
                    <p class="admin-subtitle">
                        Manage agents, players, KYC, money requests, withdrawals, game rounds, videos, odds, results, payouts, and reports.
                    </p>
                </div>

                <div class="admin-user-box">
                    <p class="admin-user-label">Logged In As</p>
                    <h2 class="admin-user-name">{{ $user->name }}</h2>
                    <p class="admin-user-role">{{ $user->roleLabel() }}</p>
                </div>
            </div>
        </section>

        <section class="admin-stats-grid">
            <div class="admin-stat-card">
                <p class="admin-stat-label">Total Agents</p>
                <h2 class="admin-stat-value">{{ $totalAgents ?? 0 }}</h2>
                <p class="admin-stat-sub">Registered agents</p>
            </div>

            <div class="admin-stat-card">
                <p class="admin-stat-label">Total Players</p>
                <h2 class="admin-stat-value">{{ $totalPlayers ?? 0 }}</h2>
                <p class="admin-stat-sub">Registered players</p>
            </div>

            <div class="admin-stat-card">
                <p class="admin-stat-label">Active Agents</p>
                <h2 class="admin-stat-value" style="color:#16a34a;">{{ $activeAgents ?? 0 }}</h2>
                <p class="admin-stat-sub">Can manage players</p>
            </div>

            <div class="admin-stat-card">
                <p class="admin-stat-label">Active Players</p>
                <h2 class="admin-stat-value" style="color:#16a34a;">{{ $activePlayers ?? 0 }}</h2>
                <p class="admin-stat-sub">Allowed accounts</p>
            </div>

            <div class="admin-stat-card">
                <p class="admin-stat-label">Pending KYC</p>
                <h2 class="admin-stat-value" style="color:#d97706;">{{ $pendingKyc ?? 0 }}</h2>
                <p class="admin-stat-sub">Waiting for review</p>
            </div>
        </section>

        <section class="admin-main-grid">
            <div class="admin-card">
                <div class="admin-section-head">
                    <div>
                        <h2 class="admin-section-title">Admin Actions</h2>
                        <p class="admin-section-sub">
                            Main controls for users, money, games, and reports.
                        </p>
                    </div>

                    <a href="{{ route('admin.games.index') }}" class="admin-primary-btn">
                        Open Game Control
                    </a>
                </div>

                <div class="admin-actions-grid">
                    <a href="{{ route('admin.agents.index') }}" class="admin-action">
                        <div class="admin-action-icon" style="background:#16a34a;">AG</div>
                        <h3 class="admin-action-title">Manage Agents</h3>
                        <p class="admin-action-text">Activate, deactivate, and review agent accounts.</p>
                    </a>

                    <a href="{{ route('admin.players.index') }}" class="admin-action">
                        <div class="admin-action-icon" style="background:#2563eb;">PL</div>
                        <h3 class="admin-action-title">Manage Players</h3>
                        <p class="admin-action-text">View player accounts, agents, status, and wallets.</p>
                    </a>

                    <a href="{{ route('admin.kyc.index') }}" class="admin-action">
                        <div class="admin-action-icon" style="background:#ea580c;">KY</div>
                        <h3 class="admin-action-title">KYC Requests</h3>
                        <p class="admin-action-text">Approve or reject player verification documents.</p>
                    </a>

                    <a href="{{ route('admin.money-requests.index') }}" class="admin-action">
                        <div class="admin-action-icon" style="background:#facc15;color:#0f172a;">₱</div>
                        <h3 class="admin-action-title">Money Requests</h3>
                        <p class="admin-action-text">Review agent funding and player wallet requests.</p>
                    </a>

                    <a href="{{ route('admin.games.index') }}" class="admin-action">
                        <div class="admin-action-icon" style="background:#7c3aed;">GM</div>
                        <h3 class="admin-action-title">Game Control</h3>
                        <p class="admin-action-text">Create game, start betting, end game, and declare result.</p>
                    </a>

                    <a href="{{ route('admin.reports.games') }}" class="admin-action">
                        <div class="admin-action-icon" style="background:#0f172a;">RP</div>
                        <h3 class="admin-action-title">Reports</h3>
                        <p class="admin-action-text">View transactions, payouts, bets, and game logs.</p>
                    </a>
                </div>
            </div>

            <div class="admin-right-stack">
                <div class="admin-card">
                    <div class="admin-section-head">
                        <div>
                            <h2 class="admin-section-title">Game Snapshot</h2>
                            <p class="admin-section-sub">Quick access to current totalizator status.</p>
                        </div>

                        <span class="admin-game-status" style="background:{{ $statusBg }}; color:{{ $statusColor }};">
                            {{ $gameStatus }}
                        </span>
                    </div>

                    <div class="admin-pool-list">
                        <div class="admin-pool-item" style="background:#fff7ed;border-color:#fed7aa;">
                            <div class="admin-pool-head">
                                <p class="admin-pool-label" style="color:#ea580c;">Meron</p>
                                <span class="admin-pool-odds" style="background:#fed7aa;color:#9a3412;">
                                    {{ number_format($current->meron_odds ?? 0, 2) }}x
                                </span>
                            </div>
                            <h3 class="admin-pool-value">₱{{ number_format($current->meron_total ?? 0, 2) }}</h3>
                        </div>

                        <div class="admin-pool-item" style="background:#eff6ff;border-color:#bfdbfe;">
                            <div class="admin-pool-head">
                                <p class="admin-pool-label" style="color:#2563eb;">Wala</p>
                                <span class="admin-pool-odds" style="background:#dbeafe;color:#1d4ed8;">
                                    {{ number_format($current->wala_odds ?? 0, 2) }}x
                                </span>
                            </div>
                            <h3 class="admin-pool-value">₱{{ number_format($current->wala_total ?? 0, 2) }}</h3>
                        </div>

                        <div class="admin-pool-item" style="background:#f5f3ff;border-color:#ddd6fe;">
                            <div class="admin-pool-head">
                                <p class="admin-pool-label" style="color:#7c3aed;">Draw</p>
                                <span class="admin-pool-odds" style="background:#ede9fe;color:#6d28d9;">
                                    {{ number_format($current->draw_odds ?? 0, 2) }}x
                                </span>
                            </div>
                            <h3 class="admin-pool-value">₱{{ number_format($current->draw_total ?? 0, 2) }}</h3>
                        </div>
                    </div>

                    <div class="admin-total-pool">
                        <p class="admin-total-label">Total Pool</p>
                        <h3 class="admin-total-value">₱{{ number_format($current->total_pool ?? 0, 2) }}</h3>
                        <p class="admin-subtitle" style="margin-top:6px;">
                            Net Pool: ₱{{ number_format($current->net_pool ?? 0, 2) }}
                        </p>
                    </div>

                    <a href="{{ route('admin.games.index') }}" class="admin-primary-btn" style="width:100%;margin-top:14px;">
                        Go to Game Control
                    </a>
                </div>

                <div class="admin-card">
                    <h2 class="admin-section-title">System Status</h2>
                    <p class="admin-section-sub">Main services overview.</p>

                    <div class="admin-status-list">
                        <div class="admin-status-row">
                            <span class="admin-status-name">Auth</span>
                            <span class="admin-online-pill">Online</span>
                        </div>

                        <div class="admin-status-row">
                            <span class="admin-status-name">Database</span>
                            <span class="admin-online-pill">Connected</span>
                        </div>

                        <div class="admin-status-row">
                            <span class="admin-status-name">Game Engine</span>
                            <span class="admin-online-pill">Setup</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>