@props(['title' => config('app.name', 'Laravel')])

@php
    $user = auth()->user();
    $role = $user?->role ?? 'player';
    $roleLabel = $user?->roleLabel() ?? ucfirst($role);

    $safeRoute = function ($routeName, $fallback = 'dashboard') {
        return Route::has($routeName) ? route($routeName) : route($fallback);
    };

    $menu = match ($role) {
        'admin' => [
            ['label' => 'Dashboard', 'href' => $safeRoute('admin.dashboard'), 'icon' => 'home', 'active' => request()->routeIs('admin.dashboard')],
            ['label' => 'Manage Agents', 'href' => $safeRoute('admin.agents.index'), 'icon' => 'users', 'active' => request()->routeIs('admin.agents.*')],
            ['label' => 'Manage Players', 'href' => $safeRoute('admin.players.index'), 'icon' => 'users', 'active' => request()->routeIs('admin.players.*')],
            ['label' => 'Money Requests', 'href' => $safeRoute('admin.money-requests.index'), 'icon' => 'wallet', 'active' => request()->routeIs('admin.money-requests.*') || request()->routeIs('admin.withdrawals.*')],
            ['label' => 'Game Control', 'href' => $safeRoute('admin.games.index'), 'icon' => 'game', 'active' => request()->routeIs('admin.games.*')],
            ['label' => 'KYC Requests', 'href' => $safeRoute('admin.kyc.index'), 'icon' => 'shield', 'active' => request()->routeIs('admin.kyc.*')],
            ['label' => 'Game Reports', 'href' => $safeRoute('admin.reports.games'), 'icon' => 'report', 'active' => request()->routeIs('admin.reports.games')],
            ['label' => 'Wallet Reports', 'href' => $safeRoute('admin.reports.wallet'), 'icon' => 'document', 'active' => request()->routeIs('admin.reports.wallet')],
        ],

        'agent' => [
            ['label' => 'Dashboard', 'href' => $safeRoute('agent.dashboard'), 'icon' => 'home', 'active' => request()->routeIs('agent.dashboard')],
            ['label' => 'My Wallet', 'href' => $safeRoute('agent.wallet.index'), 'icon' => 'wallet', 'active' => request()->routeIs('agent.wallet.*')],
            ['label' => 'Player Requests', 'href' => $safeRoute('agent.requests.index'), 'icon' => 'document', 'active' => request()->routeIs('agent.requests.*')],
        ],

        default => [
            ['label' => 'Dashboard', 'href' => $safeRoute('player.dashboard'), 'icon' => 'home', 'active' => request()->routeIs('player.dashboard')],
            ['label' => 'Wallet', 'href' => $safeRoute('player.wallet.index'), 'icon' => 'wallet', 'active' => request()->routeIs('player.wallet.*')],
            ['label' => 'Play Game', 'href' => $safeRoute('player.game.index'), 'icon' => 'game', 'active' => request()->routeIs('player.game.*')],
            ['label' => 'KYC Verification', 'href' => $safeRoute('player.kyc.index'), 'icon' => 'shield', 'active' => request()->routeIs('player.kyc.*')],
            ['label' => 'Transactions', 'href' => $safeRoute('player.wallet.index'), 'icon' => 'document', 'active' => request()->routeIs('player.transactions.*')],
        ],
    };

    $initials = strtoupper(substr($user?->name ?? 'U', 0, 2));

    $panelLabel = match ($role) {
        'admin' => 'Admin Panel',
        'agent' => 'Agent Panel',
        default => 'Player Panel',
    };
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>{{ $title }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            min-height: 100%;
            font-family: Inter, Arial, Helvetica, sans-serif;
            background: #f4f7fc;
            color: #0f172a;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button, input {
            font-family: inherit;
        }

        .app-shell {
            min-height: 100vh;
            display: flex;
            background: #f4f7fc;
        }

        .sidebar {
            width: 280px;
            flex-shrink: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at 85% 15%, rgba(33, 125, 255, 0.18), transparent 24%),
                linear-gradient(180deg, #04142d 0%, #020b1d 100%);
            padding: 26px 20px;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            overflow: hidden;
        }

        .sidebar::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 42px 42px;
            pointer-events: none;
        }

        .sidebar-inner {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .brand-mark {
            width: 36px;
            height: 36px;
            color: #2a8cff;
            font-size: 36px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-text {
            color: #ffffff;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.03em;
            line-height: 1;
        }

        .brand-text span {
            color: #2a8cff;
        }

        .panel-label {
            margin: 0 0 20px 48px;
            color: rgba(255,255,255,.45);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .16em;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        .nav-link {
            min-height: 52px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0 16px;
            color: rgba(255,255,255,0.78);
            border-radius: 16px;
            border: 1px solid transparent;
            font-size: 15px;
            font-weight: 800;
            transition: all .18s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #ffffff;
            border-color: rgba(42, 140, 255, 0.5);
            background: linear-gradient(90deg, rgba(20,93,223,0.85), rgba(15,49,110,0.55));
            box-shadow: 0 12px 30px rgba(15, 73, 190, 0.25);
        }

        .nav-icon {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .sidebar-spacer {
            flex: 1;
        }

        .promo-card {
            margin-top: 22px;
            background:
                radial-gradient(circle at 85% 82%, rgba(37,99,235,.50), transparent 24%),
                linear-gradient(180deg, rgba(6,22,52,1) 0%, rgba(7,33,92,0.96) 100%);
            border: 1px solid rgba(42,140,255,.35);
            border-radius: 22px;
            padding: 22px 18px;
            color: #fff;
            box-shadow: inset 0 0 45px rgba(34, 125, 255, 0.08);
        }

        .promo-card h3 {
            margin: 0;
            font-size: 18px;
            line-height: 1.2;
            font-weight: 900;
        }

        .promo-card h3 span {
            color: #2a8cff;
        }

        .promo-card p {
            margin: 12px 0 18px;
            color: rgba(255,255,255,0.74);
            font-size: 14px;
            line-height: 1.5;
            font-weight: 600;
        }

        .promo-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 18px;
            border-radius: 12px;
            background: #1677ff;
            color: #fff;
            font-size: 14px;
            font-weight: 900;
            box-shadow: 0 14px 26px rgba(22,119,255,.28);
        }

        .help-card {
            margin-top: 16px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 16px;
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .help-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(42, 140, 255, 0.12);
            color: #2a8cff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .help-title {
            margin: 0;
            font-size: 14px;
            font-weight: 900;
        }

        .help-text {
            margin: 3px 0 0;
            font-size: 12px;
            color: rgba(255,255,255,.65);
            font-weight: 600;
        }

        .logout-form {
            margin-top: 16px;
        }

        .logout-button {
            width: 100%;
            height: 48px;
            border: 1px solid rgba(248, 113, 113, .35);
            border-radius: 14px;
            background: rgba(239, 68, 68, .13);
            color: #fecaca;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: .18s ease;
        }

        .logout-button:hover {
            background: rgba(239, 68, 68, .24);
            color: #ffffff;
        }

        .topbar-logout-form {
            margin: 0;
        }

        .topbar-logout-button {
            height: 46px;
            border: 1px solid #fecaca;
            border-radius: 14px;
            background: #fff1f2;
            color: #dc2626;
            padding: 0 18px;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(15,23,42,.04);
            transition: .18s ease;
        }

        .topbar-logout-button:hover {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 18px 22px 24px;
        }

        .topbar {
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .page-title {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: #0f172a;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .search-box {
            width: 300px;
            height: 50px;
            border: 1px solid #dce5f1;
            border-radius: 15px;
            background: #fff;
            padding: 0 16px;
            outline: none;
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            box-shadow: 0 8px 24px rgba(15,23,42,.04);
        }

        .user-box {
            min-width: 215px;
            height: 54px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid #dde6f2;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 14px;
            box-shadow: 0 8px 24px rgba(15,23,42,.04);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            background: linear-gradient(135deg, #1d7cff, #5a5ff6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 900;
        }

        .user-name {
            margin: 0;
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
        }

        .user-role {
            margin: 2px 0 0;
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
        }

        .content {
            min-height: calc(100vh - 100px);
        }

        .mobile-menu {
            display: none;
            gap: 10px;
            overflow-x: auto;
            margin-bottom: 16px;
            padding-bottom: 4px;
        }

        .mobile-menu a {
            flex: 0 0 auto;
            padding: 10px 14px;
            border-radius: 12px;
            background: #eaf2ff;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 800;
        }

        @media (max-width: 1100px) {
            .app-shell {
                display: block;
            }

            .sidebar {
                display: none;
            }

            .main {
                padding: 14px;
            }

            .mobile-menu {
                display: flex;
            }
        }

        @media (max-width: 768px) {
            .topbar {
                flex-direction: column;
                align-items: stretch;
                height: auto;
            }

            .topbar-right {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }

            .search-box,
            .user-box,
            .topbar-logout-button {
                width: 100%;
                min-width: 0;
            }
        }
    </style>
</head>

<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-inner">
                <a href="{{ route('dashboard') }}" class="brand">
                    <div class="brand-mark">⚡</div>
                    <div class="brand-text">J <span>L</span></div>
                </a>

                <p class="panel-label">{{ $panelLabel }}</p>

                <nav class="nav">
                    @foreach($menu as $item)
                        <a href="{{ $item['href'] }}" class="nav-link {{ $item['active'] ? 'active' : '' }}">
                            <span class="nav-icon">
                                @if($item['icon'] === 'home')
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 10.5L12 4L20 10.5V19C20 19.5523 19.5523 20 19 20H15V14H9V20H5C4.44772 20 4 19.5523 4 19V10.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>
                                @elseif($item['icon'] === 'wallet')
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                        <path d="M3 7.5C3 6.11929 4.11929 5 5.5 5H18C19.1046 5 20 5.89543 20 7V17C20 18.1046 19.1046 19 18 19H5.5C4.11929 19 3 17.8807 3 16.5V7.5Z" stroke="currentColor" stroke-width="2"/>
                                        <path d="M16 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @elseif($item['icon'] === 'game')
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 10H17C19.2091 10 21 11.7909 21 14V15C21 17.2091 19.2091 19 17 19H7C4.79086 19 3 17.2091 3 15V14C3 11.7909 4.79086 10 7 10Z" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 13V17M6 15H10M16.5 14.5H16.51M18.5 16.5H18.51" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M8 10L10 6H14L16 10" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>
                                @elseif($item['icon'] === 'shield')
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3L19 6V11C19 15.4183 16.3137 19.3828 12 21C7.68629 19.3828 5 15.4183 5 11V6L12 3Z" stroke="currentColor" stroke-width="2"/>
                                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @elseif($item['icon'] === 'users')
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                        <path d="M16 11C17.6569 11 19 9.65685 19 8C19 6.34315 17.6569 5 16 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M8 11C9.65685 11 11 9.65685 11 8C11 6.34315 9.65685 5 8 5C6.34315 5 5 6.34315 5 8C5 9.65685 6.34315 11 8 11Z" stroke="currentColor" stroke-width="2"/>
                                        <path d="M3 19C3.5 15.5 5.5 14 8 14C10.5 14 12.5 15.5 13 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M14 14C16.5 14 18.5 15.5 19 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @elseif($item['icon'] === 'report')
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                        <path d="M5 20V4H19V20H5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                        <path d="M9 16V12M12 16V8M15 16V10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @else
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 4H17L20 7V20H4V4H7Z" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 10H16M8 14H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @endif
                            </span>

                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <div class="sidebar-spacer"></div>

                <div class="promo-card">
                    <h3>
                        @if($role === 'admin')
                            Manage. Track. <span>Grow.</span>
                        @elseif($role === 'agent')
                            Assist. Approve. <span>Earn.</span>
                        @else
                            Play. Bet. <span>Win.</span>
                        @endif
                    </h3>

                    <p>
                        @if($role === 'admin')
                            Monitor users, games, reports, and wallet activity.
                        @elseif($role === 'agent')
                            Manage player requests and your wallet balance.
                        @else
                            Exciting games and big rewards await you!
                        @endif
                    </p>

                    <a href="{{ $role === 'player' ? $safeRoute('player.game.index') : route('dashboard') }}" class="promo-btn">
                        {{ $role === 'player' ? 'Play Now' : 'Go to Dashboard' }}
                    </a>
                </div>

                <div class="help-card">
                    <div class="help-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M12 18H12.01M9.09 9A3 3 0 1 1 15 10C15 12 12 12.5 12 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="help-title">Need Help?</p>
                        <p class="help-text">Contact our support team</p>
                    </div>
                </div>

                @if(Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf

                        <button type="submit" class="logout-button">
                            Logout
                        </button>
                    </form>
                @endif
            </div>
        </aside>

        <main class="main">
            <header class="topbar">
                <h1 class="page-title">{{ $title }}</h1>

                <div class="topbar-right">
                    <input type="search" class="search-box" placeholder="Search...">

                    <div class="user-box">
                        <div class="user-avatar">{{ $initials }}</div>
                        <div>
                            <p class="user-name">{{ $user?->name ?? 'User' }}</p>
                            <p class="user-role">{{ $roleLabel }}</p>
                        </div>
                    </div>

                    @if(Route::has('logout'))
                        <form method="POST" action="{{ route('logout') }}" class="topbar-logout-form">
                            @csrf

                            <button type="submit" class="topbar-logout-button">
                                Logout
                            </button>
                        </form>
                    @endif
                </div>
            </header>

            <nav class="mobile-menu">
                @foreach($menu as $item)
                    <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <section class="content">
                {{ $slot }}
            </section>
        </main>
    </div>

    @fluxScripts
</body>
</html>