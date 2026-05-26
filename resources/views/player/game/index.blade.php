<x-layouts.app :title="__('Game')">
    @php
        $user = auth()->user();

        $status = $currentGame?->status ?? 'no_game';

        $isOpen = $currentGame && $currentGame->status === 'open';
        $isWaiting = $currentGame && $currentGame->status === 'waiting';
        $isClosed = $currentGame && $currentGame->status === 'closed';
        $isEnded = $currentGame && $currentGame->status === 'ended';

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
            default => '#64748b',
        };

        $statusBg = match ($status) {
            'open' => '#dcfce7',
            'waiting' => '#fef3c7',
            'closed' => '#dbeafe',
            'ended' => '#fee2e2',
            default => '#f1f5f9',
        };

        $roadEntries = collect($logrohan ?? [])->take(240)->values();

        $meronCount = $roadEntries->filter(function ($entry) {
            return strtolower($entry->winning_side ?? $entry->result ?? '') === 'meron';
        })->count();

        $walaCount = $roadEntries->filter(function ($entry) {
            return strtolower($entry->winning_side ?? $entry->result ?? '') === 'wala';
        })->count();

        $drawCount = $roadEntries->filter(function ($entry) {
            return strtolower($entry->winning_side ?? $entry->result ?? '') === 'draw';
        })->count();

        $cancelledCount = $roadEntries->filter(function ($entry) {
            $side = strtolower($entry->winning_side ?? $entry->result ?? '');
            return in_array($side, ['cancelled', 'canceled', 'cancel']);
        })->count();
    @endphp

    <style>
        .tg-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .tg-hero {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            padding: 26px;
            color: white;
            background:
                radial-gradient(circle at 82% 45%, rgba(29,124,255,.65), transparent 26%),
                radial-gradient(circle at 18% 20%, rgba(250,204,21,.16), transparent 22%),
                linear-gradient(135deg, #03142f 0%, #041a4d 52%, #0848b9 100%);
            box-shadow: 0 18px 42px rgba(2,18,54,.18);
        }

        .tg-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 54px 54px;
            opacity: .45;
        }

        .tg-hero-inner {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            gap: 24px;
            align-items: center;
        }

        .tg-kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .tg-title {
            margin: 8px 0 0;
            font-size: 32px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .tg-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.74);
            font-size: 14px;
            font-weight: 700;
        }

        .tg-wallet-box {
            min-width: 240px;
            border-radius: 18px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            padding: 18px;
            backdrop-filter: blur(10px);
        }

        .tg-wallet-label {
            margin: 0;
            color: rgba(255,255,255,.65);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .tg-wallet-value {
            margin: 8px 0 0;
            color: #facc15;
            font-size: 30px;
            font-weight: 950;
        }

        .tg-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 800;
        }

        .tg-alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .tg-alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .tg-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 380px;
            gap: 18px;
            align-items: start;
        }

        .tg-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .tg-video-card {
            overflow: hidden;
            padding: 0;
        }

        .tg-video-head {
            padding: 18px;
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 14px;
            border-bottom: 1px solid #e7edf6;
        }

        .tg-game-name {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .tg-round {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
        }

        .tg-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
        }

        .tg-video-wrap {
            background: #111827;
            padding: 18px;
        }

        .tg-video {
            width: 100%;
            height: 360px;
            background: #000;
            border-radius: 14px;
            display: block;
        }

        .tg-no-video {
            height: 360px;
            border-radius: 14px;
            background:
                radial-gradient(circle at 50% 50%, rgba(29,124,255,.18), transparent 26%),
                linear-gradient(135deg, #020617, #111827);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-weight: 900;
        }

        .tg-section-title {
            margin: 0;
            color: #0f172a;
            font-size: 20px;
            font-weight: 950;
        }

        .tg-section-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .tg-total-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .tg-total-box {
            border-radius: 18px;
            padding: 16px;
            border: 1px solid #e7edf6;
            background: #f8fbff;
        }

        .tg-total-label {
            margin: 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .tg-total-value {
            margin: 8px 0 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .tg-total-odds {
            margin: 5px 0 0;
            font-size: 13px;
            font-weight: 900;
        }

        .tg-bet-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .tg-bet-card {
            border-radius: 20px;
            padding: 18px;
            border: 1px solid #e7edf6;
            background: white;
            box-shadow: 0 8px 20px rgba(15,23,42,.035);
        }

        .tg-bet-card.meron {
            background: linear-gradient(180deg, #fff7ed, #ffffff);
            border-color: #fed7aa;
        }

        .tg-bet-card.wala {
            background: linear-gradient(180deg, #eff6ff, #ffffff);
            border-color: #bfdbfe;
        }

        .tg-bet-card.draw {
            background: linear-gradient(180deg, #f5f3ff, #ffffff);
            border-color: #ddd6fe;
        }

        .tg-side {
            margin: 0;
            font-size: 20px;
            font-weight: 950;
        }

        .tg-side.meron {
            color: #ea580c;
        }

        .tg-side.wala {
            color: #2563eb;
        }

        .tg-side.draw {
            color: #7c3aed;
        }

        .tg-side-info {
            margin-top: 12px;
            display: grid;
            gap: 8px;
        }

        .tg-side-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
        }

        .tg-side-row strong {
            color: #0f172a;
        }

        .tg-form {
            margin-top: 16px;
            display: grid;
            gap: 10px;
        }

        .tg-input {
            width: 100%;
            height: 44px;
            border-radius: 12px;
            border: 1px solid #dce6f2;
            background: white;
            padding: 0 12px;
            outline: none;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
        }

        .tg-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.12);
        }

        .tg-button {
            height: 44px;
            border: 0;
            border-radius: 12px;
            color: white;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            transition: .16s ease;
        }

        .tg-button:hover {
            transform: translateY(-1px);
            filter: brightness(.96);
        }

        .tg-button:disabled {
            cursor: not-allowed;
            opacity: .45;
            transform: none;
        }

        .tg-btn-meron {
            background: #ea580c;
        }

        .tg-btn-wala {
            background: #2563eb;
        }

        .tg-btn-draw {
            background: #7c3aed;
        }

        .tg-closed-box {
            border-radius: 16px;
            padding: 16px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 14px;
            font-weight: 900;
        }

        .tg-history-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .tg-history-item {
            border: 1px solid #e7edf6;
            border-radius: 14px;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }

        .tg-history-title {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
        }

        .tg-history-sub {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .tg-history-pill {
            border-radius: 999px;
            padding: 6px 10px;
            background: #f1f5f9;
            color: #334155;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
        }

        .tg-empty {
            border-radius: 18px;
            padding: 24px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            text-align: center;
            color: #64748b;
            font-weight: 800;
        }

        .tg-road-section {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .tg-score-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            overflow: hidden;
            border-radius: 16px;
            border: 1px solid #dce6f2;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .tg-score-box {
            min-height: 76px;
            color: white;
            text-align: center;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-right: 1px solid rgba(255,255,255,.16);
        }

        .tg-score-box:last-child {
            border-right: 0;
        }

        .tg-score-label {
            margin: 0;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .22em;
            color: rgba(255,255,255,.88);
        }

        .tg-score-value {
            margin: 4px 0 0;
            font-size: 34px;
            line-height: 1;
            font-weight: 950;
            color: white;
        }

        .tg-road-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 12px;
        }

        .tg-road-kicker {
            margin: 0;
            color: #475569;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .22em;
        }

        .tg-road-title {
            margin: 2px 0 0;
            color: #0f172a;
            font-size: 17px;
            font-weight: 950;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .tg-road-subtitle {
            margin: 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .12em;
        }

        .tg-logrohan-board,
        .tg-bead-board {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border: 1px solid #dce6f2;
            border-radius: 16px;
            background:
                linear-gradient(#d9e0ea 1px, transparent 1px),
                linear-gradient(90deg, #d9e0ea 1px, transparent 1px),
                #ffffff;
            background-size: 38px 38px;
        }

        .tg-logrohan-inner,
        .tg-bead-inner {
            min-width: 900px;
            height: 238px;
            position: relative;
            padding: 8px;
        }

        .tg-road-dot {
            position: absolute;
            width: 27px;
            height: 27px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 950;
            background: white;
        }

        .tg-road-dot.meron {
            border: 3px solid #ef4444;
            color: #ef4444;
        }

        .tg-road-dot.wala {
            border: 3px solid #3b82f6;
            color: #3b82f6;
        }

        .tg-road-dot.draw {
            border: 3px solid #22c55e;
            color: #22c55e;
        }

        .tg-road-dot.cancelled {
            border: 3px solid #9ca3af;
            color: #4b5563;
        }

        .tg-bead-dot {
            position: absolute;
            width: 27px;
            height: 27px;
            border-radius: 999px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 950;
            box-shadow: inset 0 -3px 8px rgba(0,0,0,.18), 0 4px 10px rgba(15,23,42,.16);
        }

        .tg-bead-dot.meron {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .tg-bead-dot.wala {
            background: linear-gradient(135deg, #60a5fa, #2563eb);
        }

        .tg-bead-dot.draw {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .tg-bead-dot.cancelled {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
        }

        .tg-road-empty-cell {
            position: absolute;
            width: 27px;
            height: 27px;
            border-radius: 999px;
            border: 1px dashed #cbd5e1;
            background: rgba(255,255,255,.45);
        }

        @media (max-width: 1300px) {
            .tg-main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 980px) {
            .tg-hero-inner,
            .tg-total-grid,
            .tg-bet-grid,
            .tg-score-strip {
                grid-template-columns: 1fr;
            }

            .tg-hero-inner {
                flex-direction: column;
                align-items: stretch;
            }

            .tg-video {
                height: 260px;
            }
        }
    </style>

    <div class="tg-page">
        <section class="tg-hero">
            <div class="tg-hero-inner">
                <div>
                    <p class="tg-kicker">Player</p>
                    <h1 class="tg-title">
                        Horse Racing
                    </h1>
                    <p class="tg-subtitle">
                        Bet on Meron, Wala, or Draw. Odds update based on the total pool.
                    </p>
                </div>

                <div class="tg-wallet-box">
                    <p class="tg-wallet-label">Wallet Balance</p>
                    <h2 class="tg-wallet-value">
                        ₱{{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}
                    </h2>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="tg-alert tg-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="tg-alert tg-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(!$currentGame)
            <div class="tg-card">
                <div class="tg-empty">
                    No game available yet. Please wait for the admin to create and start a game.
                </div>
            </div>
        @else
            <section class="tg-main-grid">
                <div style="display:flex; flex-direction:column; gap:18px;">
                    <div class="tg-card tg-video-card">
                        <div class="tg-video-head">
                            <div>
                                <h2 class="tg-game-name">
                                    {{ $currentGame->title ?? 'Current Game' }}
                                </h2>
                                <p class="tg-round">
                                    Round: {{ $currentGame->round_code ?? $currentGame->id }}
                                </p>
                            </div>

                            <span
                                class="tg-status"
                                style="background:{{ $statusBg }}; color:{{ $statusColor }};"
                            >
                                {{ $currentGame->status }}
                            </span>
                        </div>

                        <div class="tg-video-wrap">
                            @if(!empty($currentGame->video_url))
                                <video class="tg-video" controls muted playsinline>
                                    <source src="{{ $currentGame->video_url }}">
                                </video>
                            @else
                                <div class="tg-no-video">
                                    <div>
                                        <div style="font-size:42px;">🎥</div>
                                        <div style="margin-top:10px;">No video uploaded for this round</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="tg-total-grid">
                        <div class="tg-total-box">
                            <p class="tg-total-label">Meron Total</p>
                            <h3 class="tg-total-value">₱{{ number_format($meronTotal, 2) }}</h3>
                            <p class="tg-total-odds" style="color:#ea580c;">Odds {{ number_format($meronOdds, 2) }}x</p>
                        </div>

                        <div class="tg-total-box">
                            <p class="tg-total-label">Wala Total</p>
                            <h3 class="tg-total-value">₱{{ number_format($walaTotal, 2) }}</h3>
                            <p class="tg-total-odds" style="color:#2563eb;">Odds {{ number_format($walaOdds, 2) }}x</p>
                        </div>

                        <div class="tg-total-box">
                            <p class="tg-total-label">Draw Total</p>
                            <h3 class="tg-total-value">₱{{ number_format($drawTotal, 2) }}</h3>
                            <p class="tg-total-odds" style="color:#7c3aed;">Odds {{ number_format($drawOdds, 2) }}x</p>
                        </div>

                        <div class="tg-total-box">
                            <p class="tg-total-label">Total Pool</p>
                            <h3 class="tg-total-value">₱{{ number_format($totalPool, 2) }}</h3>
                            <p class="tg-total-odds" style="color:#16a34a;">Net ₱{{ number_format($netPool, 2) }}</p>
                        </div>
                    </div>

                    <div class="tg-card">
                        <div style="margin-bottom:16px;">
                            <h2 class="tg-section-title">Place Your Bet</h2>
                            <p class="tg-section-sub">
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

                        @if(!$isOpen)
                            <div class="tg-closed-box">
                                You cannot bet because this game status is
                                <strong>{{ strtoupper($currentGame->status) }}</strong>.
                                Admin must create/start a new game with status <strong>OPEN</strong>.
                            </div>
                        @endif

                        <div class="tg-bet-grid" style="margin-top:16px;">
                            <div class="tg-bet-card meron">
                                <h3 class="tg-side meron">MERON</h3>

                                <div class="tg-side-info">
                                    <div class="tg-side-row">
                                        <span>Total</span>
                                        <strong>₱{{ number_format($meronTotal, 2) }}</strong>
                                    </div>
                                    <div class="tg-side-row">
                                        <span>Odds</span>
                                        <strong>{{ number_format($meronOdds, 2) }}x</strong>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('player.game.bet') }}" class="tg-form">
                                    @csrf
                                    <input type="hidden" name="game_round_id" value="{{ $currentGame->id }}">
                                    <input type="hidden" name="side" value="meron">

                                    <input
                                        type="number"
                                        name="amount"
                                        class="tg-input"
                                        min="1"
                                        step="1"
                                        placeholder="Enter amount"
                                        {{ !$isOpen ? 'disabled' : '' }}
                                    >

                                    <button
                                        type="submit"
                                        class="tg-button tg-btn-meron"
                                        {{ !$isOpen ? 'disabled' : '' }}
                                    >
                                        Bet Meron
                                    </button>
                                </form>
                            </div>

                            <div class="tg-bet-card wala">
                                <h3 class="tg-side wala">WALA</h3>

                                <div class="tg-side-info">
                                    <div class="tg-side-row">
                                        <span>Total</span>
                                        <strong>₱{{ number_format($walaTotal, 2) }}</strong>
                                    </div>
                                    <div class="tg-side-row">
                                        <span>Odds</span>
                                        <strong>{{ number_format($walaOdds, 2) }}x</strong>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('player.game.bet') }}" class="tg-form">
                                    @csrf
                                    <input type="hidden" name="game_round_id" value="{{ $currentGame->id }}">
                                    <input type="hidden" name="side" value="wala">

                                    <input
                                        type="number"
                                        name="amount"
                                        class="tg-input"
                                        min="1"
                                        step="1"
                                        placeholder="Enter amount"
                                        {{ !$isOpen ? 'disabled' : '' }}
                                    >

                                    <button
                                        type="submit"
                                        class="tg-button tg-btn-wala"
                                        {{ !$isOpen ? 'disabled' : '' }}
                                    >
                                        Bet Wala
                                    </button>
                                </form>
                            </div>

                            <div class="tg-bet-card draw">
                                <h3 class="tg-side draw">DRAW</h3>

                                <div class="tg-side-info">
                                    <div class="tg-side-row">
                                        <span>Total</span>
                                        <strong>₱{{ number_format($drawTotal, 2) }}</strong>
                                    </div>
                                    <div class="tg-side-row">
                                        <span>Odds</span>
                                        <strong>{{ number_format($drawOdds, 2) }}x</strong>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('player.game.bet') }}" class="tg-form">
                                    @csrf
                                    <input type="hidden" name="game_round_id" value="{{ $currentGame->id }}">
                                    <input type="hidden" name="side" value="draw">

                                    <input
                                        type="number"
                                        name="amount"
                                        class="tg-input"
                                        min="1"
                                        step="1"
                                        placeholder="Enter amount"
                                        {{ !$isOpen ? 'disabled' : '' }}
                                    >

                                    <button
                                        type="submit"
                                        class="tg-button tg-btn-draw"
                                        {{ !$isOpen ? 'disabled' : '' }}
                                    >
                                        Bet Draw
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <aside style="display:flex; flex-direction:column; gap:18px;">
                    <div class="tg-card">
                        <h2 class="tg-section-title">My Latest Bets</h2>
                        <p class="tg-section-sub">Your recent bet records.</p>

                        <div class="tg-history-list" style="margin-top:14px;">
                            @forelse($myBets as $bet)
                                <div class="tg-history-item">
                                    <div>
                                        <p class="tg-history-title">
                                            {{ strtoupper($bet->side) }} — ₱{{ number_format($bet->amount, 2) }}
                                        </p>
                                        <p class="tg-history-sub">
                                            Round: {{ $bet->round?->round_code ?? $bet->game_round_id }}
                                            • Odds {{ number_format($bet->odds_at_bet, 2) }}x
                                        </p>
                                    </div>

                                    <span class="tg-history-pill">
                                        {{ $bet->status }}
                                    </span>
                                </div>
                            @empty
                                <div class="tg-empty">
                                    No bets yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:18px;">
                        <div class="tg-score-strip">
                            <div class="tg-score-box" style="background:#ef4444;">
                                <p class="tg-score-label">Meron</p>
                                <h3 class="tg-score-value">{{ $meronCount }}</h3>
                            </div>

                            <div class="tg-score-box" style="background:#3b82f6;">
                                <p class="tg-score-label">Wala</p>
                                <h3 class="tg-score-value">{{ $walaCount }}</h3>
                            </div>

                            <div class="tg-score-box" style="background:#22c55e;">
                                <p class="tg-score-label">Draw</p>
                                <h3 class="tg-score-value">{{ $drawCount }}</h3>
                            </div>

                            <div class="tg-score-box" style="background:#9ca3af;">
                                <p class="tg-score-label">Cancelled</p>
                                <h3 class="tg-score-value">{{ $cancelledCount }}</h3>
                            </div>
                        </div>

                        <section class="tg-road-section">
                            <div class="tg-road-head">
                                <div>
                                    <p class="tg-road-kicker">Logrohan</p>
                                    <h2 class="tg-road-title">Result Pattern</h2>
                                </div>
                            </div>

                            <div class="tg-logrohan-board">
                                <div class="tg-logrohan-inner">
                                    @forelse($roadEntries as $index => $entry)
                                        @php
                                            $side = strtolower($entry->winning_side ?? $entry->result ?? 'cancelled');

                                            if ($side === 'canceled' || $side === 'cancel') {
                                                $side = 'cancelled';
                                            }

                                            if (!in_array($side, ['meron', 'wala', 'draw', 'cancelled'])) {
                                                $side = 'cancelled';
                                            }

                                            $row = $index % 6;
                                            $col = floor($index / 6);

                                            $left = 11 + ($col * 38);
                                            $top = 11 + ($row * 38);

                                            $label = match ($side) {
                                                'meron' => 'M',
                                                'wala' => 'W',
                                                'draw' => 'D',
                                                default => 'C',
                                            };
                                        @endphp

                                        <div
                                            class="tg-road-dot {{ $side }}"
                                            style="left:{{ $left }}px; top:{{ $top }}px;"
                                            title="{{ strtoupper($side) }} - {{ $entry->round_code ?? $entry->id }}"
                                        >
                                            {{ $label }}
                                        </div>
                                    @empty
                                        <div style="padding:24px; color:#64748b; font-weight:900;">
                                            No result history yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </section>

                        <section class="tg-road-section">
                            <div class="tg-road-head">
                                <div>
                                    <p class="tg-road-kicker">Road</p>
                                    <h2 class="tg-road-title">Bead Road</h2>
                                </div>

                                <p class="tg-road-subtitle">Latest Results</p>
                            </div>

                            <div class="tg-bead-board">
                                <div class="tg-bead-inner">
                                    @for($i = 0; $i < 144; $i++)
                                        @php
                                            $row = $i % 6;
                                            $col = floor($i / 6);

                                            $left = 11 + ($col * 38);
                                            $top = 11 + ($row * 38);
                                        @endphp

                                        <div
                                            class="tg-road-empty-cell"
                                            style="left:{{ $left }}px; top:{{ $top }}px;"
                                        ></div>
                                    @endfor

                                    @foreach($roadEntries as $index => $entry)
                                        @php
                                            $side = strtolower($entry->winning_side ?? $entry->result ?? 'cancelled');

                                            if ($side === 'canceled' || $side === 'cancel') {
                                                $side = 'cancelled';
                                            }

                                            if (!in_array($side, ['meron', 'wala', 'draw', 'cancelled'])) {
                                                $side = 'cancelled';
                                            }

                                            $row = $index % 6;
                                            $col = floor($index / 6);

                                            $left = 11 + ($col * 38);
                                            $top = 11 + ($row * 38);

                                            $number = $index + 1;
                                        @endphp

                                        <div
                                            class="tg-bead-dot {{ $side }}"
                                            style="left:{{ $left }}px; top:{{ $top }}px;"
                                            title="{{ strtoupper($side) }} - {{ $entry->round_code ?? $entry->id }}"
                                        >
                                            {{ $number }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>
                </aside>
            </section>
        @endif
    </div>
</x-layouts.app>