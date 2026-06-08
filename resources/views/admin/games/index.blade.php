<x-layouts.app :title="__('Admin Game Control')">
    @php
        $selectedGameId = request('game_id') ?: $currentGame?->id;
        $hasSelectedGame = request()->filled('game_id') && $currentGame;

        $status = $currentGame?->status ?? 'no_game';

        $statusColor = match ($status) {
            'open' => '#16a34a',
            'waiting' => '#d97706',
            'closed' => '#2563eb',
            'ended' => '#dc2626',
            'settled' => '#7c3aed',
            default => '#64748b',
        };

        $statusBg = match ($status) {
            'open' => '#dcfce7',
            'waiting' => '#fef3c7',
            'closed' => '#dbeafe',
            'ended' => '#fee2e2',
            'settled' => '#f3e8ff',
            default => '#f1f5f9',
        };

        $meronTotal = (float) ($currentGame->meron_total ?? 0);
        $walaTotal = (float) ($currentGame->wala_total ?? 0);
        $drawTotal = (float) ($currentGame->draw_total ?? 0);
        $totalPool = (float) ($currentGame->total_pool ?? 0);
        $netPool = (float) ($currentGame->net_pool ?? 0);

        $meronOdds = (float) ($currentGame->meron_odds ?? 0);
        $walaOdds = (float) ($currentGame->wala_odds ?? 0);
        $drawOdds = (float) ($currentGame->draw_odds ?? 0);

        $videoUrl = $currentGame->video_url ?? null;
        $youtubeEmbedUrl = null;

        if ($videoUrl) {
            if (preg_match('/youtu\.be\/([^?&]+)/', $videoUrl, $matches)) {
                $youtubeEmbedUrl = 'https://www.youtube.com/embed/' . $matches[1];
            } elseif (preg_match('/youtube\.com\/watch\?v=([^?&]+)/', $videoUrl, $matches)) {
                $youtubeEmbedUrl = 'https://www.youtube.com/embed/' . $matches[1];
            } elseif (preg_match('/youtube\.com\/embed\/([^?&]+)/', $videoUrl, $matches)) {
                $youtubeEmbedUrl = $videoUrl;
            }
        }

        $cards = [
            [
                'label' => 'Meron Bets',
                'value' => '₱' . number_format($meronTotal, 2),
                'sub' => 'Odds ' . number_format($meronOdds, 2) . 'x',
                'valueKey' => 'meron_total',
                'subKey' => 'meron_odds',
                'box' => 'border-orange-200 bg-orange-50',
                'text' => 'text-orange-600',
            ],
            [
                'label' => 'Wala Bets',
                'value' => '₱' . number_format($walaTotal, 2),
                'sub' => 'Odds ' . number_format($walaOdds, 2) . 'x',
                'valueKey' => 'wala_total',
                'subKey' => 'wala_odds',
                'box' => 'border-blue-200 bg-blue-50',
                'text' => 'text-blue-600',
            ],
            [
                'label' => 'Draw Bets',
                'value' => '₱' . number_format($drawTotal, 2),
                'sub' => 'Odds ' . number_format($drawOdds, 2) . 'x',
                'valueKey' => 'draw_total',
                'subKey' => 'draw_odds',
                'box' => 'border-violet-200 bg-violet-50',
                'text' => 'text-violet-600',
            ],
            [
                'label' => 'Total Pool',
                'value' => '₱' . number_format($totalPool, 2),
                'sub' => 'Net ₱' . number_format($netPool, 2),
                'valueKey' => 'total_pool',
                'subKey' => 'net_pool',
                'box' => 'border-slate-900 bg-slate-950',
                'text' => 'text-yellow-400',
            ],
        ];
    @endphp

    <div class="space-y-5">
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-blue-950 to-blue-700 p-6 text-white shadow-xl sm:p-8">
            <div class="absolute inset-0 opacity-30 [background-image:linear-gradient(90deg,rgba(255,255,255,.08)_1px,transparent_1px),linear-gradient(rgba(255,255,255,.06)_1px,transparent_1px)] [background-size:54px_54px]"></div>
            <div class="absolute -right-3 top-2 hidden rotate-[-10deg] text-8xl drop-shadow-2xl md:block">🎮</div>

            <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.22em] text-cyan-300">
                        Admin Game Control
                    </p>

                    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                        Game Rooms Totalizator
                    </h1>

                    <p class="mt-3 max-w-4xl text-sm font-bold leading-6 text-white/80">
                        Create game rooms, click a room to enter it, then start betting, close betting, end game, and declare result.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur lg:min-w-[260px]">
                    <p class="text-xs font-black uppercase tracking-[.16em] text-white/70">
                        Auto Commission
                    </p>

                    <h2 class="mt-2 text-4xl font-black text-yellow-400">
                        5%
                    </h2>

                    <p class="mt-2 text-xs font-bold text-white/75">
                        Company 3% • Agent 2%
                    </p>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-100 px-4 py-3 text-sm font-extrabold text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-100 px-4 py-3 text-sm font-extrabold text-red-800">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if($hasSelectedGame)
            <div class="flex flex-col gap-3 rounded-3xl border border-blue-100 bg-blue-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.18em] text-blue-600">
                        Selected Room
                    </p>

                    <h2 class="mt-1 text-xl font-black text-slate-950">
                        {{ $currentGame->title ?? $currentGame->round_name ?? 'Game Room' }}
                    </h2>

                    <p class="mt-1 text-sm font-bold text-slate-500">
                        Room Code: {{ $currentGame->round_code ?? $currentGame->round_number ?? $currentGame->id }}
                    </p>
                </div>

                <a
                    href="{{ route('admin.games.index') }}"
                    class="inline-flex h-11 items-center justify-center rounded-2xl bg-slate-950 px-5 text-xs font-black uppercase tracking-wide text-white transition hover:-translate-y-0.5 hover:bg-slate-800"
                >
                    ← Back to Game List
                </a>
            </div>
        @endif

        <section class="grid gap-5 xl:grid-cols-[380px_minmax(0,1fr)]">
            <aside class="space-y-5">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-2xl font-black tracking-tight text-slate-950">
                        Create Game Room
                    </h2>

                    <p class="mt-1 text-sm font-bold text-slate-500">
                        Create any title. Room code is auto-generated if blank.
                    </p>

                    <form method="POST" action="{{ route('admin.games.store') }}" class="mt-5 space-y-4">
                        @csrf

                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[.14em] text-slate-600">
                                Game Title
                            </label>

                            <input
                                name="game_title"
                                required
                                value="{{ old('game_title') }}"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-950 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                placeholder="Horse Racing, Basketball Finals, Volleyball Match"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[.14em] text-slate-600">
                                Room Code Optional
                            </label>

                            <input
                                name="round_number"
                                value="{{ old('round_number') }}"
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-950 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                placeholder="Auto if blank"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[.14em] text-slate-600">
                                YouTube / Video URL
                            </label>

                            <textarea
                                name="video_url"
                                rows="3"
                                class="w-full resize-y rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-950 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                placeholder="https://youtube.com/watch?v=... or https://youtu.be/..."
                            >{{ old('video_url') }}</textarea>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-black text-slate-950">
                                Commission is auto-computed.
                            </p>

                            <p class="mt-1 text-xs font-bold leading-5 text-slate-500">
                                5% total commission: 3% company and 2% agent.
                            </p>
                        </div>

                        <button class="h-12 w-full rounded-2xl bg-blue-600 text-sm font-black uppercase tracking-wide text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                            Create Game Room
                        </button>
                    </form>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-black tracking-tight text-slate-950">
                                Game Rooms
                            </h2>

                            <p class="mt-1 text-xs font-bold text-slate-500">
                                Ended rooms are hidden. Declared rooms stay visible.
                            </p>
                        </div>

                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black uppercase text-blue-700">
                            {{ $gameList->count() }} rooms
                        </span>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($gameList as $game)
                            @php
                                $gameStatus = strtolower($game->status ?? 'waiting');

                                $gameStatusClass = match ($gameStatus) {
                                    'open' => 'bg-green-100 text-green-700',
                                    'waiting' => 'bg-yellow-100 text-yellow-700',
                                    'closed' => 'bg-blue-100 text-blue-700',
                                    'settled' => 'bg-violet-100 text-violet-700',
                                    default => 'bg-slate-100 text-slate-600',
                                };

                                $activeClass = (int) $selectedGameId === (int) $game->id && $hasSelectedGame
                                    ? 'border-blue-500 bg-blue-50 ring-4 ring-blue-100'
                                    : 'border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50 hover:shadow-lg hover:shadow-blue-100/60';
                            @endphp

                            <a
                                href="{{ route('admin.games.index', ['game_id' => $game->id]) }}"
                                class="group block rounded-3xl border p-4 transition duration-200 hover:-translate-y-1 {{ $activeClass }}"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-black text-slate-950 transition group-hover:text-blue-700">
                                            {{ $game->title ?? $game->round_name ?? 'Game Room' }}
                                        </p>

                                        <p class="mt-1 text-xs font-bold text-slate-500">
                                            Room: {{ $game->round_code ?? $game->round_number ?? $game->id }}
                                        </p>

                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase text-slate-600">
                                                Pool ₱{{ number_format((float) ($game->total_pool ?? 0), 2) }}
                                            </span>

                                            <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase {{ $gameStatusClass }}">
                                                {{ $gameStatus }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-md shadow-blue-600/20 transition group-hover:scale-110">
                                        →
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm font-black text-slate-500">
                                No game rooms.
                            </div>
                        @endforelse
                    </div>
                </div>
            </aside>

            <main class="space-y-5">
                @if(!$hasSelectedGame)
                    <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <div class="mx-auto max-w-2xl text-center">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-blue-50 text-4xl">
                                🎮
                            </div>

                            <h2 class="mt-5 text-3xl font-black tracking-tight text-slate-950">
                                Select a Game Room
                            </h2>

                            <p class="mt-3 text-sm font-bold leading-6 text-slate-500">
                                Click any room from the list on the left. Ended rooms are hidden, but declared rooms stay visible.
                            </p>
                        </div>
                    </section>
                @else
                    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-2xl font-black tracking-tight text-slate-950" data-live="game_title">
                                    {{ $currentGame->title ?? $currentGame->round_name ?? 'Selected Game Room' }}
                                </h2>

                                <p class="mt-1 text-sm font-bold text-slate-500">
                                    Live video, totals, odds, status, and room controls.
                                </p>

                                <p class="mt-2 text-xs font-black uppercase tracking-[.14em] text-slate-400">
                                    Room:
                                    <span data-live="round_code">
                                        {{ $currentGame->round_code ?? $currentGame->round_number ?? $currentGame->id }}
                                    </span>
                                </p>
                            </div>

                            <span
                                data-live="game_status"
                                class="inline-flex w-fit items-center justify-center rounded-full px-3 py-2 text-xs font-black uppercase"
                                style="background:{{ $statusBg }}; color:{{ $statusColor }};"
                            >
                                {{ strtoupper($currentGame->status) }}
                            </span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach($cards as $card)
                                <div class="rounded-2xl border p-4 {{ $card['box'] }}">
                                    <p class="text-xs font-black uppercase tracking-[.14em] {{ $card['text'] }}">
                                        {{ $card['label'] }}
                                    </p>

                                    <h3
                                        class="mt-2 text-2xl font-black tracking-tight {{ $card['box'] === 'border-slate-900 bg-slate-950' ? 'text-yellow-400' : 'text-slate-950' }}"
                                        data-live="{{ $card['valueKey'] }}"
                                    >
                                        {{ $card['value'] }}
                                    </h3>

                                    <p
                                        class="mt-2 text-sm font-black {{ $card['box'] === 'border-slate-900 bg-slate-950' ? 'text-white/70' : $card['text'] }}"
                                        data-live="{{ $card['subKey'] }}"
                                    >
                                        {{ $card['sub'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5 rounded-3xl bg-slate-950 p-3 sm:p-4">
                            @if($youtubeEmbedUrl)
                                <iframe
                                    class="aspect-video w-full rounded-2xl border border-white/10 bg-black"
                                    src="{{ $youtubeEmbedUrl }}"
                                    title="Game Video"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                ></iframe>
                            @elseif($videoUrl)
                                <video class="aspect-video w-full rounded-2xl border border-white/10 bg-black object-contain" controls playsinline>
                                    <source src="{{ $videoUrl }}">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <div class="flex aspect-video w-full items-center justify-center rounded-2xl border border-white/10 bg-gradient-to-br from-slate-950 to-slate-800 text-center font-black text-white/70">
                                    <div>
                                        <div class="text-5xl">🎥</div>
                                        <div class="mt-3">No video loaded yet.</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 grid gap-3 lg:grid-cols-[1fr_1fr_1fr_2fr]">
                            <form method="POST" action="{{ route('admin.games.start', $currentGame) }}">
                                @csrf
                                <button class="h-12 w-full rounded-2xl bg-green-600 text-xs font-black uppercase tracking-wide text-white transition hover:-translate-y-0.5 hover:bg-green-700">
                                    Start This Room
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.games.close', $currentGame) }}">
                                @csrf
                                <button class="h-12 w-full rounded-2xl bg-orange-500 text-xs font-black uppercase tracking-wide text-white transition hover:-translate-y-0.5 hover:bg-orange-600">
                                    Close Betting
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.games.end', $currentGame) }}">
                                @csrf
                                <button class="h-12 w-full rounded-2xl bg-slate-950 text-xs font-black uppercase tracking-wide text-white transition hover:-translate-y-0.5 hover:bg-slate-800">
                                    End Game
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.games.declare', $currentGame) }}" class="grid gap-3 sm:grid-cols-[1fr_auto]">
                                @csrf

                                <select
                                    name="winning_side"
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-black text-slate-950 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                >
                                    <option value="meron">Meron</option>
                                    <option value="wala">Wala</option>
                                    <option value="draw">Draw</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>

                                <button class="h-12 rounded-2xl bg-yellow-400 px-6 text-xs font-black uppercase tracking-wide text-slate-950 transition hover:-translate-y-0.5 hover:bg-yellow-300">
                                    Declare
                                </button>
                            </form>
                        </div>
                    </section>
                @endif

                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div>
                        <h2 class="text-2xl font-black tracking-tight text-slate-950">
                            Logrohan / Result History
                        </h2>

                        <p class="mt-1 text-sm font-bold text-slate-500">
                            Latest declared game results.
                        </p>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2" id="adminLiveLogrohan">
                        @forelse($logrohan as $entry)
                            @php
                                $result = strtolower($entry->result ?? $entry->winning_side ?? 'cancelled');

                                if (in_array($result, ['cancel', 'canceled'])) {
                                    $result = 'cancelled';
                                }

                                if (!in_array($result, ['meron', 'wala', 'draw', 'cancelled'])) {
                                    $result = 'cancelled';
                                }

                                $pillClass = match ($result) {
                                    'meron' => 'bg-red-100 text-red-700',
                                    'wala' => 'bg-blue-100 text-blue-700',
                                    'draw' => 'bg-yellow-100 text-yellow-700',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp

                            <span class="rounded-full px-3 py-2 text-xs font-black uppercase {{ $pillClass }}">
                                {{ $entry->round_number ?? $entry->round_code ?? 'Room' }} - {{ strtoupper($result) }}
                            </span>
                        @empty
                            <div class="w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm font-black text-slate-500">
                                No results yet.
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectedGameId = @json($hasSelectedGame ? $selectedGameId : null);
            const liveUrl = @json(route('admin.games.live')) + (selectedGameId ? '?game_id=' + encodeURIComponent(selectedGameId) : '');

            const money = (value) => {
                const number = Number(value || 0);

                return '₱' + number.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            };

            const odds = (value) => {
                const number = Number(value || 0);
                return 'Odds ' + number.toFixed(2) + 'x';
            };

            const setAll = (key, value) => {
                document.querySelectorAll(`[data-live="${key}"]`).forEach((el) => {
                    el.textContent = value;
                });
            };

            const statusStyle = (status) => {
                switch (status) {
                    case 'open':
                        return { bg: '#dcfce7', color: '#16a34a' };
                    case 'waiting':
                        return { bg: '#fef3c7', color: '#d97706' };
                    case 'closed':
                        return { bg: '#dbeafe', color: '#2563eb' };
                    case 'ended':
                        return { bg: '#fee2e2', color: '#dc2626' };
                    case 'settled':
                        return { bg: '#f3e8ff', color: '#7c3aed' };
                    default:
                        return { bg: '#f1f5f9', color: '#64748b' };
                }
            };

            const pillClass = (result) => {
                switch (result) {
                    case 'meron':
                        return 'bg-red-100 text-red-700';
                    case 'wala':
                        return 'bg-blue-100 text-blue-700';
                    case 'draw':
                        return 'bg-yellow-100 text-yellow-700';
                    default:
                        return 'bg-slate-100 text-slate-600';
                }
            };

            const renderLogrohan = (items) => {
                const box = document.getElementById('adminLiveLogrohan');

                if (!box) {
                    return;
                }

                if (!items || items.length === 0) {
                    box.innerHTML = `
                        <div class="w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm font-black text-slate-500">
                            No results yet.
                        </div>
                    `;

                    return;
                }

                box.innerHTML = items.map((item) => {
                    return `
                        <span class="rounded-full px-3 py-2 text-xs font-black uppercase ${pillClass(item.result_key)}">
                            ${item.round_number} - ${item.result}
                        </span>
                    `;
                }).join('');
            };

            const loadLiveAdminGame = async () => {
                if (!selectedGameId) {
                    return;
                }

                try {
                    const response = await fetch(liveUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        cache: 'no-store',
                    });

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();

                    if (!data.has_game && selectedGameId) {
                        window.location.href = @json(route('admin.games.index'));
                        return;
                    }

                    if (data.has_game && data.game) {
                        setAll('game_title', data.game.title);
                        setAll('round_code', data.game.round_code);

                        setAll('meron_total', money(data.game.meron_total));
                        setAll('wala_total', money(data.game.wala_total));
                        setAll('draw_total', money(data.game.draw_total));
                        setAll('total_pool', money(data.game.total_pool));
                        setAll('net_pool', 'Net ' + money(data.game.net_pool));

                        setAll('meron_odds', odds(data.game.meron_odds));
                        setAll('wala_odds', odds(data.game.wala_odds));
                        setAll('draw_odds', odds(data.game.draw_odds));

                        const status = String(data.game.status || 'no_game').toLowerCase();
                        const statusEl = document.querySelector('[data-live="game_status"]');

                        if (statusEl) {
                            const style = statusStyle(status);

                            statusEl.textContent = status.toUpperCase();
                            statusEl.style.background = style.bg;
                            statusEl.style.color = style.color;
                        }
                    }

                    renderLogrohan(data.logrohan);
                } catch (error) {
                    console.error('Admin live game update failed:', error);
                }
            };

            loadLiveAdminGame();
            setInterval(loadLiveAdminGame, 1000);
        });
    </script>
</x-layouts.app>