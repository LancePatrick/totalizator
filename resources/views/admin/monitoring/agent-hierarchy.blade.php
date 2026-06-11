<x-layouts.app :title="__('Agent Hierarchy')">
    <style>
        .page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .hero {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 28px;
            background: linear-gradient(135deg, #03142f 0%, #041a4d 55%, #064e3b 100%);
            color: white;
            box-shadow: 0 18px 42px rgba(15, 23, 42, .12);
        }

        .hero::after {
            content: "AG";
            position: absolute;
            right: 42px;
            top: 8px;
            font-size: 110px;
            font-weight: 950;
            color: rgba(255, 255, 255, .10);
            transform: rotate(-8deg);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .subtitle {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, .74);
            font-size: 14px;
            font-weight: 750;
            line-height: 1.6;
        }

        .card {
            background: #ffffff;
            border: 1px solid #dce6f2;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .045);
        }

        .filter {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 10px;
        }

        .input {
            width: 100%;
            height: 46px;
            border-radius: 14px;
            border: 1px solid #dce6f2;
            background: #ffffff;
            padding: 0 14px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            outline: none;
        }

        .input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
        }

        .btn {
            min-height: 46px;
            border: 0;
            border-radius: 14px;
            padding: 0 18px;
            font-size: 12px;
            font-weight: 950;
            cursor: pointer;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .btn-blue {
            background: #2563eb;
            color: white;
            box-shadow: 0 10px 22px rgba(37, 99, 235, .18);
        }

        .btn-dark {
            background: #0f172a;
            color: white;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
        }

        .summary-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .045);
        }

        .summary-label {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 850;
        }

        .summary-value {
            margin: 8px 0 0;
            color: #0f172a;
            font-size: 26px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .green {
            color: #16a34a !important;
        }

        .blue {
            color: #2563eb !important;
        }

        .orange {
            color: #ea580c !important;
        }

        .agent-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        details.agent-item {
            background: #ffffff;
            border: 1px solid #dce6f2;
            border-radius: 22px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .045);
            overflow: hidden;
        }

        details.agent-item[open] {
            border-color: rgba(37, 99, 235, .35);
            box-shadow: 0 18px 36px rgba(37, 99, 235, .08);
        }

        summary.agent-summary {
            list-style: none;
            cursor: pointer;
            padding: 18px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 16px;
            align-items: center;
        }

        summary.agent-summary::-webkit-details-marker {
            display: none;
        }

        .agent-main {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .avatar {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 950;
            flex: 0 0 auto;
        }

        .agent-name {
            margin: 0;
            color: #0f172a;
            font-size: 17px;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .agent-meta {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
            line-height: 1.5;
        }

        .badges {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .badge {
            min-height: 34px;
            border-radius: 999px;
            padding: 0 12px;
            background: #f8fbff;
            border: 1px solid #dce6f2;
            color: #0f172a;
            font-size: 12px;
            font-weight: 950;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .badge-red {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .badge-blue {
            background: #dbeafe;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .badge-yellow {
            background: #fef3c7;
            color: #b45309;
            border-color: #fde68a;
        }

        .badge-orange {
            background: #ffedd5;
            color: #ea580c;
            border-color: #fed7aa;
        }

        .players-panel {
            border-top: 1px solid #e7edf6;
            padding: 18px;
            background: #f8fbff;
        }

        .players-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .players-title {
            margin: 0;
            color: #0f172a;
            font-size: 15px;
            font-weight: 950;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: white;
        }

        table {
            width: 100%;
            min-width: 950px;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            padding: 13px 14px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #edf2f7;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .muted {
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
            margin-top: 4px;
        }

        .amount {
            font-weight: 950;
            color: #16a34a;
            white-space: nowrap;
        }

        .bet-amount {
            font-weight: 950;
            color: #2563eb;
            white-space: nowrap;
        }

        .commission-amount {
            font-weight: 950;
            color: #ea580c;
            white-space: nowrap;
        }

        .empty {
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            background: white;
            border: 1px dashed #cbd5e1;
            color: #64748b;
            font-weight: 850;
        }

        .pagination {
            margin-top: 16px;
        }

        @media(max-width: 1200px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width: 1000px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }

            summary.agent-summary {
                grid-template-columns: 1fr;
            }

            .badges {
                justify-content: flex-start;
            }

            .filter {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $agents = $agents ?? collect();

        $visibleAgents = method_exists($agents, 'items')
            ? collect($agents->items())
            : collect($agents);

        $totalAgents = method_exists($agents, 'total')
            ? $agents->total()
            : $visibleAgents->count();

        $visiblePlayers = $visibleAgents->sum(function ($agent) {
            return (int) data_get($agent, 'players_count', data_get($agent, 'total_players_count', 0));
        });

        $visibleWallet = $visibleAgents->sum(function ($agent) {
            return (float) data_get($agent, 'wallet_balance', 0);
        });

        $visibleTotalBets = $visibleAgents->sum(function ($agent) {
            return (float) data_get($agent, 'players_total_bets', data_get($agent, 'total_player_bets', 0));
        });

        $visibleAgentCommission = $visibleAgents->sum(function ($agent) {
            $bets = (float) data_get($agent, 'players_total_bets', data_get($agent, 'total_player_bets', 0));
            return round($bets * 0.02, 2);
        });
    @endphp

    <div class="page">
        <section class="hero">
            <div class="hero-content">
                <p class="kicker">Admin Monitoring</p>
                <h1 class="title">Agent Hierarchy Tree</h1>
                <p class="subtitle">
                    View all agents, assigned players, wallet balance, total player bets, and agent commission.
                </p>
            </div>
        </section>

        <section class="summary-grid">
            <div class="summary-card">
                <p class="summary-label">Total Agents</p>
                <h2 class="summary-value">{{ number_format($totalAgents) }}</h2>
            </div>

            <div class="summary-card">
                <p class="summary-label">Visible Players</p>
                <h2 class="summary-value">{{ number_format($visiblePlayers) }}</h2>
            </div>

            <div class="summary-card">
                <p class="summary-label">Visible Agent Wallet</p>
                <h2 class="summary-value green">₱{{ number_format($visibleWallet, 2) }}</h2>
            </div>

            <div class="summary-card">
                <p class="summary-label">Visible Player Bets</p>
                <h2 class="summary-value blue">₱{{ number_format($visibleTotalBets, 2) }}</h2>
            </div>

            <div class="summary-card">
                <p class="summary-label">Visible Agent Commission 2%</p>
                <h2 class="summary-value orange">₱{{ number_format($visibleAgentCommission, 2) }}</h2>
            </div>
        </section>

        <section class="card">
            <form method="GET" action="{{ route('admin.agent-hierarchy.index') }}" class="filter">
                <input
                    class="input"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search agent name, email, code, player name, phone, or player email"
                >

                <button class="btn btn-blue">Search</button>

                <a class="btn btn-dark" href="{{ route('admin.agent-hierarchy.index') }}">
                    Reset
                </a>
            </form>
        </section>

        <section class="agent-list">
            @forelse($visibleAgents as $agent)
                @php
                    $agentName = data_get($agent, 'name', 'N/A');
                    $agentId = data_get($agent, 'id', 'N/A');
                    $agentEmail = data_get($agent, 'email', 'No email');
                    $agentCode = data_get($agent, 'agent_code', 'N/A');

                    $agentInitials = strtoupper(substr($agentName ?: 'A', 0, 2));

                    $agentWallet = (float) data_get($agent, 'wallet_balance', 0);
                    $agentTotalBets = (float) data_get($agent, 'players_total_bets', data_get($agent, 'total_player_bets', 0));
                    $agentCommission = round($agentTotalBets * 0.02, 2);

                    $agentPlayersCount = (int) data_get($agent, 'players_count', data_get($agent, 'total_players_count', 0));
                    $agentIsActive = (bool) data_get($agent, 'is_active', true);

                    /*
                    |--------------------------------------------------------------------------
                    | Controller-based fallback
                    |--------------------------------------------------------------------------
                    | Your current controller only sends players_count, players_wallet_sum,
                    | and players_total_bets. It does not send the actual players list.
                    | So this index loads the player list here, then computes each player total bet.
                    |--------------------------------------------------------------------------
                    */

                    $players = collect(data_get($agent, 'players', []));

                    if ($players->isEmpty() && is_numeric($agentId)) {
                        $players = \App\Models\User::query()
                            ->where('role', 'player')
                            ->where('agent_id', $agentId)
                            ->orderBy('name')
                            ->get()
                            ->map(function ($player) {
                                $playerBetsQuery = \App\Models\GameBet::query()
                                    ->where('user_id', $player->id)
                                    ->whereNotIn('status', ['refunded', 'cancelled']);

                                if (\Illuminate\Support\Facades\Schema::hasTable('game_rounds')) {
                                    $playerBetsQuery->whereNotIn('game_round_id', function ($query) {
                                        $query->select('id')
                                            ->from('game_rounds')
                                            ->where('winning_side', 'cancelled');
                                    });
                                }

                                $playerTotalBets = (float) $playerBetsQuery->sum('amount');

                                $player->total_bets = $playerTotalBets;
                                $player->agent_commission_amount = round($playerTotalBets * 0.02, 2);

                                return $player;
                            });
                    }
                @endphp

                <details class="agent-item">
                    <summary class="agent-summary">
                        <div class="agent-main">
                            <div class="avatar">
                                {{ $agentInitials }}
                            </div>

                            <div>
                                <h2 class="agent-name">
                                    {{ $agentName }}
                                </h2>

                                <div class="agent-meta">
                                    {{ $agentEmail }} • Code: {{ $agentCode ?: 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="badges">
                            <span class="badge badge-blue">
                                Players: {{ number_format($agentPlayersCount) }}
                            </span>

                            <span class="badge">
                                Wallet: ₱{{ number_format($agentWallet, 2) }}
                            </span>

                            <span class="badge badge-blue">
                                Bets: ₱{{ number_format($agentTotalBets, 2) }}
                            </span>

                            <span class="badge badge-orange">
                                Commission 2%: ₱{{ number_format($agentCommission, 2) }}
                            </span>

                            <span class="badge {{ $agentIsActive ? 'badge-green' : 'badge-red' }}">
                                {{ $agentIsActive ? 'Active' : 'Inactive' }}
                            </span>

                            <span class="badge badge-yellow">
                                Click to View Players
                            </span>
                        </div>
                    </summary>

                    <div class="players-panel">
                        <div class="players-head">
                            <h3 class="players-title">
                                Players under {{ $agentName }}
                            </h3>

                            <div class="badges">
                                <span class="badge badge-blue">
                                    {{ number_format($players->count()) }} player/s loaded
                                </span>

                                <span class="badge badge-blue">
                                    Total Bets: ₱{{ number_format($agentTotalBets, 2) }}
                                </span>

                                <span class="badge badge-orange">
                                    Agent Commission: ₱{{ number_format($agentCommission, 2) }}
                                </span>
                            </div>
                        </div>

                        @if($players->count() > 0)
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Player</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Wallet</th>
                                            <th>Total Bet</th>
                                            <th>Agent Commission 2%</th>
                                            <th>Status</th>
                                            <th>Registered</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($players as $player)
                                            @php
                                                $playerName = data_get($player, 'name', 'N/A');
                                                $playerId = data_get($player, 'id', 'N/A');
                                                $playerEmail = data_get($player, 'email', 'N/A');
                                                $playerPhone = data_get($player, 'phone', 'N/A');
                                                $playerWallet = (float) data_get($player, 'wallet_balance', 0);

                                                $playerTotalBets = (float) data_get($player, 'total_bets', 0);
                                                $playerAgentCommission = (float) data_get(
                                                    $player,
                                                    'agent_commission_amount',
                                                    round($playerTotalBets * 0.02, 2)
                                                );

                                                $playerIsActive = (bool) data_get($player, 'is_active', true);
                                                $playerCreatedAt = data_get($player, 'created_at');
                                            @endphp

                                            <tr>
                                                <td>
                                                    {{ $playerName }}
                                                    <div class="muted">Player ID: #{{ $playerId }}</div>
                                                </td>

                                                <td>
                                                    {{ $playerEmail }}
                                                </td>

                                                <td>
                                                    {{ $playerPhone ?: 'N/A' }}
                                                </td>

                                                <td>
                                                    <span class="amount">
                                                        ₱{{ number_format($playerWallet, 2) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="bet-amount">
                                                        ₱{{ number_format($playerTotalBets, 2) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="commission-amount">
                                                        ₱{{ number_format($playerAgentCommission, 2) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="badge {{ $playerIsActive ? 'badge-green' : 'badge-red' }}">
                                                        {{ $playerIsActive ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>

                                                <td>
                                                    @if($playerCreatedAt)
                                                        {{ \Illuminate\Support\Carbon::parse($playerCreatedAt)->format('M d, Y') }}
                                                        <div class="muted">
                                                            {{ \Illuminate\Support\Carbon::parse($playerCreatedAt)->format('h:i A') }}
                                                        </div>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty">
                                No players assigned to this agent yet.
                            </div>
                        @endif
                    </div>
                </details>
            @empty
                <div class="card">
                    <div class="empty">
                        No agents found.
                    </div>
                </div>
            @endforelse
        </section>

        @if(method_exists($agents, 'links'))
            <div class="pagination">
                {{ $agents->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>