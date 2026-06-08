<x-layouts.app :title="__('Game')">
    @php
        $user = auth()->user();

        $status = $currentGame?->status ?? 'no_game';

        $isOpen = $currentGame && $currentGame->status === 'open';
        $isWaiting = $currentGame && $currentGame->status === 'waiting';
        $isClosed = $currentGame && $currentGame->status === 'closed';
        $isEnded = $currentGame && in_array($currentGame->status, ['ended', 'settled']);

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

        $roadEntries = collect($logrohan ?? [])
            ->take(240)
            ->reverse()
            ->values();

        $normalizeSide = function ($entry) {
            $side = strtolower($entry->winning_side ?? $entry->result ?? $entry->side ?? 'cancelled');

            if ($side === 'canceled' || $side === 'cancel') {
                $side = 'cancelled';
            }

            if (!in_array($side, ['meron', 'wala', 'draw', 'cancelled'])) {
                $side = 'cancelled';
            }

            return $side;
        };

        $sideLabel = function ($side) {
            return match ($side) {
                'meron' => 'M',
                'wala' => 'W',
                'draw' => 'D',
                default => 'C',
            };
        };

        $meronCount = $roadEntries->filter(fn ($entry) => $normalizeSide($entry) === 'meron')->count();
        $walaCount = $roadEntries->filter(fn ($entry) => $normalizeSide($entry) === 'wala')->count();
        $drawCount = $roadEntries->filter(fn ($entry) => $normalizeSide($entry) === 'draw')->count();
        $cancelledCount = $roadEntries->filter(fn ($entry) => $normalizeSide($entry) === 'cancelled')->count();

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

        $bigRoadPositions = [];
        $occupied = [];
        $lastSide = null;
        $currentCol = -1;
        $currentRow = 0;

        foreach ($roadEntries as $index => $entry) {
            $side = $normalizeSide($entry);

            if ($side !== $lastSide) {
                $currentCol++;
                $currentRow = 0;
                $lastSide = $side;

                while (isset($occupied[$currentCol . '-0'])) {
                    $currentCol++;
                }
            } else {
                $nextRow = $currentRow + 1;

                if ($nextRow <= 5 && !isset($occupied[$currentCol . '-' . $nextRow])) {
                    $currentRow = $nextRow;
                } else {
                    $currentCol++;

                    while (isset($occupied[$currentCol . '-' . $currentRow])) {
                        $currentCol++;
                    }
                }
            }

            $occupied[$currentCol . '-' . $currentRow] = true;

            $bigRoadPositions[] = [
                'entry' => $entry,
                'side' => $side,
                'label' => $sideLabel($side),
                'row' => $currentRow,
                'col' => $currentCol,
                'index' => $index,
            ];
        }
    @endphp

    <div class="game-page">
        <section class="game-hero-card">
            <div class="game-hero-grid"></div>

            <div class="game-hero-content">
                <div>
                    <p class="game-kicker">Player</p>

                    <h1 class="game-title">
                        Horse Racing
                    </h1>

                    <p class="game-subtitle">
                        Bet on Meron, Wala, or Draw. Odds update based on the total pool.
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

        @if(!$currentGame)
            <div class="game-card">
                <div class="game-empty">
                    No game available yet. Please wait for the admin to create and start a game.
                </div>
            </div>
        @else
            <section class="game-shell">
                <main class="game-left">
                    <section class="game-live-section">
                        <div class="game-video-card">
                            <div class="game-video-head">
                                <div>
                                    <h2 class="game-card-title">
                                        {{ $currentGame->title ?? 'Current Game' }}
                                    </h2>

                                    <p class="game-card-sub">
                                        Round:
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
                                @if(!empty($currentGame->video_url))
                                    <video class="game-video" controls muted playsinline>
                                        <source src="{{ $currentGame->video_url }}">
                                    </video>
                                @else
                                    <div class="game-video game-video-placeholder">
                                        <div>
                                            <div class="game-video-icon">🎥</div>
                                            <div>No video uploaded for this round</div>
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

                    <section class="game-total-row">
                        @foreach($summaryCards as $card)
                            <div class="game-total-card game-total-{{ $card['theme'] }}">
                                <div class="game-total-icon">
                                    {{ $card['icon'] }}
                                </div>

                                <div>
                                    <p class="game-total-label">{{ $card['fullLabel'] }}</p>

                                    <h3 class="game-total-value" data-live="{{ $card['totalKey'] }}">
                                        {{ $card['value'] }}
                                    </h3>

                                    <p class="game-total-sub" data-live="{{ $card['oddsKey'] }}">
                                        {{ $card['sub'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </section>

                    <section class="game-card">
                        <div>
                            <h2 class="game-card-title">Place Your Bet</h2>

                            <p class="game-card-sub" id="placeBetSubtitle">
                                @if($isOpen)
                                    Betting is open. Choose your side and enter your amount.
                                @elseif($isWaiting)
                                    Game is waiting. Admin has not opened betting yet.
                                @elseif($isClosed)
                                    Betting is already closed for this round.
                                @elseif($isEnded)
                                    This round already ended. Please wait for a new game.
                                @endif
                            </p>
                        </div>

                        <div
                            class="game-warning"
                            id="gameClosedWarning"
                            style="{{ $isOpen ? 'display:none;' : '' }}"
                        >
                            ⚠ You cannot bet because this game status is
                            <strong data-live="game_status_text">{{ strtoupper($currentGame->status) }}</strong>.
                            Admin must create/start a new game with status <strong>OPEN</strong>.
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
                        <p class="game-card-sub">Your recent bet records.</p>

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
                                                Round: {{ $bet->round?->round_code ?? $bet->game_round_id }}
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

                    <section class="game-score-strip">
                        <div class="game-score-box game-score-meron">
                            <p>Meron</p>
                            <h3 data-live="meron_count">{{ $meronCount }}</h3>
                        </div>

                        <div class="game-score-box game-score-wala">
                            <p>Wala</p>
                            <h3 data-live="wala_count">{{ $walaCount }}</h3>
                        </div>

                        <div class="game-score-box game-score-draw">
                            <p>Draw</p>
                            <h3 data-live="draw_count">{{ $drawCount }}</h3>
                        </div>

                        <div class="game-score-box game-score-cancel">
                            <p>Cancel</p>
                            <h3 data-live="cancelled_count">{{ $cancelledCount }}</h3>
                        </div>
                    </section>

                    <section class="game-card">
                        <div class="game-road-head">
                            <div>
                                <p class="game-road-kicker">Road</p>
                                <h2 class="game-road-title">Big Road</h2>
                            </div>

                            <p class="game-road-note">Pattern by streak</p>
                        </div>

                        <div class="road-board">
                            <div class="big-road-inner">
                                @for($i = 0; $i < 240; $i++)
                                    @php
                                        $row = $i % 6;
                                        $col = floor($i / 6);
                                        $left = 8 + ($col * 34);
                                        $top = 8 + ($row * 34);
                                    @endphp

                                    <div class="road-empty-cell" style="left:{{ $left }}px; top:{{ $top }}px;"></div>
                                @endfor

                                @forelse($bigRoadPositions as $item)
                                    @php
                                        $left = 8 + ($item['col'] * 34);
                                        $top = 8 + ($item['row'] * 34);
                                        $entry = $item['entry'];
                                    @endphp

                                    <div
                                        class="big-road-dot {{ $item['side'] }}"
                                        style="left:{{ $left }}px; top:{{ $top }}px;"
                                        title="{{ strtoupper($item['side']) }} - {{ $entry->round_code ?? $entry->id }}"
                                    >
                                        {{ $item['label'] }}
                                    </div>
                                @empty
                                    <div class="game-road-empty">
                                        No result history yet.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    <section class="game-card">
                        <div class="game-road-head">
                            <div>
                                <p class="game-road-kicker">Road</p>
                                <h2 class="game-road-title">Bead Road</h2>
                            </div>

                            <p class="game-road-note">Chronological</p>
                        </div>

                        <div class="road-board">
                            <div class="bead-road-inner">
                                @for($i = 0; $i < 240; $i++)
                                    @php
                                        $row = $i % 6;
                                        $col = floor($i / 6);
                                        $left = 8 + ($col * 34);
                                        $top = 8 + ($row * 34);
                                    @endphp

                                    <div class="road-empty-cell" style="left:{{ $left }}px; top:{{ $top }}px;"></div>
                                @endfor

                                @foreach($roadEntries as $index => $entry)
                                    @php
                                        $side = $normalizeSide($entry);
                                        $row = $index % 6;
                                        $col = floor($index / 6);
                                        $left = 8 + ($col * 34);
                                        $top = 8 + ($row * 34);
                                        $number = $index + 1;
                                    @endphp

                                    <div
                                        class="bead-road-dot {{ $side }}"
                                        style="left:{{ $left }}px; top:{{ $top }}px;"
                                        title="{{ strtoupper($side) }} - {{ $entry->round_code ?? $entry->id }}"
                                    >
                                        {{ $number }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </aside>
            </section>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const liveUrl = @json(route('player.game.live'));

            let latestGameId = @json($currentGame?->id);

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
                    case 'ended':
                        return { bg: '#fee2e2', color: '#dc2626' };
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
                        placeBetSubtitle.textContent = 'Game is waiting. Admin has not opened betting yet.';
                    } else if (statusKey === 'closed') {
                        placeBetSubtitle.textContent = 'Betting is already closed for this round.';
                    } else if (statusKey === 'ended' || statusKey === 'settled') {
                        placeBetSubtitle.textContent = 'This round already ended. Please wait for a new game.';
                    } else {
                        placeBetSubtitle.textContent = 'Please wait for the admin to create or start a game.';
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
                                        Round: ${bet.round} • Odds ${Number(bet.odds || 0).toFixed(2)}x
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

                    if (data.has_game && data.game) {
                        if (latestGameId && Number(latestGameId) !== Number(data.game.id)) {
                            window.location.reload();
                            return;
                        }

                        latestGameId = data.game.id;

                        setGameRoundInputs(data.game.id);
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

                    if (data.road_counts) {
                        setAll('meron_count', data.road_counts.meron);
                        setAll('wala_count', data.road_counts.wala);
                        setAll('draw_count', data.road_counts.draw);
                        setAll('cancelled_count', data.road_counts.cancelled);
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