<x-layouts.app :title="__('Admin Game Control')">
    @php
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
    @endphp

    <style>
        .ag-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .ag-hero {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            padding: 28px;
            color: white;
            background:
                radial-gradient(circle at 82% 45%, rgba(29,124,255,.65), transparent 26%),
                radial-gradient(circle at 18% 20%, rgba(250,204,21,.16), transparent 22%),
                linear-gradient(135deg, #03142f 0%, #041a4d 52%, #0848b9 100%);
            box-shadow: 0 18px 42px rgba(2,18,54,.18);
        }

        .ag-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 54px 54px;
            opacity: .45;
        }

        .ag-hero::after {
            content: "🏁";
            position: absolute;
            right: 42px;
            top: 10px;
            font-size: 120px;
            transform: rotate(-10deg);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.30));
        }

        .ag-hero-inner {
            position: relative;
            z-index: 2;
            max-width: 860px;
        }

        .ag-kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .ag-title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .ag-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.74);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
        }

        .ag-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 850;
        }

        .ag-alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .ag-alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .ag-layout {
            display: grid;
            grid-template-columns: 420px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .ag-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .ag-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .ag-card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .ag-form {
            margin-top: 18px;
            display: grid;
            gap: 14px;
        }

        .ag-field {
            display: grid;
            gap: 7px;
        }

        .ag-label {
            color: #334155;
            font-size: 13px;
            font-weight: 900;
        }

        .ag-input,
        .ag-textarea,
        .ag-select {
            width: 100%;
            border-radius: 14px;
            border: 1px solid #dce6f2;
            background: #ffffff;
            padding: 0 14px;
            outline: none;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            transition: .16s ease;
        }

        .ag-input,
        .ag-select {
            height: 50px;
        }

        .ag-textarea {
            min-height: 100px;
            padding-top: 12px;
            resize: vertical;
        }

        .ag-input:focus,
        .ag-textarea:focus,
        .ag-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.12);
        }

        .ag-btn {
            min-height: 48px;
            border: 0;
            border-radius: 14px;
            color: white;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: .04em;
            transition: .16s ease;
        }

        .ag-btn:hover {
            transform: translateY(-1px);
            filter: brightness(.96);
        }

        .ag-btn-create {
            background: #2563eb;
            box-shadow: 0 12px 24px rgba(37,99,235,.18);
        }

        .ag-btn-start {
            background: #16a34a;
        }

        .ag-btn-close {
            background: #f97316;
        }

        .ag-btn-end {
            background: #0f172a;
        }

        .ag-btn-declare {
            background: #facc15;
            color: #0f172a;
        }

        .ag-right-stack {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .ag-current-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
        }

        .ag-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .ag-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .ag-stat {
            border-radius: 18px;
            padding: 16px;
            border: 1px solid #e7edf6;
        }

        .ag-stat-label {
            margin: 0;
            font-size: 13px;
            font-weight: 950;
        }

        .ag-stat-value {
            margin: 8px 0 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .ag-stat-odds {
            margin: 5px 0 0;
            font-size: 13px;
            font-weight: 900;
        }

        .ag-video-wrap {
            margin-top: 18px;
            background: #111827;
            border-radius: 18px;
            overflow: hidden;
            padding: 16px;
        }

        .ag-video {
            width: 100%;
            height: 420px;
            background: black;
            display: block;
            border-radius: 14px;
        }

        .ag-no-video {
            height: 420px;
            border-radius: 14px;
            background:
                radial-gradient(circle at 50% 50%, rgba(29,124,255,.18), transparent 26%),
                linear-gradient(135deg, #020617, #111827);
            color: rgba(255,255,255,.70);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 14px;
            font-weight: 900;
        }

        .ag-control-grid {
            margin-top: 18px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .ag-declare-form {
            display: flex;
            gap: 8px;
        }

        .ag-empty {
            border-radius: 18px;
            padding: 34px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            font-weight: 850;
        }

        .ag-logrohan-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .ag-logrohan-pill {
            border-radius: 999px;
            padding: 9px 13px;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .ag-logrohan-pill.meron {
            background: #fee2e2;
            color: #dc2626;
        }

        .ag-logrohan-pill.wala {
            background: #dbeafe;
            color: #2563eb;
        }

        .ag-logrohan-pill.draw {
            background: #fef3c7;
            color: #b45309;
        }

        .ag-logrohan-pill.cancelled {
            background: #f1f5f9;
            color: #475569;
        }

        @media (max-width: 1300px) {
            .ag-layout {
                grid-template-columns: 1fr;
            }

            .ag-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .ag-hero::after {
                display: none;
            }

            .ag-current-head,
            .ag-declare-form {
                flex-direction: column;
            }

            .ag-control-grid {
                grid-template-columns: 1fr;
            }

            .ag-video,
            .ag-no-video {
                height: 280px;
            }
        }

        @media (max-width: 640px) {
            .ag-stats-grid {
                grid-template-columns: 1fr;
            }

            .ag-title {
                font-size: 28px;
            }
        }
    </style>

    <div class="ag-page">
        <section class="ag-hero">
            <div class="ag-hero-inner">
                <p class="ag-kicker">Admin Game Control</p>

                <h1 class="ag-title">
                    Horse Racing Totalizator
                </h1>

                <p class="ag-subtitle">
                    Create race rounds, paste video URL, start betting, close betting, end the game, and declare Meron / Wala / Draw result.
                </p>
            </div>
        </section>

        @if(session('success'))
            <div class="ag-alert ag-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="ag-alert ag-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="ag-layout">
            <div class="ag-card">
                <h2 class="ag-card-title">Create Game Round</h2>
                <p class="ag-card-sub">Add the race details and video link.</p>

                <form method="POST" action="{{ route('admin.games.store') }}" class="ag-form">
                    @csrf

                    <div class="ag-field">
                        <label class="ag-label">Round Name</label>
                        <input
                            name="round_name"
                            required
                            class="ag-input"
                            placeholder="Horse Race Round 1"
                        >
                    </div>

                    <div class="ag-field">
                        <label class="ag-label">Round Number</label>
                        <input
                            name="round_number"
                            class="ag-input"
                            placeholder="R-001"
                        >
                    </div>

                    <div class="ag-field">
                        <label class="ag-label">Video URL</label>
                        <textarea
                            name="video_url"
                            rows="3"
                            class="ag-textarea"
                            placeholder="/videos/race.mp4 or https://example.com/race.mp4"
                        ></textarea>
                    </div>

                    <div class="ag-field">
                        <label class="ag-label">Commission Rate %</label>
                        <input
                            name="commission_rate"
                            type="number"
                            step="0.01"
                            value="10"
                            min="0"
                            max="50"
                            class="ag-input"
                        >
                    </div>

                    <button class="ag-btn ag-btn-create">
                        Create Game
                    </button>
                </form>
            </div>

            <div class="ag-right-stack">
                <div class="ag-card">
                    <div class="ag-current-head">
                        <div>
                            <h2 class="ag-card-title">Current Game</h2>
                            <p class="ag-card-sub">Live video, totals, odds, and admin controls.</p>
                        </div>

                        @if($currentGame)
                            <span
                                class="ag-status"
                                style="background:{{ $statusBg }}; color:{{ $statusColor }};"
                            >
                                {{ $currentGame->status }}
                            </span>
                        @endif
                    </div>

                    @if($currentGame)
                        <div class="ag-stats-grid">
                            <div class="ag-stat" style="background:#fff7ed;border-color:#fed7aa;">
                                <p class="ag-stat-label" style="color:#ea580c;">Meron Bets</p>
                                <h3 class="ag-stat-value">
                                    ₱{{ number_format($currentGame->meron_total ?? 0, 2) }}
                                </h3>
                                <p class="ag-stat-odds" style="color:#ea580c;">
                                    Odds {{ number_format($currentGame->meron_odds ?? 0, 2) }}x
                                </p>
                            </div>

                            <div class="ag-stat" style="background:#eff6ff;border-color:#bfdbfe;">
                                <p class="ag-stat-label" style="color:#2563eb;">Wala Bets</p>
                                <h3 class="ag-stat-value">
                                    ₱{{ number_format($currentGame->wala_total ?? 0, 2) }}
                                </h3>
                                <p class="ag-stat-odds" style="color:#2563eb;">
                                    Odds {{ number_format($currentGame->wala_odds ?? 0, 2) }}x
                                </p>
                            </div>

                            <div class="ag-stat" style="background:#f5f3ff;border-color:#ddd6fe;">
                                <p class="ag-stat-label" style="color:#7c3aed;">Draw Bets</p>
                                <h3 class="ag-stat-value">
                                    ₱{{ number_format($currentGame->draw_total ?? 0, 2) }}
                                </h3>
                                <p class="ag-stat-odds" style="color:#7c3aed;">
                                    Odds {{ number_format($currentGame->draw_odds ?? 0, 2) }}x
                                </p>
                            </div>

                            <div class="ag-stat" style="background:#0f172a;border-color:#0f172a;">
                                <p class="ag-stat-label" style="color:rgba(255,255,255,.60);">Total Pool</p>
                                <h3 class="ag-stat-value" style="color:#facc15;">
                                    ₱{{ number_format($currentGame->total_pool ?? 0, 2) }}
                                </h3>
                                <p class="ag-stat-odds" style="color:rgba(255,255,255,.65);">
                                    Net: ₱{{ number_format($currentGame->net_pool ?? 0, 2) }}
                                </p>
                            </div>
                        </div>

                        <div class="ag-video-wrap">
                            @if($currentGame->video_url)
                                <video class="ag-video" controls>
                                    <source src="{{ $currentGame->video_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <div class="ag-no-video">
                                    <div>
                                        <div style="font-size:42px;">🎥</div>
                                        <div style="margin-top:10px;">No video loaded yet.</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="ag-control-grid">
                            <form method="POST" action="{{ route('admin.games.start', $currentGame) }}">
                                @csrf
                                <button class="ag-btn ag-btn-start" style="width:100%;">
                                    Start
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.games.close', $currentGame) }}">
                                @csrf
                                <button class="ag-btn ag-btn-close" style="width:100%;">
                                    Close Betting
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.games.end', $currentGame) }}">
                                @csrf
                                <button class="ag-btn ag-btn-end" style="width:100%;">
                                    End Game
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.games.declare', $currentGame) }}" class="ag-declare-form">
                                @csrf

                                <select name="winning_side" class="ag-select">
                                    <option value="meron">Meron</option>
                                    <option value="wala">Wala</option>
                                    <option value="draw">Draw</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>

                                <button class="ag-btn ag-btn-declare">
                                    Declare
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="ag-empty">
                            No game created yet.
                        </div>
                    @endif
                </div>

                <div class="ag-card">
                    <h2 class="ag-card-title">Logrohan / Result History</h2>
                    <p class="ag-card-sub">Latest declared game results.</p>

                    <div class="ag-logrohan-list">
                        @forelse($logrohan as $entry)
                            @php
                                $result = strtolower($entry->result ?? 'cancelled');

                                if (!in_array($result, ['meron', 'wala', 'draw', 'cancelled'])) {
                                    $result = 'cancelled';
                                }
                            @endphp

                            <span class="ag-logrohan-pill {{ $result }}">
                                {{ $entry->round_number ?? 'Round' }} - {{ strtoupper($entry->result ?? 'N/A') }}
                            </span>
                        @empty
                            <div class="ag-empty" style="width:100%;">
                                No results yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>