<x-layouts.app :title="__('Agent Dashboard')">
    @php
        $currentAgent = $agent ?? auth()->user();
        $agentCode = $currentAgent->agent_code ?? auth()->user()->agent_code ?? 'No Code Yet';
    @endphp

    <style>
        .agent-page { display:flex; flex-direction:column; gap:18px; }

        .agent-hero {
            position:relative; overflow:hidden; border-radius:22px; padding:28px; color:white;
            background:
                radial-gradient(circle at 82% 45%, rgba(29,124,255,.65), transparent 26%),
                radial-gradient(circle at 18% 20%, rgba(250,204,21,.16), transparent 22%),
                linear-gradient(135deg, #03142f 0%, #041a4d 52%, #0848b9 100%);
            box-shadow:0 18px 42px rgba(2,18,54,.18);
        }

        .agent-hero::before {
            content:""; position:absolute; inset:0;
            background:
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
            background-size:54px 54px; opacity:.45;
        }

        .agent-hero::after {
            content:"💼"; position:absolute; right:46px; top:14px; font-size:112px;
            transform:rotate(-8deg); filter:drop-shadow(0 18px 24px rgba(0,0,0,.30));
        }

        .agent-hero-inner {
            position:relative; z-index:2; display:grid;
            grid-template-columns:minmax(0,1fr) 320px; gap:24px; align-items:center;
        }

        .agent-kicker {
            margin:0; color:#38bdf8; font-size:12px; font-weight:900;
            text-transform:uppercase; letter-spacing:.18em;
        }

        .agent-title {
            margin:8px 0 0; color:white; font-size:34px; line-height:1.1;
            font-weight:950; letter-spacing:-.04em;
        }

        .agent-subtitle {
            margin:10px 0 0; color:rgba(255,255,255,.74); font-size:14px;
            font-weight:700; line-height:1.6;
        }

        .agent-code-box {
            border-radius:18px; background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.15); padding:18px; backdrop-filter:blur(10px);
        }

        .agent-code-label {
            margin:0; color:rgba(255,255,255,.65); font-size:12px; font-weight:900;
            text-transform:uppercase; letter-spacing:.12em;
        }

        .agent-code-value {
            margin:8px 0 0; color:#facc15; font-size:30px; line-height:1;
            font-weight:950; letter-spacing:-.04em; word-break:break-word;
        }

        .agent-code-help {
            margin:8px 0 0; color:rgba(255,255,255,.72); font-size:12px;
            font-weight:700; line-height:1.5;
        }

        .agent-stats-grid {
            display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px;
        }

        .agent-stat-card {
            background:white; border:1px solid #dce6f2; border-radius:20px; padding:18px;
            min-height:116px; box-shadow:0 10px 24px rgba(15,23,42,.045); transition:.16s ease;
        }

        .agent-stat-card:hover {
            transform:translateY(-2px); border-color:#bfdbfe; box-shadow:0 16px 30px rgba(15,23,42,.07);
        }

        .agent-stat-label { margin:0; color:#64748b; font-size:13px; font-weight:850; }
        .agent-stat-value { margin:12px 0 0; color:#0f172a; font-size:34px; line-height:1; font-weight:950; letter-spacing:-.04em; }
        .agent-stat-sub { margin:8px 0 0; color:#64748b; font-size:12px; font-weight:750; }

        .agent-main-grid {
            display:grid; grid-template-columns:minmax(0,1fr) 360px; gap:18px; align-items:start;
        }

        .agent-card {
            background:white; border:1px solid #dce6f2; border-radius:20px; padding:20px;
            box-shadow:0 10px 24px rgba(15,23,42,.045);
        }

        .agent-card-head {
            display:flex; align-items:flex-start; justify-content:space-between; gap:14px; margin-bottom:18px;
        }

        .agent-card-title { margin:0; color:#0f172a; font-size:22px; font-weight:950; }
        .agent-card-sub { margin:6px 0 0; color:#64748b; font-size:13px; font-weight:700; line-height:1.5; }

        .agent-primary-btn {
            min-height:42px; border-radius:12px; background:#2563eb; color:white; padding:0 16px;
            display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:950;
            box-shadow:0 12px 24px rgba(37,99,235,.18); text-decoration:none; white-space:nowrap;
        }

        .agent-table-wrap { width:100%; overflow-x:auto; border:1px solid #e7edf6; border-radius:18px; }

        .agent-table {
            width:100%; min-width:760px; border-collapse:collapse; text-align:left; font-size:14px;
        }

        .agent-table thead { background:#f8fbff; }

        .agent-table th {
            padding:14px; color:#64748b; font-size:12px; font-weight:950;
            text-transform:uppercase; letter-spacing:.08em; border-bottom:1px solid #e7edf6; white-space:nowrap;
        }

        .agent-table td {
            padding:16px 14px; border-bottom:1px solid #eef2f7; vertical-align:middle;
        }

        .agent-table tbody tr:hover { background:#f8fbff; }

        .agent-player-name { margin:0; color:#0f172a; font-size:14px; font-weight:950; }
        .agent-muted { margin:5px 0 0; color:#64748b; font-size:12px; font-weight:700; }

        .agent-pill {
            display:inline-flex; align-items:center; justify-content:center; border-radius:999px;
            padding:7px 11px; font-size:11px; font-weight:950; text-transform:uppercase; white-space:nowrap;
        }

        .agent-pill.active { background:#dcfce7; color:#15803d; }
        .agent-pill.inactive { background:#fee2e2; color:#dc2626; }
        .agent-pill.kyc { background:#fef3c7; color:#b45309; }

        .agent-wallet { color:#16a34a; font-size:14px; font-weight:950; white-space:nowrap; }
        .agent-empty { padding:34px 20px; text-align:center; color:#64748b; font-size:14px; font-weight:850; }

        .agent-right-stack { display:flex; flex-direction:column; gap:18px; }

        .agent-action-list { display:grid; gap:12px; margin-top:16px; }

        .agent-action {
            min-height:54px; border-radius:14px; padding:0 16px; display:flex;
            align-items:center; justify-content:center; text-align:center; text-decoration:none;
            font-size:13px; font-weight:950; text-transform:uppercase; letter-spacing:.04em; transition:.16s ease;
        }

        .agent-action:hover { transform:translateY(-1px); filter:brightness(.96); }
        .agent-action-yellow { background:#facc15; color:#0f172a; }
        .agent-action-dark { background:#0f172a; color:white; }
        .agent-action-blue { background:#2563eb; color:white; }

        .agent-code-panel {
            border-radius:18px; background:#0f172a; color:white; padding:20px; text-align:center; margin-top:16px;
        }

        .agent-code-panel-value {
            margin:0; color:#facc15; font-size:30px; font-weight:950; letter-spacing:-.03em; word-break:break-word;
        }

        .agent-info-list { display:grid; gap:12px; margin-top:16px; }

        .agent-info-row {
            display:flex; align-items:center; justify-content:space-between; gap:12px;
            border-bottom:1px solid #eef2f7; padding-bottom:12px;
        }

        .agent-info-row:last-child { border-bottom:0; padding-bottom:0; }

        .agent-info-label { color:#64748b; font-size:13px; font-weight:850; }
        .agent-info-value { color:#0f172a; font-size:13px; font-weight:950; text-align:right; }

        @media (max-width:1300px) {
            .agent-main-grid { grid-template-columns:1fr; }
            .agent-stats-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }

        @media (max-width:900px) {
            .agent-hero-inner { grid-template-columns:1fr; }
            .agent-hero::after { display:none; }
            .agent-card-head { flex-direction:column; }
            .agent-primary-btn { width:100%; }
            .agent-title { font-size:28px; }
        }

        @media (max-width:600px) {
            .agent-stats-grid { grid-template-columns:1fr; }
        }
    </style>

    <div class="agent-page">
        <section class="agent-hero">
            <div class="agent-hero-inner">
                <div>
                    <p class="agent-kicker">Agent Panel</p>

                    <h1 class="agent-title">
                        Welcome, {{ $currentAgent->name ?? auth()->user()->name }}
                    </h1>

                    <p class="agent-subtitle">
                        Manage assigned players, money requests, withdrawal requests, and agent wallet balance.
                    </p>
                </div>

                <div class="agent-code-box">
                    <p class="agent-code-label">Your Agent Code</p>
                    <h2 class="agent-code-value">{{ $agentCode }}</h2>
                    <p class="agent-code-help">Give this code to players so they register under you.</p>
                </div>
            </div>
        </section>

        <section class="agent-stats-grid">
            <div class="agent-stat-card">
                <p class="agent-stat-label">Total Players</p>
                <h2 class="agent-stat-value">{{ $totalPlayers ?? 0 }}</h2>
                <p class="agent-stat-sub">Assigned to your code</p>
            </div>

            <div class="agent-stat-card">
                <p class="agent-stat-label">Active</p>
                <h2 class="agent-stat-value" style="color:#16a34a;">{{ $activePlayers ?? 0 }}</h2>
                <p class="agent-stat-sub">Can access player panel</p>
            </div>

            <div class="agent-stat-card">
                <p class="agent-stat-label">Inactive</p>
                <h2 class="agent-stat-value" style="color:#dc2626;">{{ $inactivePlayers ?? 0 }}</h2>
                <p class="agent-stat-sub">Disabled accounts</p>
            </div>

            <div class="agent-stat-card">
                <p class="agent-stat-label">Pending KYC</p>
                <h2 class="agent-stat-value" style="color:#d97706;">{{ $pendingKyc ?? 0 }}</h2>
                <p class="agent-stat-sub">Waiting for review</p>
            </div>
        </section>

        <section class="agent-main-grid">
            <div class="agent-card">
                <div class="agent-card-head">
                    <div>
                        <h2 class="agent-card-title">Recent Downlines</h2>
                        <p class="agent-card-sub">Players assigned to your agent code.</p>
                    </div>

                    <a href="{{ route('agent.requests.index') }}" class="agent-primary-btn">
                        View Requests
                    </a>
                </div>

                <div class="agent-table-wrap">
                    <table class="agent-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>KYC</th>
                                <th>Wallet</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse(($players ?? []) as $player)
                                <tr>
                                    <td>
                                        <p class="agent-player-name">{{ $player->name }}</p>
                                    </td>

                                    <td>
                                        <p class="agent-muted">{{ $player->email }}</p>
                                    </td>

                                    <td>
                                        @php
                                            $isActive = $player->is_active ?? false;
                                        @endphp

                                        <span class="agent-pill {{ $isActive ? 'active' : 'inactive' }}">
                                            {{ $player->statusLabel() }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="agent-pill kyc">{{ $player->kycLabel() }}</span>
                                    </td>

                                    <td>
                                        <span class="agent-wallet">
                                            ₱{{ number_format($player->wallet_balance, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="agent-empty">No players assigned yet.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="agent-right-stack">
                <div class="agent-card">
                    <h2 class="agent-card-title">Quick Actions</h2>
                    <p class="agent-card-sub">Manage wallet and requests from your assigned players.</p>

                    <div class="agent-action-list">
                        <a href="{{ route('agent.wallet.index') }}" class="agent-action agent-action-yellow">
                            My Wallet
                        </a>

                        <a href="{{ route('agent.requests.index') }}" class="agent-action agent-action-blue">
                            Review Player Requests
                        </a>

                        <a href="{{ route('agent.requests.index') }}" class="agent-action agent-action-dark">
                            Review Withdrawals
                        </a>
                    </div>
                </div>

                <div class="agent-card">
                    <h2 class="agent-card-title">Agent Code</h2>
                    <p class="agent-card-sub">Share this code with players during registration.</p>

                    <div class="agent-code-panel">
                        <p class="agent-code-panel-value">{{ $agentCode }}</p>
                    </div>
                </div>

                <div class="agent-card">
                    <h2 class="agent-card-title">Account Summary</h2>
                    <p class="agent-card-sub">Quick view of your agent account.</p>

                    <div class="agent-info-list">
                        <div class="agent-info-row">
                            <span class="agent-info-label">Role</span>
                            <span class="agent-info-value">{{ auth()->user()->roleLabel() }}</span>
                        </div>

                        <div class="agent-info-row">
                            <span class="agent-info-label">Wallet</span>
                            <span class="agent-info-value">₱{{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}</span>
                        </div>

                        <div class="agent-info-row">
                            <span class="agent-info-label">Status</span>
                            <span class="agent-info-value">{{ auth()->user()->statusLabel() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>