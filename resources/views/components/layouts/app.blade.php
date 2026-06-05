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
            ['label' => 'Commission Withdrawals', 'href' => $safeRoute('admin.commission-withdrawals.index'), 'icon' => 'wallet', 'active' => request()->routeIs('admin.commission-withdrawals.*')],
            ['label' => 'Commission Reports', 'href' => $safeRoute('admin.commission-reports.index'), 'icon' => 'report', 'active' => request()->routeIs('admin.commission-reports.*')],
            ['label' => 'Game Control', 'href' => $safeRoute('admin.games.index'), 'icon' => 'game', 'active' => request()->routeIs('admin.games.*')],
            ['label' => 'KYC Requests', 'href' => $safeRoute('admin.kyc.index'), 'icon' => 'shield', 'active' => request()->routeIs('admin.kyc.*')],
            ['label' => 'Game Reports', 'href' => $safeRoute('admin.reports.games'), 'icon' => 'report', 'active' => request()->routeIs('admin.reports.games')],
            ['label' => 'Wallet Reports', 'href' => $safeRoute('admin.reports.wallet'), 'icon' => 'document', 'active' => request()->routeIs('admin.reports.wallet')],
            ['label' => 'Overview Report', 'href' => $safeRoute('admin.monitoring.overview'), 'icon' => 'report', 'active' => request()->routeIs('admin.monitoring.*')],
            ['label' => 'Activity Logs', 'href' => $safeRoute('admin.activity-logs.index'), 'icon' => 'document', 'active' => request()->routeIs('admin.activity-logs.*')],
            ['label' => 'Agent Hierarchy', 'href' => $safeRoute('admin.agent-hierarchy.index'), 'icon' => 'users', 'active' => request()->routeIs('admin.agent-hierarchy.*')],
            ['label' => 'Agent Reports', 'href' => $safeRoute('admin.agent-reports.index'), 'icon' => 'report', 'active' => request()->routeIs('admin.agent-reports.*')],
        ],

        'agent' => [
            ['label' => 'Dashboard', 'href' => $safeRoute('agent.dashboard'), 'icon' => 'home', 'active' => request()->routeIs('agent.dashboard')],
            ['label' => 'My Wallet', 'href' => $safeRoute('agent.wallet.index'), 'icon' => 'wallet', 'active' => request()->routeIs('agent.wallet.*')],
            ['label' => 'Player Requests', 'href' => $safeRoute('agent.requests.index'), 'icon' => 'document', 'active' => request()->routeIs('agent.requests.*')],
            ['label' => 'My Players', 'href' => $safeRoute('agent.players.index'), 'icon' => 'users', 'active' => request()->routeIs('agent.players.*')],
            ['label' => 'Commissions', 'href' => $safeRoute('agent.commissions.index'), 'icon' => 'report', 'active' => request()->routeIs('agent.commissions.*')],
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

    {{-- Important: this loads resources/css/app.css --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-inner">
                <a href="{{ route('dashboard') }}" class="brand">
                    <div class="brand-mark">⚡</div>
                    <div class="brand-text">SAMPLE <span>SYSTEM</span></div>
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
                            Manage. Convert. <span>Earn.</span>
                        @else
                            Play. Bet. <span>Win.</span>
                        @endif
                    </h3>

                    <p>
                        @if($role === 'admin')
                            Monitor users, games, reports, wallets, and commission withdrawals.
                        @elseif($role === 'agent')
                            Manage player requests, monitor players, and track your commissions.
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
                        <button type="submit" class="logout-button">Logout</button>
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
                            <button type="submit" class="topbar-logout-button">Logout</button>
                        </form>
                    @endif
                </div>
            </header>

            <nav class="mobile-menu">
                @foreach($menu as $item)
                    <a href="{{ $item['href'] }}" class="{{ $item['active'] ? 'active' : '' }}">{{ $item['label'] }}</a>
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
