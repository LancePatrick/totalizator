<x-layouts.app :title="__('Play Game')">
    @php
        $user = auth()->user();

        $selectedGameId = request('game_id') ?: $currentGame?->id;
        $hasSelectedGame = request()->filled('game_id') && $currentGame;

        $status = $currentGame?->status ?? 'no_game';

        $isOpen = $currentGame && $currentGame->status === 'open';
        $isWaiting = $currentGame && $currentGame->status === 'waiting';
        $isClosed = $currentGame && $currentGame->status === 'closed';
        $isSettled = $currentGame && $currentGame->status === 'settled';

        $meronTotal = (float) ($currentGame->meron_total ?? 0);
        $walaTotal = (float) ($currentGame->wala_total ?? 0);
        $drawTotal = (float) ($currentGame->draw_total ?? 0);
        $totalPool = (float) ($currentGame->total_pool ?? ($meronTotal + $walaTotal + $drawTotal));
        $netPool = (float) ($currentGame->net_pool ?? $totalPool);

        $meronOdds = (float) ($currentGame->meron_odds ?? 0);
        $walaOdds = (float) ($currentGame->wala_odds ?? 0);
        $drawOdds = (float) ($currentGame->draw_odds ?? 0);

        $statusColor = match ($status) {
            'open' => '#16a34a',
            'waiting' => '#d97706',
            'closed' => '#2563eb',
            'settled' => '#7c3aed',
            default => '#64748b',
        };

        $statusBg = match ($status) {
            'open' => '#dcfce7',
            'waiting' => '#fef3c7',
            'closed' => '#dbeafe',
            'settled' => '#f3e8ff',
            default => '#f1f5f9',
        };

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

        $summaryCards = [
            [
                'key' => 'meron',
                'label' => 'Meron',
                'fullLabel' => 'Meron Total',
                'value' => '₱' . number_format($meronTotal, 2),
                'sub' => 'Odds ' . number_format($meronOdds, 2) . 'x',
                'icon' => 'M',
                'theme' => 'red',
                'totalKey' => 'meron_total',
                'oddsKey' => 'meron_odds',
            ],
            [
                'key' => 'wala',
                'label' => 'Wala',
                'fullLabel' => 'Wala Total',
                'value' => '₱' . number_format($walaTotal, 2),
                'sub' => 'Odds ' . number_format($walaOdds, 2) . 'x',
                'icon' => 'W',
                'theme' => 'blue',
                'totalKey' => 'wala_total',
                'oddsKey' => 'wala_odds',
            ],
            [
                'key' => 'draw',
                'label' => 'Draw',
                'fullLabel' => 'Draw Total',
                'value' => '₱' . number_format($drawTotal, 2),
                'sub' => 'Odds ' . number_format($drawOdds, 2) . 'x',
                'icon' => 'D',
                'theme' => 'purple',
                'totalKey' => 'draw_total',
                'oddsKey' => 'draw_odds',
            ],
            [
                'key' => 'pool',
                'label' => 'Pool',
                'fullLabel' => 'Total Pool',
                'value' => '₱' . number_format($totalPool, 2),
                'sub' => 'Net ₱' . number_format($netPool, 2),
                'icon' => '₱',
                'theme' => 'green',
                'totalKey' => 'total_pool',
                'oddsKey' => 'net_pool',
            ],
        ];

        $betCards = [
            [
                'side' => 'meron',
                'title' => 'MERON',
                'letter' => 'M',
                'total' => $meronTotal,
                'odds' => $meronOdds,
                'theme' => 'orange',
                'button' => 'Bet Meron',
                'totalKey' => 'meron_total',
                'oddsKey' => 'meron_odds',
            ],
            [
                'side' => 'wala',
                'title' => 'WALA',
                'letter' => 'W',
                'total' => $walaTotal,
                'odds' => $walaOdds,
                'theme' => 'blue',
                'button' => 'Bet Wala',
                'totalKey' => 'wala_total',
                'oddsKey' => 'wala_odds',
            ],
            [
                'side' => 'draw',
                'title' => 'DRAW',
                'letter' => 'D',
                'total' => $drawTotal,
                'odds' => $drawOdds,
                'theme' => 'purple',
                'button' => 'Bet Draw',
                'totalKey' => 'draw_total',
                'oddsKey' => 'draw_odds',
            ],
        ];
    @endphp

    <div class="game-page">
        <section class="game-hero-card">
            <div class="game-hero-grid"></div>

            <div class="game-hero-content">
                <div>
                    <p class="game-kicker">Player</p>

                    <h1 class="game-title" data-live="game_title">
                        {{ $hasSelectedGame ? ($currentGame->title ?? $currentGame->round_name ?? 'Game Room') : 'Choose Game Room' }}
                    </h1>

                    <p class="game-subtitle">
                        Choose an active game room, watch the live video, and place your bet.
                    </p>
                </div>

                <div class="game-wallet-card">
                    <div class="game-wallet-icon">
                        💼
                    </div>

                    <div>
                        <p class="game-wallet-label">Wallet Balance</p>

                        <h2 class="game-wallet-value" data-live="wallet_balance">
                            ₱{{ number_format($user->wallet_balance ?? 0, 2) }}
                        </h2>
                    </div>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="game-alert game-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="game-alert game-alert-error">
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
                    href="{{ route('player.game.index') }}"
                    class="inline-flex h-11 items-center justify-center rounded-2xl bg-slate-950 px-5 text-xs font-black uppercase tracking-wide text-white transition hover:-translate-y-0.5 hover:bg-slate-800"
                >
                    ← Back to Rooms
                </a>
            </div>
        @endif

        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-950">
                        Game Rooms
                    </h2>

                    <p class="mt-1 text-sm font-bold text-slate-500">
                        Ended rooms are hidden. Declared rooms stay visible.
                    </p>
                </div>

                <span class="w-fit rounded-full bg-blue-50 px-3 py-1 text-xs font-black uppercase text-blue-700">
                    {{ collect($gameRooms ?? [])->count() }} rooms
                </span>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4" id="playerRoomsList">
                @forelse($gameRooms ?? [] as $room)
                    @php
                        $roomStatus = strtolower($room->status ?? 'waiting');

                        $roomStatusClass = match ($roomStatus) {
                            'open' => 'bg-green-100 text-green-700',
                            'waiting' => 'bg-yellow-100 text-yellow-700',
                            'closed' => 'bg-blue-100 text-blue-700',
                            'settled' => 'bg-violet-100 text-violet-700',
                            default => 'bg-slate-100 text-slate-600',
                        };

                        $activeClass = (int) ($selectedGameId ?? 0) === (int) $room->id && $hasSelectedGame
                            ? 'border-blue-500 bg-blue-50 ring-4 ring-blue-100'
                            : 'border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50 hover:shadow-lg hover:shadow-blue-100/60';
                    @endphp

                    <a
                        href="{{ route('player.game.index', ['game_id' => $room->id]) }}"
                        class="group rounded-3xl border p-4 transition duration-200 hover:-translate-y-1 {{ $activeClass }}"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-base font-black text-slate-950 transition group-hover:text-blue-700">
                                    {{ $room->title ?? $room->round_name ?? 'Game Room' }}
                                </h3>

                                <p class="mt-1 text-xs font-bold text-slate-500">
                                    Room: {{ $room->round_code ?? $room->round_number ?? $room->id }}
                                </p>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase text-slate-600">
                                        Pool ₱{{ number_format((float) ($room->total_pool ?? 0), 2) }}
                                    </span>

                                    <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase {{ $roomStatusClass }}">
                                        {{ $roomStatus }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-md shadow-blue-600/20 transition group-hover:scale-110">
                                →
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm font-black text-slate-500 sm:col-span-2 xl:col-span-4">
                        No game rooms.
                    </div>
                @endforelse
            </div>
        </section>

        @if(!$hasSelectedGame)
            <section class="game-card">
                <div class="game-empty">
                    Select a game room above to enter the betting area.
                </div>
            </section>
        @else
            <section class="game-shell">
                <main class="game-left">
                    <section class="game-live-section">
                        <div class="game-video-card">
                            <div class="game-video-head">
                                <div>
                                    <h2 class="game-card-title" data-live="game_title">
                                        {{ $currentGame->title ?? $currentGame->round_name ?? 'Current Game' }}
                                    </h2>

                                    <p class="game-card-sub">
                                        Room:
                                        <span data-live="round_code">
                                            {{ $currentGame->round_code ?? $currentGame->round_number ?? $currentGame->id }}
                                        </span>
                                    </p>
                                </div>

                                <span
                                    class="game-status-badge"
                                    data-live="game_status"
                                    style="background:{{ $statusBg }}; color:{{ $statusColor }};"
                                >
                                    {{ strtoupper($currentGame->status) }}
                                </span>
                            </div>

                            <div class="game-video-frame">
                                @if($youtubeEmbedUrl)
                                    <iframe
                                        class="game-video"
                                        src="{{ $youtubeEmbedUrl }}"
                                        title="Game Video"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen
                                    ></iframe>
                                @elseif($videoUrl)
                                    <video class="game-video" controls muted playsinline>
                                        <source src="{{ $videoUrl }}">
                                    </video>
                                @else
                                    <div class="game-video game-video-placeholder">
                                        <div>
                                            <div class="game-video-icon">🎥</div>
                                            <div>No video uploaded for this room</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="game-market-grid">
                            @foreach($summaryCards as $card)
                                <div class="game-market-card game-market-{{ $card['theme'] }}">
                                    <div class="game-market-top">
                                        <div class="game-market-icon">
                                            {{ $card['icon'] }}
                                        </div>

                                        <span class="game-live-pill">Live</span>
                                    </div>

                                    <div>
                                        <p class="game-market-label">
                                            <span class="game-label-desktop">{{ $card['fullLabel'] }}</span>
                                            <span class="game-label-mobile">{{ $card['label'] }}</span>
                                        </p>

                                        <h3 class="game-market-value" data-live="{{ $card['totalKey'] }}">
                                            {{ $card['value'] }}
                                        </h3>

                                        <p class="game-market-sub" data-live="{{ $card['oddsKey'] }}">
                                            {{ $card['sub'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="game-card">
                        <div>
                            <h2 class="game-card-title">Place Your Bet</h2>

                            <p class="game-card-sub" id="placeBetSubtitle">
                                @if($isOpen)
                                    Betting is open. Choose your side and enter your amount.
                                @elseif($isWaiting)
                                    Game room is waiting. Admin has not opened betting yet.
                                @elseif($isClosed)
                                    Betting is already closed for this room.
                                @elseif($isSettled)
                                    This room is already declared. Please choose another open room.
                                @endif
                            </p>
                        </div>

                        <div
                            class="game-warning"
                            id="gameClosedWarning"
                            style="{{ $isOpen ? 'display:none;' : '' }}"
                        >
                            ⚠ You cannot bet because this room status is
                            <strong data-live="game_status_text">{{ strtoupper($currentGame->status) }}</strong>.
                            Choose an <strong>OPEN</strong> room to bet.
                        </div>

                        <div class="game-bet-grid">
                            @foreach($betCards as $card)
                                <div class="game-bet-card game-bet-{{ $card['theme'] }}">
                                    <div class="game-bet-head">
                                        <div class="game-bet-icon">
                                            {{ $card['letter'] }}
                                        </div>

                                        <h3 class="game-bet-title">
                                            {{ $card['title'] }}
                                        </h3>
                                    </div>

                                    <div class="game-bet-info">
                                        <div class="game-bet-row">
                                            <span>Total</span>
                                            <strong data-live="{{ $card['totalKey'] }}">
                                                ₱{{ number_format($card['total'], 2) }}
                                            </strong>
                                        </div>

                                        <div class="game-bet-row">
                                            <span>Odds</span>
                                            <strong data-live="{{ $card['oddsKey'] }}">
                                                Odds {{ number_format($card['odds'], 2) }}x
                                            </strong>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('player.game.bet') }}" class="game-bet-form">
                                        @csrf

                                        <input
                                            type="hidden"
                                            name="game_round_id"
                                            value="{{ $currentGame->id }}"
                                            data-live-input="game_round_id"
                                        >

                                        <input type="hidden" name="side" value="{{ $card['side'] }}">

                                        <input
                                            type="number"
                                            name="amount"
                                            min="1"
                                            step="1"
                                            placeholder="Enter amount"
                                            class="game-input js-bet-field"
                                            {{ !$isOpen ? 'disabled' : '' }}
                                        >

                                        <button
                                            type="submit"
                                            class="game-btn js-bet-field"
                                            {{ !$isOpen ? 'disabled' : '' }}
                                        >
                                            {{ $card['button'] }}
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </main>

                <aside class="game-right">
                    <section class="game-card">
                        <h2 class="game-card-title">My Latest Bets</h2>
                        <p class="game-card-sub">Your recent bet records for this room.</p>

                        <div class="game-bet-history" id="liveLatestBets">
                            @forelse($myBets as $bet)
                                @php
                                    $betSide = strtolower($bet->side);

                                    $betColor = match ($betSide) {
                                        'meron' => '#ef4444',
                                        'wala' => '#2563eb',
                                        'draw' => '#7c3aed',
                                        default => '#64748b',
                                    };

                                    $betLetter = match ($betSide) {
                                        'meron' => 'M',
                                        'wala' => 'W',
                                        'draw' => 'D',
                                        default => '?',
                                    };

                                    $betStatus = strtolower($bet->status ?? 'pending');
                                @endphp

                                <div class="game-history-item">
                                    <div class="game-history-left">
                                        <div class="game-history-icon" style="background:{{ $betColor }};">
                                            {{ $betLetter }}
                                        </div>

                                        <div class="game-history-text">
                                            <p class="game-history-title">
                                                {{ strtoupper($bet->side) }} — ₱{{ number_format($bet->amount, 2) }}
                                            </p>

                                            <p class="game-history-sub">
                                                Room: {{ $bet->round?->round_code ?? $bet->game_round_id }}
                                                • Odds {{ number_format($bet->odds_at_bet, 2) }}x
                                            </p>
                                        </div>
                                    </div>

                                    <span class="game-status-pill {{ $betStatus }}">
                                        {{ strtoupper($bet->status) }}
                                    </span>
                                </div>
                            @empty
                                <div class="game-empty">
                                    No bets yet.
                                </div>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </section>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectedRoomId = @json($hasSelectedGame ? $selectedGameId : null);
            const liveUrl = @json(route('player.game.live')) + (selectedRoomId ? '?game_id=' + encodeURIComponent(selectedRoomId) : '');

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

            const setGameRoundInputs = (gameId) => {
                document.querySelectorAll('[data-live-input="game_round_id"]').forEach((input) => {
                    input.value = gameId;
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
                    case 'settled':
                        return { bg: '#f3e8ff', color: '#7c3aed' };
                    default:
                        return { bg: '#f1f5f9', color: '#64748b' };
                }
            };

            const updateStatusUi = (status) => {
                const statusKey = String(status || '').toLowerCase();
                const isOpen = statusKey === 'open';

                const statusEl = document.querySelector('[data-live="game_status"]');
                const style = statusStyle(statusKey);

                if (statusEl) {
                    statusEl.textContent = statusKey.toUpperCase();
                    statusEl.style.background = style.bg;
                    statusEl.style.color = style.color;
                }

                setAll('game_status_text', statusKey.toUpperCase());

                const warning = document.getElementById('gameClosedWarning');

                if (warning) {
                    warning.style.display = isOpen ? 'none' : 'block';
                }

                document.querySelectorAll('.js-bet-field').forEach((el) => {
                    el.disabled = !isOpen;
                });

                const placeBetSubtitle = document.getElementById('placeBetSubtitle');

                if (placeBetSubtitle) {
                    if (isOpen) {
                        placeBetSubtitle.textContent = 'Betting is open. Choose your side and enter your amount.';
                    } else if (statusKey === 'waiting') {
                        placeBetSubtitle.textContent = 'Game room is waiting. Admin has not opened betting yet.';
                    } else if (statusKey === 'closed') {
                        placeBetSubtitle.textContent = 'Betting is already closed for this room.';
                    } else if (statusKey === 'settled') {
                        placeBetSubtitle.textContent = 'This room is already declared. Please choose another open room.';
                    } else {
                        placeBetSubtitle.textContent = 'Please choose an open game room.';
                    }
                }
            };

            const betColor = (side) => {
                switch (side) {
                    case 'meron':
                        return '#ef4444';
                    case 'wala':
                        return '#2563eb';
                    case 'draw':
                        return '#7c3aed';
                    default:
                        return '#64748b';
                }
            };

            const betLetter = (side) => {
                switch (side) {
                    case 'meron':
                        return 'M';
                    case 'wala':
                        return 'W';
                    case 'draw':
                        return 'D';
                    default:
                        return '?';
                }
            };

            const renderLatestBets = (bets) => {
                const box = document.getElementById('liveLatestBets');

                if (!box) {
                    return;
                }

                if (!bets || bets.length === 0) {
                    box.innerHTML = `
                        <div class="game-empty">
                            No bets yet.
                        </div>
                    `;

                    return;
                }

                box.innerHTML = bets.map((bet) => {
                    const sideKey = bet.side_key || '';
                    const statusKey = bet.status_key || 'pending';

                    return `
                        <div class="game-history-item">
                            <div class="game-history-left">
                                <div class="game-history-icon" style="background:${betColor(sideKey)};">
                                    ${betLetter(sideKey)}
                                </div>

                                <div class="game-history-text">
                                    <p class="game-history-title">
                                        ${bet.side} — ${money(bet.amount)}
                                    </p>

                                    <p class="game-history-sub">
                                        Room: ${bet.round} • Odds ${Number(bet.odds || 0).toFixed(2)}x
                                    </p>
                                </div>
                            </div>

                            <span class="game-status-pill ${statusKey}">
                                ${bet.status}
                            </span>
                        </div>
                    `;
                }).join('');
            };

            const loadLiveGame = async () => {
                if (!selectedRoomId) {
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

                    setAll('wallet_balance', money(data.wallet_balance));

                    if (!data.has_game && selectedRoomId) {
                        window.location.href = @json(route('player.game.index'));
                        return;
                    }

                    if (data.has_game && data.game) {
                        setGameRoundInputs(data.game.id);
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

                        updateStatusUi(data.game.status);
                    }

                    renderLatestBets(data.my_bets);
                } catch (error) {
                    console.error('Live game update failed:', error);
                }
            };

            loadLiveGame();
            setInterval(loadLiveGame, 1000);
        });
    </script>
</x-layouts.app>