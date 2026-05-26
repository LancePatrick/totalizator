@php
    $user = auth()->user();
    $playerUser = $player ?? $user;

    $name = $playerUser->name ?? $user->name;
    $initials = strtoupper(substr($name, 0, 1));
    $wallet = number_format($user->wallet_balance ?? 0, 2);
    $agentName = $user->assignedAgentName();
    $status = $user->statusLabel();
    $kyc = $user->kycLabel();
@endphp

<x-app-layout title="Player Dashboard">
    <div class="min-h-screen bg-[#0c5dc7] px-3 py-3 sm:px-4 lg:px-6">
        <div class="mx-auto max-w-[1500px] space-y-3">

            {{-- HERO CARD --}}
            <section class="relative overflow-hidden rounded-[10px] bg-[#061b42] px-5 py-4 text-white shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-r from-[#061b42] via-[#06245f] to-[#0060df]"></div>

                <div class="absolute inset-0 opacity-60"
                    style="background:
                        radial-gradient(circle at 78% 48%, rgba(0,119,255,.75), transparent 17%),
                        radial-gradient(circle at 88% 15%, rgba(44,154,255,.45), transparent 12%),
                        radial-gradient(circle at 70% 86%, rgba(1,75,190,.45), transparent 16%);">
                </div>

                {{-- Wallet Illustration --}}
                <div class="pointer-events-none absolute right-8 top-[-8px] hidden h-[120px] w-[230px] md:block">
                    <div class="absolute right-20 top-5 h-7 w-7 rounded-full bg-gradient-to-br from-yellow-200 to-yellow-500 shadow-lg"></div>
                    <div class="absolute right-8 top-10 h-7 w-7 rounded-full bg-gradient-to-br from-yellow-200 to-yellow-500 shadow-lg"></div>

                    <div class="absolute right-12 top-7 h-[70px] w-[120px] rotate-[-13deg] rounded-xl bg-gradient-to-br from-[#21416e] to-[#112749] shadow-2xl ring-1 ring-white/10">
                        <div class="absolute left-3 top-3 h-2 w-14 rounded bg-white/10"></div>
                        <div class="absolute bottom-3 left-3 h-2 w-20 rounded bg-white/10"></div>
                        <div class="absolute right-[-10px] top-6 h-9 w-12 rounded-lg bg-[#0d2348] ring-1 ring-white/10">
                            <div class="absolute left-3 top-3 h-3 w-3 rounded-full bg-blue-400"></div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 grid grid-cols-1 items-center gap-4 md:grid-cols-[1fr_280px]">
                    <div class="flex items-center gap-4">
                        <div class="flex h-[58px] w-[58px] shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#1d73ff] to-[#2f80ff] text-[26px] font-black text-white shadow-lg">
                            {{ $initials }}
                        </div>

                        <div>
                            <p class="text-[11px] font-bold leading-none text-white/75">Welcome back,</p>

                            <div class="mt-1 flex items-center gap-2">
                                <h1 class="text-[22px] font-black leading-none tracking-tight text-white">
                                    {{ $name }}
                                </h1>

                                <span class="rounded-full border border-blue-300/30 bg-blue-500/15 px-2 py-[3px] text-[10px] font-black text-blue-300">
                                    Player
                                </span>
                            </div>

                            <p class="mt-2 text-[12px] font-semibold text-white/70">
                                Your assigned agent:
                                <span class="font-black text-sky-300">{{ $agentName }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="md:pl-3">
                        <p class="text-[11px] font-bold leading-none text-white/65">Wallet Balance</p>
                        <h2 class="mt-1 text-[31px] font-black leading-none tracking-tight text-white">
                            ₱{{ $wallet }}
                        </h2>
                        <p class="mt-2 text-[11px] font-semibold leading-none text-white/60">
                            Available balance
                        </p>
                    </div>
                </div>
            </section>

            {{-- STATUS CARDS --}}
            <section class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <a href="{{ route('player.wallet.index') }}"
                   class="group rounded-[8px] border border-[#e5eaf1] bg-white px-4 py-3 shadow-sm transition hover:-translate-y-[1px] hover:shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#eef3ff] text-[#4774f6]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 7.5C4 6.12 5.12 5 6.5 5h11C18.88 5 20 6.12 20 7.5v9c0 1.38-1.12 2.5-2.5 2.5h-11C5.12 19 4 17.88 4 16.5v-9Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M15 12h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M7 9h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>

                            <div>
                                <p class="text-[11px] font-bold leading-none text-slate-500">Wallet Balance</p>
                                <h3 class="mt-1 text-[18px] font-black leading-tight text-[#1b376b]">
                                    ₱{{ $wallet }}
                                </h3>
                                <p class="text-[11px] font-semibold leading-none text-slate-400">Available balance</p>
                            </div>
                        </div>

                        <span class="text-lg font-bold text-slate-300 group-hover:text-blue-500">›</span>
                    </div>
                </a>

                <div class="rounded-[8px] border border-[#e5eaf1] bg-white px-4 py-3 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#e9f9f0] text-[#22c55e]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M4 20c.8-4 3.7-6 8-6s7.2 2 8 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>

                            <div>
                                <p class="text-[11px] font-bold leading-none text-slate-500">Account Status</p>
                                <h3 class="mt-1 flex items-center gap-1 text-[18px] font-black leading-tight text-[#16a34a]">
                                    {{ $status }}
                                    <span class="h-[7px] w-[7px] rounded-full bg-[#22c55e]"></span>
                                </h3>
                                <p class="text-[11px] font-semibold leading-none text-slate-400">Player account status</p>
                            </div>
                        </div>

                        <span class="text-lg font-bold text-slate-300">›</span>
                    </div>
                </div>

                <a href="{{ route('player.kyc.index') }}"
                   class="group rounded-[8px] border border-[#e5eaf1] bg-white px-4 py-3 shadow-sm transition hover:-translate-y-[1px] hover:shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#e9f9f0] text-[#22c55e]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 3 19 6v5c0 4.5-2.8 8.5-7 10-4.2-1.5-7-5.5-7-10V6l7-3Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="m9 12 2 2 4-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>

                            <div>
                                <p class="text-[11px] font-bold leading-none text-slate-500">KYC Status</p>
                                <h3 class="mt-1 flex items-center gap-1 text-[18px] font-black leading-tight text-[#16a34a]">
                                    {{ $kyc }}
                                    <span class="h-[7px] w-[7px] rounded-full bg-[#22c55e]"></span>
                                </h3>
                                <p class="text-[11px] font-semibold leading-none text-slate-400">Verification status</p>
                            </div>
                        </div>

                        <span class="text-lg font-bold text-slate-300 group-hover:text-emerald-500">›</span>
                    </div>
                </a>
            </section>

            {{-- MAIN CONTENT --}}
            <section class="grid grid-cols-1 gap-3 xl:grid-cols-[1fr_420px]">

                {{-- PLAYER ACTIONS --}}
                <div class="rounded-[8px] border border-[#e5eaf1] bg-white p-4 shadow-sm">
                    <div class="mb-3">
                        <h2 class="flex items-center gap-2 text-[15px] font-black leading-none text-[#1e355f]">
                            <span class="text-[#3b82f6]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M13 2 4 14h7l-1 8 10-13h-7l1-7Z"/>
                                </svg>
                            </span>
                            Player Actions
                        </h2>
                        <p class="mt-1 text-[11px] font-semibold text-slate-500">
                            Request funds, submit KYC, play the game, and view your activity.
                        </p>
                    </div>

                    <div class="space-y-2">

                        {{-- Request Money --}}
                        <a href="{{ route('player.wallet.index') }}"
                           class="group grid min-h-[48px] grid-cols-[34px_1fr] items-center gap-3 rounded-[7px] border border-[#edf1f6] bg-white px-3 py-2 transition hover:border-blue-200 hover:bg-blue-50/30 sm:grid-cols-[34px_1fr_auto]">
                            <div class="flex h-8 w-8 items-center justify-center rounded-[7px] bg-[#eef5ff] text-[#4d7df7]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 4v10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="m8 10 4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-[12px] font-black leading-tight text-[#1f2f4d]">Request Money</h3>
                                <p class="mt-[2px] text-[10px] font-semibold leading-tight text-slate-500">
                                    Request wallet funds from your assigned agent.
                                </p>
                            </div>

                            <span class="rounded-[5px] bg-[#eef5ff] px-4 py-1.5 text-center text-[10px] font-black text-[#4d7df7]">
                                Request Funds ›
                            </span>
                        </a>

                        {{-- Withdraw Money --}}
                        <a href="{{ route('player.wallet.index') }}"
                           class="group grid min-h-[48px] grid-cols-[34px_1fr] items-center gap-3 rounded-[7px] border border-[#edf1f6] bg-white px-3 py-2 transition hover:border-violet-200 hover:bg-violet-50/30 sm:grid-cols-[34px_1fr_auto]">
                            <div class="flex h-8 w-8 items-center justify-center rounded-[7px] bg-[#f5f0ff] text-[#8b5cf6]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 20V10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="m8 14 4-4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5 5h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-[12px] font-black leading-tight text-[#1f2f4d]">Withdraw Money</h3>
                                <p class="mt-[2px] text-[10px] font-semibold leading-tight text-slate-500">
                                    Request withdrawal from your wallet balance.
                                </p>
                            </div>

                            <span class="rounded-[5px] bg-[#f5f0ff] px-4 py-1.5 text-center text-[10px] font-black text-[#8b5cf6]">
                                Withdraw Funds ›
                            </span>
                        </a>

                        {{-- Submit KYC --}}
                        <a href="{{ route('player.kyc.index') }}"
                           class="group grid min-h-[48px] grid-cols-[34px_1fr] items-center gap-3 rounded-[7px] border border-[#edf1f6] bg-white px-3 py-2 transition hover:border-orange-200 hover:bg-orange-50/30 sm:grid-cols-[34px_1fr_auto]">
                            <div class="flex h-8 w-8 items-center justify-center rounded-[7px] bg-[#fff2ec] text-[#fb6b2a]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 3 19 6v5c0 4.5-2.8 8.5-7 10-4.2-1.5-7-5.5-7-10V6l7-3Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="m9 12 2 2 4-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-[12px] font-black leading-tight text-[#1f2f4d]">Submit KYC</h3>
                                <p class="mt-[2px] text-[10px] font-semibold leading-tight text-slate-500">
                                    Upload your verification details for approval.
                                </p>
                            </div>

                            <span class="rounded-[5px] bg-[#fff2ec] px-4 py-1.5 text-center text-[10px] font-black text-[#fb6b2a]">
                                Submit KYC ›
                            </span>
                        </a>

                        {{-- Play Game --}}
                        <a href="{{ route('player.game.index') }}"
                           class="group grid min-h-[48px] grid-cols-[34px_1fr] items-center gap-3 rounded-[7px] border border-[#edf1f6] bg-white px-3 py-2 transition hover:border-emerald-200 hover:bg-emerald-50/30 sm:grid-cols-[34px_1fr_auto]">
                            <div class="flex h-8 w-8 items-center justify-center rounded-[7px] bg-[#eafaf1] text-[#22c55e]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M7 8h10a4 4 0 0 1 3.8 2.8l1 3.3A3 3 0 0 1 18.9 18c-.9 0-1.7-.4-2.3-1.1L15 15H9l-1.6 1.9A3 3 0 0 1 2.1 14.1l1-3.3A4 4 0 0 1 7 8Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M8 11v4M6 13h4M16 12h.01M18 15h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-[12px] font-black leading-tight text-[#1f2f4d]">Play Game</h3>
                                <p class="mt-[2px] text-[10px] font-semibold leading-tight text-slate-500">
                                    Watch race video and bet on Meron, Wala, or Draw.
                                </p>
                            </div>

                            <span class="rounded-[5px] bg-[#eafaf1] px-4 py-1.5 text-center text-[10px] font-black text-[#22c55e]">
                                Play Now ›
                            </span>
                        </a>

                        {{-- My Bets --}}
                        <a href="{{ route('player.game.index') }}"
                           class="group grid min-h-[48px] grid-cols-[34px_1fr] items-center gap-3 rounded-[7px] border border-[#edf1f6] bg-white px-3 py-2 transition hover:border-amber-200 hover:bg-amber-50/30 sm:grid-cols-[34px_1fr_auto]">
                            <div class="flex h-8 w-8 items-center justify-center rounded-[7px] bg-[#fff7e8] text-[#f59e0b]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M7 5h10v14H7V5Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M9 9h6M9 13h6M9 17h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-[12px] font-black leading-tight text-[#1f2f4d]">My Bets</h3>
                                <p class="mt-[2px] text-[10px] font-semibold leading-tight text-slate-500">
                                    View your game bet records and result status.
                                </p>
                            </div>

                            <span class="rounded-[5px] bg-[#fff7e8] px-4 py-1.5 text-center text-[10px] font-black text-[#f59e0b]">
                                View Bets ›
                            </span>
                        </a>

                        {{-- Transaction History --}}
                        <a href="{{ route('player.wallet.index') }}"
                           class="group grid min-h-[48px] grid-cols-[34px_1fr] items-center gap-3 rounded-[7px] border border-[#edf1f6] bg-white px-3 py-2 transition hover:border-blue-200 hover:bg-blue-50/30 sm:grid-cols-[34px_1fr_auto]">
                            <div class="flex h-8 w-8 items-center justify-center rounded-[7px] bg-[#eef5ff] text-[#4d7df7]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-[12px] font-black leading-tight text-[#1f2f4d]">Transaction History</h3>
                                <p class="mt-[2px] text-[10px] font-semibold leading-tight text-slate-500">
                                    View your transaction history and account activities.
                                </p>
                            </div>

                            <span class="rounded-[5px] bg-[#eef5ff] px-4 py-1.5 text-center text-[10px] font-black text-[#4d7df7]">
                                View History ›
                            </span>
                        </a>
                    </div>
                </div>

                {{-- RIGHT SIDE --}}
                <div class="space-y-3">

                    {{-- RECENT ACTIVITY --}}
                    <div class="rounded-[8px] border border-[#e5eaf1] bg-white p-4 shadow-sm">
                        <div class="mb-3 flex items-center justify-between">
                            <h2 class="text-[14px] font-black text-[#1e355f]">Recent Activity</h2>
                            <a href="{{ route('player.wallet.index') }}" class="text-[10px] font-black text-[#4d7df7]">
                                View All
                            </a>
                        </div>

                        <div class="space-y-3">
                            <div class="grid grid-cols-[32px_1fr_auto] items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-[7px] bg-[#eafaf1] text-[#22c55e]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black leading-tight text-[#1f2f4d]">KYC Verification</p>
                                    <p class="text-[10px] font-semibold leading-tight text-slate-500">Verification approved</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black leading-tight text-[#22c55e]">Approved</p>
                                    <p class="mt-[2px] text-[9px] font-semibold text-slate-400">Today</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-[32px_1fr_auto] items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-[7px] bg-[#eafaf1] text-[#22c55e]">
                                    <span class="text-sm font-black">₱</span>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black leading-tight text-[#1f2f4d]">Wallet Funded</p>
                                    <p class="text-[10px] font-semibold leading-tight text-slate-500">Agent: {{ $agentName }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black leading-tight text-[#22c55e]">+ ₱{{ $wallet }}</p>
                                    <p class="mt-[2px] text-[9px] font-semibold text-slate-400">Current</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-[32px_1fr_auto] items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-[7px] bg-[#f5f0ff] text-[#8b5cf6]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <path d="M7 8h10a4 4 0 0 1 3.8 2.8l1 3.3A3 3 0 0 1 18.9 18c-.9 0-1.7-.4-2.3-1.1L15 15H9l-1.6 1.9A3 3 0 0 1 2.1 14.1l1-3.3A4 4 0 0 1 7 8Z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black leading-tight text-[#1f2f4d]">Game Played</p>
                                    <p class="text-[10px] font-semibold leading-tight text-slate-500">Meron · Bet: ₱50.00</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black leading-tight text-[#ef4444]">- ₱50.00</p>
                                    <p class="mt-[2px] text-[9px] font-semibold text-slate-400">May 21</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-[32px_1fr_auto] items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-[7px] bg-[#ffeef7] text-[#ec4899]">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 20V10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="m8 14 4-4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black leading-tight text-[#1f2f4d]">Withdrawal Request</p>
                                    <p class="text-[10px] font-semibold leading-tight text-slate-500">To e-Wallet</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black leading-tight text-[#ef4444]">- ₱100.00</p>
                                    <p class="mt-[2px] text-[9px] font-semibold text-slate-400">May 20</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- QUICK STATS --}}
                    <div class="rounded-[8px] border border-[#e5eaf1] bg-white p-4 shadow-sm">
                        <div class="mb-3 flex items-center justify-between">
                            <h2 class="text-[14px] font-black text-[#1e355f]">Quick Stats</h2>
                            <button type="button" class="rounded-[5px] border border-[#e5eaf1] bg-white px-3 py-1 text-[10px] font-black text-slate-500">
                                This Week
                            </button>
                        </div>

                        <div class="rounded-[8px] border border-[#edf1f6] bg-white p-3">
                            <div class="grid grid-cols-[1fr_1fr_150px] items-end gap-3">
                                <div>
                                    <p class="text-[10px] font-bold leading-none text-slate-500">Total Bets</p>
                                    <h3 class="mt-2 text-[18px] font-black leading-none text-[#1f2f4d]">12</h3>
                                    <p class="mt-2 text-[9px] font-black leading-none text-[#22c55e]">▲ 20% vs last week</p>
                                </div>

                                <div>
                                    <p class="text-[10px] font-bold leading-none text-slate-500">Net Result</p>
                                    <h3 class="mt-2 text-[18px] font-black leading-none text-[#1f2f4d]">₱71.00</h3>
                                    <p class="mt-2 text-[9px] font-black leading-none text-[#22c55e]">▲ 15% vs last week</p>
                                </div>

                                <div class="h-[70px]">
                                    <svg viewBox="0 0 150 70" class="h-full w-full">
                                        <defs>
                                            <linearGradient id="chartFill" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#60a5fa" stop-opacity=".22"/>
                                                <stop offset="100%" stop-color="#60a5fa" stop-opacity="0"/>
                                            </linearGradient>
                                        </defs>

                                        <path d="M5 60 L25 40 L45 45 L62 20 L78 55 L95 15 L113 48 L130 28 L145 36 L145 70 L5 70 Z"
                                              fill="url(#chartFill)" />

                                        <path d="M5 60 L25 40 L45 45 L62 20 L78 55 L95 15 L113 48 L130 28 L145 36"
                                              fill="none"
                                              stroke="#60a5fa"
                                              stroke-width="3"
                                              stroke-linecap="round"
                                              stroke-linejoin="round" />

                                        <circle cx="25" cy="40" r="3" fill="#60a5fa"/>
                                        <circle cx="62" cy="20" r="3" fill="#60a5fa"/>
                                        <circle cx="95" cy="15" r="3" fill="#60a5fa"/>
                                        <circle cx="130" cy="28" r="3" fill="#60a5fa"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            {{-- FOOTER --}}
            <footer class="flex flex-col justify-between gap-2 px-1 pb-2 text-[10px] font-semibold text-slate-400 sm:flex-row">
                <p>© 2024 Lucky Volt. All rights reserved.</p>

                <div class="flex gap-5">
                    <a href="#" class="font-bold text-[#4d7df7] hover:underline">Terms &amp; Conditions</a>
                    <a href="#" class="font-bold text-[#4d7df7] hover:underline">Privacy Policy</a>
                </div>
            </footer>

        </div>
    </div>
</x-app-layout>