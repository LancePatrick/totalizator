<x-layouts.app :title="__('My Players')">
    <style>
        .ap-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .ap-hero {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            padding: 28px;
            color: white;
            background:
                radial-gradient(circle at 82% 45%, rgba(59,130,246,.60), transparent 26%),
                radial-gradient(circle at 18% 20%, rgba(250,204,21,.16), transparent 22%),
                linear-gradient(135deg, #03142f 0%, #041a4d 52%, #2563eb 100%);
            box-shadow: 0 18px 42px rgba(2,18,54,.18);
        }

        .ap-hero::after {
            content: "👥";
            position: absolute;
            right: 46px;
            top: 14px;
            font-size: 110px;
            transform: rotate(-8deg);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.28));
        }

        .ap-hero-inner {
            position: relative;
            z-index: 2;
            max-width: 860px;
        }

        .ap-kicker {
            margin: 0;
            color: #93c5fd;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .ap-title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .ap-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.76);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
        }

        .ap-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .ap-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
        }

        .ap-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .ap-card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .ap-link-box {
            display: grid;
            gap: 10px;
            margin-top: 16px;
        }

        .ap-input-copy {
            width: 100%;
            min-height: 50px;
            border-radius: 14px;
            border: 1px solid #dce6f2;
            background: #f8fafc;
            padding: 0 14px;
            color: #0f172a;
            font-size: 13px;
            font-weight: 800;
        }

        .ap-btn {
            min-height: 46px;
            border: 0;
            border-radius: 14px;
            padding: 0 16px;
            color: white;
            background: #2563eb;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .ap-btn-gray {
            background: #64748b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .ap-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .ap-input {
            width: 100%;
            max-width: 320px;
            height: 48px;
            border-radius: 14px;
            border: 1px solid #dce6f2;
            background: #ffffff;
            padding: 0 14px;
            outline: none;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .ap-table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e7edf6;
            border-radius: 18px;
            margin-top: 16px;
        }

        .ap-table {
            width: 100%;
            min-width: 950px;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .ap-table thead {
            background: #f8fbff;
        }

        .ap-table th {
            padding: 14px;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
            border-bottom: 1px solid #e7edf6;
            white-space: nowrap;
        }

        .ap-table td {
            padding: 15px 14px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: top;
        }

        .ap-name {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
        }

        .ap-muted {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.45;
        }

        .ap-amount {
            margin: 0;
            color: #0f172a;
            font-size: 15px;
            font-weight: 950;
            white-space: nowrap;
        }

        .ap-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .ap-pill.active {
            background: #dcfce7;
            color: #15803d;
        }

        .ap-pill.inactive {
            background: #fee2e2;
            color: #dc2626;
        }

        .ap-empty {
            padding: 32px 20px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            font-weight: 850;
        }

        @media (max-width: 900px) {
            .ap-hero::after {
                display: none;
            }

            .ap-card-head {
                flex-direction: column;
            }

            .ap-title {
                font-size: 28px;
            }
        }
    </style>

    <div class="ap-page">
        <section class="ap-hero">
            <div class="ap-hero-inner">
                <p class="ap-kicker">Agent Panel</p>

                <h1 class="ap-title">
                    My Players
                </h1>

                <p class="ap-subtitle">
                    View your assigned players, wallet balances, betting activity, load history, and withdrawal totals.
                </p>
            </div>
        </section>

        <section class="ap-card">
            <div class="ap-card-head">
                <div>
                    <h2 class="ap-card-title">Registration Link</h2>
                    <p class="ap-card-sub">
                        Send this link to players. When they register, they will be automatically assigned to you.
                    </p>
                </div>
            </div>

            <div class="ap-link-box">
                <input
                    id="registrationLink"
                    type="text"
                    value="{{ $registrationLink }}"
                    class="ap-input-copy"
                    readonly
                >

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button type="button" class="ap-btn" onclick="copyAgentLink()">
                        Copy Link
                    </button>

                    <a href="{{ $registrationLink }}" target="_blank" class="ap-btn ap-btn-gray">
                        Open Link
                    </a>
                </div>

                <p id="copyMessage" class="ap-muted" style="display:none;color:#15803d;font-weight:900;">
                    Registration link copied.
                </p>
            </div>
        </section>

        <section class="ap-card">
            <div class="ap-card-head">
                <div>
                    <h2 class="ap-card-title">Players List</h2>
                    <p class="ap-card-sub">
                        Total players shown: {{ $players->total() }}
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('agent.players.index') }}" class="ap-filter">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="ap-input"
                    placeholder="Search player name or email"
                >

                <button type="submit" class="ap-btn">
                    Search
                </button>

                <a href="{{ route('agent.players.index') }}" class="ap-btn ap-btn-gray">
                    Reset
                </a>
            </form>

            <div class="ap-table-wrap">
                <table class="ap-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Wallet</th>
                            <th>Total Bets</th>
                            <th>Total Loads</th>
                            <th>Total Withdrawals</th>
                            <th>Status</th>
                            <th>Registered</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($players as $player)
                            <tr>
                                <td>
                                    <p class="ap-name">{{ $player->name }}</p>
                                    <p class="ap-muted">{{ $player->email }}</p>
                                </td>

                                <td>
                                    <p class="ap-amount">
                                        ₱{{ number_format($player->wallet_balance ?? 0, 2) }}
                                    </p>
                                </td>

                                <td>
                                    <p class="ap-amount">
                                        ₱{{ number_format($player->total_bets ?? 0, 2) }}
                                    </p>
                                </td>

                                <td>
                                    <p class="ap-amount">
                                        ₱{{ number_format($player->total_load_requests ?? 0, 2) }}
                                    </p>
                                </td>

                                <td>
                                    <p class="ap-amount">
                                        ₱{{ number_format($player->total_withdrawals ?? 0, 2) }}
                                    </p>
                                </td>

                                <td>
                                    @if($player->is_active)
                                        <span class="ap-pill active">Active</span>
                                    @else
                                        <span class="ap-pill inactive">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    <p class="ap-name">
                                        {{ $player->created_at?->format('M d, Y') }}
                                    </p>

                                    <p class="ap-muted">
                                        {{ $player->created_at?->format('h:i A') }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="ap-empty">
                                        No players found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px;">
                {{ $players->links() }}
            </div>
        </section>
    </div>

    <script>
        function copyAgentLink() {
            const input = document.getElementById('registrationLink');
            const message = document.getElementById('copyMessage');

            if (!input) {
                return;
            }

            input.select();
            input.setSelectionRange(0, 99999);

            navigator.clipboard.writeText(input.value).then(function () {
                if (message) {
                    message.style.display = 'block';

                    setTimeout(function () {
                        message.style.display = 'none';
                    }, 2000);
                }
            });
        }
    </script>
</x-layouts.app>