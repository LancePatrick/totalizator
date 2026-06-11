<x-layouts.app :title="__('Agent Reports')">
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
            content: "AR";
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

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .summary-card {
            background: #ffffff;
            border: 1px solid #dce6f2;
            border-radius: 22px;
            padding: 20px;
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
            font-size: 28px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .summary-sub {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
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

        .card {
            background: #ffffff;
            border: 1px solid #dce6f2;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .045);
        }

        .card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
        }

        .card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
        }

        .filter {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto auto auto;
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

        .btn-green {
            background: #16a34a;
            color: white;
            box-shadow: 0 10px 22px rgba(22, 163, 74, .16);
        }

        .btn-dark {
            background: #0f172a;
            color: white;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            background: white;
        }

        table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            padding: 14px;
            background: #f8fbff;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        td {
            padding: 16px 14px;
            border-bottom: 1px solid #edf2f7;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #f8fbff;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .name {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
        }

        .muted {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
        }

        .amount {
            font-weight: 950;
            color: #0f172a;
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

        .pill {
            min-height: 32px;
            border-radius: 999px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 950;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        .pill-green {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .pill-red {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .pill-blue {
            background: #dbeafe;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .empty {
            padding: 34px 20px;
            text-align: center;
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

            .filter {
                grid-template-columns: 1fr 1fr;
            }

            .card-head {
                flex-direction: column;
            }
        }

        @media(max-width: 700px) {
            .summary-grid,
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

        $visibleAgentWallet = $visibleAgents->sum(function ($row) {
            $agent = data_get($row, 'agent', $row);

            return (float) data_get($agent, 'wallet_balance', 0);
        });

        $visiblePlayers = $visibleAgents->sum(function ($row) {
            return (int) data_get($row, 'players_count', data_get($row, 'total_players_count', 0));
        });

        $computedTotalPlayerBets = $visibleAgents->sum(function ($row) {
            return (float) data_get($row, 'total_bets', data_get($row, 'total_player_bets', 0));
        });

        $computedTotalAgentCommission = $visibleAgents->sum(function ($row) {
            return (float) data_get($row, 'agent_commission', data_get($row, 'computed_agent_commission', 0));
        });

        $totalPlayerBets = $totalPlayerBets ?? $computedTotalPlayerBets;
        $totalAgentCommission = $totalAgentCommission ?? $computedTotalAgentCommission;
    @endphp

    <div class="page">
        <section class="hero">
            <div class="hero-content">
                <p class="kicker">Admin Monitoring</p>
                <h1 class="title">Agent Reports</h1>
                <p class="subtitle">
                    Agent wallet, assigned players, total player bets, computed 2% agent commission, and exportable report.
                </p>
            </div>
        </section>

        <section class="summary-grid">
            <div class="summary-card">
                <p class="summary-label">Total Agents</p>
                <h2 class="summary-value">{{ number_format($totalAgents) }}</h2>
                <p class="summary-sub">Filtered agent count</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Visible Players</p>
                <h2 class="summary-value blue">{{ number_format($visiblePlayers) }}</h2>
                <p class="summary-sub">Players on this page</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Total Player Bets</p>
                <h2 class="summary-value blue">₱{{ number_format($totalPlayerBets, 2) }}</h2>
                <p class="summary-sub">All filtered agent player bets</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Total Agent Commission 2%</p>
                <h2 class="summary-value orange">₱{{ number_format($totalAgentCommission, 2) }}</h2>
                <p class="summary-sub">Computed from player bets</p>
            </div>

            <div class="summary-card">
                <p class="summary-label">Visible Agent Wallet</p>
                <h2 class="summary-value green">₱{{ number_format($visibleAgentWallet, 2) }}</h2>
                <p class="summary-sub">Wallet of agents on this page</p>
            </div>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Filters</h2>
                    <p class="card-sub">Filter by agent name, email, agent code, and date range.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.agent-reports.index') }}" class="filter">
                <input
                    class="input"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search agent name, email, or code"
                >

                <input
                    class="input"
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                >

                <input
                    class="input"
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                >

                <button class="btn btn-blue">Filter</button>

                <a class="btn btn-dark" href="{{ route('admin.agent-reports.index') }}">
                    Reset
                </a>

                <a class="btn btn-green" href="{{ route('admin.agent-reports.export', request()->query()) }}">
                    Export CSV
                </a>
            </form>
        </section>

        <section class="card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">Agent Commission Report</h2>
                    <p class="card-sub">Showing agent wallet, players, total player bets, and computed commission.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Email</th>
                            <th>Agent Code</th>
                            <th>Players</th>
                            <th>Wallet</th>
                            <th>Total Player Bets</th>
                            <th>Agent Commission 2%</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($visibleAgents as $row)
                            @php
                                $agent = data_get($row, 'agent', $row);

                                $agentName = data_get($agent, 'name', 'N/A');
                                $agentId = data_get($agent, 'id', 'N/A');
                                $agentEmail = data_get($agent, 'email', 'N/A');
                                $agentCode = data_get($agent, 'agent_code', 'N/A');

                                $agentPlayers = (int) data_get($row, 'players_count', data_get($row, 'total_players_count', 0));
                                $agentWallet = (float) data_get($agent, 'wallet_balance', data_get($row, 'wallet_balance', 0));
                                $agentBets = (float) data_get($row, 'total_bets', data_get($row, 'total_player_bets', 0));
                                $agentCommission = (float) data_get($row, 'agent_commission', data_get($row, 'computed_agent_commission', 0));

                                $agentIsActive = (bool) data_get($agent, 'is_active', data_get($row, 'is_active', true));
                            @endphp

                            <tr>
                                <td>
                                    <p class="name">{{ $agentName }}</p>
                                    <p class="muted">Agent ID: #{{ $agentId }}</p>
                                </td>

                                <td>
                                    {{ $agentEmail }}
                                </td>

                                <td>
                                    <span class="pill pill-blue">
                                        {{ $agentCode ?: 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount">
                                        {{ number_format($agentPlayers) }}
                                    </span>
                                    <p class="muted">assigned player/s</p>
                                </td>

                                <td>
                                    <span class="amount green">
                                        ₱{{ number_format($agentWallet, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="bet-amount">
                                        ₱{{ number_format($agentBets, 2) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="commission-amount">
                                        ₱{{ number_format($agentCommission, 2) }}
                                    </span>
                                    <p class="muted">2% of player bets</p>
                                </td>

                                <td>
                                    <span class="pill {{ $agentIsActive ? 'pill-green' : 'pill-red' }}">
                                        {{ $agentIsActive ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty">
                                        No agents found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($agents, 'links'))
                <div class="pagination">
                    {{ $agents->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>