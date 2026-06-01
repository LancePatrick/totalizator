<x-layouts.app :title="__('Admin Dashboard')">
    @php
        $user = auth()->user();
        $current = $currentGame ?? null;

        $gameStatus = $current?->status ?? 'No Game';

        $statusColorClass = match ($current?->status) {
            'open' => 'bg-green-100 text-green-700',
            'waiting' => 'bg-amber-100 text-amber-700',
            'closed' => 'bg-blue-100 text-blue-700',
            'ended' => 'bg-red-100 text-red-700',
            'settled' => 'bg-purple-100 text-purple-700',
            default => 'bg-slate-100 text-slate-500',
        };

        $safeRoute = function ($routeName, $fallback = 'admin.dashboard') {
            return Route::has($routeName) ? route($routeName) : route($fallback);
        };
    @endphp

    <div class="flex flex-col gap-[18px]">
        <section class="relative overflow-hidden rounded-[24px] bg-[radial-gradient(circle_at_82%_45%,rgba(29,124,255,0.65),transparent_26%),radial-gradient(circle_at_18%_20%,rgba(250,204,21,0.16),transparent_22%),linear-gradient(135deg,#03142f_0%,#041a4d_52%,#064e3b_100%)] p-[30px] text-white shadow-[0_18px_42px_rgba(2,18,54,0.16)] max-[600px]:p-6">
            <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(90deg,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[length:54px_54px] opacity-45"></div>
            <div class="pointer-events-none absolute right-[42px] top-2 rotate-[-8deg] text-[120px] font-black leading-none text-white/10 max-[900px]:hidden">
                AD
            </div>

            <div class="relative z-10 grid grid-cols-[minmax(0,1fr)_300px] items-center gap-6 max-[900px]:grid-cols-1">
                <div>
                    <p class="m-0 text-xs font-black uppercase tracking-[0.18em] text-sky-400">Admin Panel</p>
                    <h1 class="mb-0 mt-2 text-[36px] font-black leading-[1.1] tracking-[-0.04em] text-white max-[600px]:text-[28px]">
                        Full System Access
                    </h1>
                    <p class="mb-0 mt-2.5 max-w-[820px] text-sm font-bold leading-[1.6] text-white/75">
                        Manage agents, players, KYC, wallet requests, withdrawals, game rounds, odds, payouts, reports, activity logs, and agent monitoring.
                    </p>

                    <div class="mt-[18px] flex flex-wrap gap-2.5">
                        <a href="{{ $safeRoute('admin.games.index') }}" class="inline-flex min-h-[42px] items-center justify-center rounded-[13px] border border-blue-600 bg-blue-600 px-4 text-[13px] font-black text-white no-underline shadow-[0_12px_24px_rgba(37,99,235,0.22)]">
                            Open Game Control
                        </a>

                        <a href="{{ $safeRoute('admin.monitoring.overview') }}" class="inline-flex min-h-[42px] items-center justify-center rounded-[13px] border border-white/20 bg-white/10 px-4 text-[13px] font-black text-white no-underline backdrop-blur-[10px]">
                            Overview Report
                        </a>

                        <a href="{{ $safeRoute('admin.activity-logs.index') }}" class="inline-flex min-h-[42px] items-center justify-center rounded-[13px] border border-white/20 bg-white/10 px-4 text-[13px] font-black text-white no-underline backdrop-blur-[10px]">
                            Activity Logs
                        </a>
                    </div>
                </div>

                <div class="rounded-[20px] border border-white/15 bg-white/10 p-5 backdrop-blur-[10px]">
                    <p class="m-0 text-xs font-black uppercase tracking-[0.12em] text-white/65">Logged In As</p>
                    <h2 class="mb-0 mt-2 text-[25px] font-black tracking-[-0.03em] text-yellow-400">{{ $user?->name ?? 'Admin' }}</h2>
                    <p class="mb-0 mt-1 text-[13px] font-extrabold text-white/80">{{ $user?->roleLabel() ?? 'Admin' }}</p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-5 gap-3.5 max-[1400px]:grid-cols-3 max-[600px]:grid-cols-1">
            <div class="min-h-[126px] rounded-[22px] border border-[#dce6f2] bg-white p-[18px] shadow-[0_10px_24px_rgba(15,23,42,0.045)] transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_16px_30px_rgba(15,23,42,0.07)]">
                <p class="m-0 text-[13px] font-extrabold text-slate-500">Total Agents</p>
                <h2 class="mb-0 mt-3 text-[34px] font-black leading-none tracking-[-0.04em] text-slate-900">{{ number_format($totalAgents ?? 0) }}</h2>
                <p class="mb-0 mt-2 text-xs font-bold text-slate-500">Registered agents</p>
            </div>

            <div class="min-h-[126px] rounded-[22px] border border-[#dce6f2] bg-white p-[18px] shadow-[0_10px_24px_rgba(15,23,42,0.045)] transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_16px_30px_rgba(15,23,42,0.07)]">
                <p class="m-0 text-[13px] font-extrabold text-slate-500">Total Players</p>
                <h2 class="mb-0 mt-3 text-[34px] font-black leading-none tracking-[-0.04em] text-slate-900">{{ number_format($totalPlayers ?? 0) }}</h2>
                <p class="mb-0 mt-2 text-xs font-bold text-slate-500">Registered players</p>
            </div>

            <div class="min-h-[126px] rounded-[22px] border border-[#dce6f2] bg-white p-[18px] shadow-[0_10px_24px_rgba(15,23,42,0.045)] transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_16px_30px_rgba(15,23,42,0.07)]">
                <p class="m-0 text-[13px] font-extrabold text-slate-500">Active Agents</p>
                <h2 class="mb-0 mt-3 text-[34px] font-black leading-none tracking-[-0.04em] text-green-600">{{ number_format($activeAgents ?? 0) }}</h2>
                <p class="mb-0 mt-2 text-xs font-bold text-slate-500">Can manage players</p>
            </div>

            <div class="min-h-[126px] rounded-[22px] border border-[#dce6f2] bg-white p-[18px] shadow-[0_10px_24px_rgba(15,23,42,0.045)] transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_16px_30px_rgba(15,23,42,0.07)]">
                <p class="m-0 text-[13px] font-extrabold text-slate-500">Active Players</p>
                <h2 class="mb-0 mt-3 text-[34px] font-black leading-none tracking-[-0.04em] text-green-600">{{ number_format($activePlayers ?? 0) }}</h2>
                <p class="mb-0 mt-2 text-xs font-bold text-slate-500">Allowed accounts</p>
            </div>

            <div class="min-h-[126px] rounded-[22px] border border-[#dce6f2] bg-white p-[18px] shadow-[0_10px_24px_rgba(15,23,42,0.045)] transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_16px_30px_rgba(15,23,42,0.07)]">
                <p class="m-0 text-[13px] font-extrabold text-slate-500">Pending KYC</p>
                <h2 class="mb-0 mt-3 text-[34px] font-black leading-none tracking-[-0.04em] text-amber-600">{{ number_format($pendingKyc ?? 0) }}</h2>
                <p class="mb-0 mt-2 text-xs font-bold text-slate-500">Waiting for review</p>
            </div>
        </section>

        <section class="grid grid-cols-[minmax(0,1fr)_390px] items-start gap-[18px] max-[1400px]:grid-cols-1">
            <div class="rounded-[22px] border border-[#dce6f2] bg-white p-5 shadow-[0_10px_24px_rgba(15,23,42,0.045)]">
                <div class="mb-[18px] flex items-start justify-between gap-3.5 max-[900px]:flex-col">
                    <div>
                        <h2 class="m-0 text-[22px] font-black tracking-[-0.03em] text-slate-900">Admin Actions</h2>
                        <p class="mb-0 mt-1.5 text-[13px] font-bold leading-normal text-slate-500">
                            Main controls for users, money, games, monitoring, and reports.
                        </p>
                    </div>

                    <a href="{{ $safeRoute('admin.games.index') }}" class="inline-flex min-h-[42px] items-center justify-center whitespace-nowrap rounded-[13px] bg-blue-600 px-4 text-[13px] font-black text-white no-underline shadow-[0_12px_24px_rgba(37,99,235,0.18)]">
                        Open Game Control
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-3.5 max-[900px]:grid-cols-1">
                    <a href="{{ $safeRoute('admin.agents.index') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-green-600 text-sm font-black text-white">AG</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">Manage Agents</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">Activate, deactivate, and review agent accounts.</p>
                    </a>

                    <a href="{{ $safeRoute('admin.players.index') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-blue-600 text-sm font-black text-white">PL</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">Manage Players</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">View player accounts, agents, status, and wallets.</p>
                    </a>

                    <a href="{{ $safeRoute('admin.kyc.index') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-orange-600 text-sm font-black text-white">KY</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">KYC Requests</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">Approve or reject player verification documents.</p>
                    </a>

                    <a href="{{ $safeRoute('admin.money-requests.index') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-yellow-400 text-sm font-black text-slate-900">₱</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">Money Requests</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">Review agent funding and player wallet requests.</p>
                    </a>

                    <a href="{{ $safeRoute('admin.games.index') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-purple-600 text-sm font-black text-white">GM</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">Game Control</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">Create game, start betting, end game, and declare result.</p>
                    </a>

                    <a href="{{ $safeRoute('admin.reports.games') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-slate-900 text-sm font-black text-white">GR</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">Game Reports</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">View game totals, pools, commission, payouts, and logs.</p>
                    </a>

                    <a href="{{ $safeRoute('admin.reports.wallet') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-cyan-600 text-sm font-black text-white">WR</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">Wallet Reports</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">Review wallet transaction reports and balances.</p>
                    </a>

                    <a href="{{ $safeRoute('admin.monitoring.overview') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-blue-600 text-sm font-black text-white">OV</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">Overview Report</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">Audit loading, withdrawals, bets, commissions, and wallets.</p>
                    </a>

                    <a href="{{ $safeRoute('admin.activity-logs.index') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-slate-600 text-sm font-black text-white">LG</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">Activity Logs</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">Track wallet movements, payouts, refunds, and commission logs.</p>
                    </a>

                    <a href="{{ $safeRoute('admin.agent-hierarchy.index') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-green-600 text-sm font-black text-white">AH</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">Agent Hierarchy</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">View agents, their players, total bets, and commission details.</p>
                    </a>

                    <a href="{{ $safeRoute('admin.agent-reports.index') }}" class="min-h-[158px] rounded-[18px] border border-[#e7edf6] bg-white p-[18px] text-inherit no-underline transition duration-150 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_14px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex h-[46px] w-[46px] items-center justify-center rounded-[14px] bg-orange-600 text-sm font-black text-white">AR</div>
                        <h3 class="mb-0 mt-3.5 text-[17px] font-black text-slate-900">Agent Reports</h3>
                        <p class="mb-0 mt-[7px] text-[13px] font-bold leading-normal text-slate-500">Check agent wallet, player bets, and computed 2% commission.</p>
                    </a>
                </div>
            </div>

            <div class="flex flex-col gap-[18px]">
                <div class="rounded-[22px] border border-[#dce6f2] bg-white p-5 shadow-[0_10px_24px_rgba(15,23,42,0.045)]">
                    <div class="mb-[18px] flex items-start justify-between gap-3.5 max-[900px]:flex-col">
                        <div>
                            <h2 class="m-0 text-[22px] font-black tracking-[-0.03em] text-slate-900">Game Snapshot</h2>
                            <p class="mb-0 mt-1.5 text-[13px] font-bold leading-normal text-slate-500">Quick access to current totalizator status.</p>
                        </div>

                        <span class="inline-flex rounded-full px-3 py-[7px] text-xs font-black uppercase {{ $statusColorClass }}">
                            {{ $gameStatus }}
                        </span>
                    </div>

                    <div class="mt-4 flex flex-col gap-3">
                        <div class="rounded-2xl border border-orange-200 bg-orange-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="m-0 text-sm font-black text-orange-600">Meron</p>
                                <span class="rounded-full bg-orange-200 px-2.5 py-1 text-xs font-black text-orange-800">
                                    {{ number_format($current->meron_odds ?? 0, 2) }}x
                                </span>
                            </div>
                            <h3 class="mb-0 mt-2.5 text-2xl font-black text-slate-900">₱{{ number_format($current->meron_total ?? 0, 2) }}</h3>
                        </div>

                        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="m-0 text-sm font-black text-blue-600">Wala</p>
                                <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-black text-blue-700">
                                    {{ number_format($current->wala_odds ?? 0, 2) }}x
                                </span>
                            </div>
                            <h3 class="mb-0 mt-2.5 text-2xl font-black text-slate-900">₱{{ number_format($current->wala_total ?? 0, 2) }}</h3>
                        </div>

                        <div class="rounded-2xl border border-purple-200 bg-purple-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="m-0 text-sm font-black text-purple-600">Draw</p>
                                <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-black text-purple-700">
                                    {{ number_format($current->draw_odds ?? 0, 2) }}x
                                </span>
                            </div>
                            <h3 class="mb-0 mt-2.5 text-2xl font-black text-slate-900">₱{{ number_format($current->draw_total ?? 0, 2) }}</h3>
                        </div>
                    </div>

                    <div class="mt-3 rounded-[18px] bg-slate-900 p-[18px] text-white">
                        <p class="m-0 text-xs font-black uppercase tracking-[0.12em] text-white/55">Total Pool</p>
                        <h3 class="mb-0 mt-2 text-[30px] font-black text-yellow-400">₱{{ number_format($current->total_pool ?? 0, 2) }}</h3>
                        <p class="mb-0 mt-1.5 text-[13px] font-bold text-white/65">
                            Net Pool: ₱{{ number_format($current->net_pool ?? 0, 2) }}
                        </p>
                    </div>

                    <a href="{{ $safeRoute('admin.games.index') }}" class="mt-3.5 inline-flex min-h-[42px] w-full items-center justify-center whitespace-nowrap rounded-[13px] bg-blue-600 px-4 text-[13px] font-black text-white no-underline shadow-[0_12px_24px_rgba(37,99,235,0.18)]">
                        Go to Game Control
                    </a>
                </div>

                <div class="rounded-[22px] border border-[#dce6f2] bg-white p-5 shadow-[0_10px_24px_rgba(15,23,42,0.045)]">
                    <h2 class="m-0 text-[22px] font-black tracking-[-0.03em] text-slate-900">System Status</h2>
                    <p class="mb-0 mt-1.5 text-[13px] font-bold leading-normal text-slate-500">Main services overview.</p>

                    <div class="mt-3.5 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                            <span class="text-[13px] font-extrabold text-slate-500">Auth</span>
                            <span class="rounded-full bg-green-100 px-2.5 py-1.5 text-[11px] font-black uppercase text-green-700">Online</span>
                        </div>

                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                            <span class="text-[13px] font-extrabold text-slate-500">Database</span>
                            <span class="rounded-full bg-green-100 px-2.5 py-1.5 text-[11px] font-black uppercase text-green-700">Connected</span>
                        </div>

                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                            <span class="text-[13px] font-extrabold text-slate-500">Game Engine</span>
                            <span class="rounded-full bg-green-100 px-2.5 py-1.5 text-[11px] font-black uppercase text-green-700">Setup</span>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-[13px] font-extrabold text-slate-500">Monitoring Reports</span>
                            <span class="rounded-full bg-green-100 px-2.5 py-1.5 text-[11px] font-black uppercase text-green-700">Added</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
