<x-layouts.app :title="__('Manage Agents')">
    <style>
        .agents-page {
            display: grid;
            gap: 18px;
        }

        .agents-hero {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 34px;
            color: white;
            background:
                radial-gradient(circle at 84% 42%, rgba(37, 99, 235, .55), transparent 28%),
                linear-gradient(135deg, #061a40 0%, #082a72 52%, #1455d9 100%);
            box-shadow: 0 18px 44px rgba(2, 18, 54, .18);
        }

        .agents-hero::after {
            content: "AG";
            position: absolute;
            right: 44px;
            top: 22px;
            color: rgba(255, 255, 255, .12);
            font-size: 96px;
            font-weight: 950;
            transform: rotate(-8deg);
        }

        .agents-kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .agents-title {
            margin: 10px 0 0;
            color: white;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .agents-subtitle {
            margin: 12px 0 0;
            max-width: 760px;
            color: rgba(255, 255, 255, .78);
            font-size: 14px;
            font-weight: 750;
            line-height: 1.6;
        }

        .agents-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }

        .agents-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
        }

        .agents-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .agents-card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
        }

        .agents-back {
            height: 42px;
            border-radius: 12px;
            background: #0f172a;
            color: white;
            padding: 0 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .agents-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 850;
        }

        .agents-alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .agents-alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .agents-filter {
            display: grid;
            grid-template-columns: 1.5fr .7fr .8fr .8fr auto;
            gap: 10px;
            margin-bottom: 14px;
        }

        .agents-input,
        .agents-select {
            width: 100%;
            height: 42px;
            border-radius: 13px;
            border: 1px solid #cbd5e1;
            background: white;
            color: #0f172a;
            padding: 0 14px;
            font-size: 12px;
            font-weight: 850;
            outline: none;
        }

        .agents-input:focus,
        .agents-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }

        .agents-filter-btn {
            height: 42px;
            border: 0;
            border-radius: 13px;
            background: #2563eb;
            color: white;
            padding: 0 18px;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            cursor: pointer;
        }

        .agents-table-wrap {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
        }

        .agents-table {
            width: 100%;
            min-width: 1180px;
            border-collapse: collapse;
            text-align: left;
        }

        .agents-table thead {
            background: #f8fafc;
        }

        .agents-table th {
            padding: 14px 14px;
            color: #64748b;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .12em;
            border-bottom: 1px solid #e2e8f0;
        }

        .agents-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
        }

        .agents-name {
            margin: 0;
            color: #0f172a;
            font-size: 13px;
            font-weight: 950;
        }

        .agents-muted {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
        }

        .agents-money {
            margin: 0;
            color: #16a34a;
            font-size: 14px;
            font-weight: 950;
        }

        .agents-commission {
            margin: 0;
            color: #d97706;
            font-size: 14px;
            font-weight: 950;
        }

        .agents-commission-available {
            margin: 5px 0 0;
            color: #2563eb;
            font-size: 11px;
            font-weight: 900;
        }

        .agents-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
        }

        .agents-pill.active {
            background: #dcfce7;
            color: #15803d;
        }

        .agents-pill.inactive {
            background: #fee2e2;
            color: #dc2626;
        }

        .agents-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .agents-btn {
            min-height: 36px;
            border: 0;
            border-radius: 11px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .agents-btn-blue {
            background: #2563eb;
        }

        .agents-btn-amber {
            background: #d97706;
        }

        .agents-btn-red {
            background: #dc2626;
        }

        .agents-btn-green {
            background: #16a34a;
        }

        .agents-empty {
            padding: 38px 20px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            font-weight: 850;
        }

        .agents-pagination {
            margin-top: 16px;
        }

        .agent-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, .58);
            backdrop-filter: blur(6px);
        }

        .agent-modal.show {
            display: flex;
        }

        .agent-modal-card {
            width: 100%;
            max-width: 560px;
            overflow: hidden;
            border-radius: 24px;
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 30px 90px rgba(15, 23, 42, .28);
        }

        .agent-modal-head {
            padding: 22px 24px;
            color: white;
            background:
                radial-gradient(circle at 88% 20%, rgba(59, 130, 246, .55), transparent 28%),
                linear-gradient(135deg, #071a3d 0%, #123a9b 100%);
            display: flex;
            justify-content: space-between;
            gap: 14px;
        }

        .agent-modal-title {
            margin: 0;
            color: white;
            font-size: 22px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .agent-modal-sub {
            margin: 6px 0 0;
            color: rgba(255,255,255,.75);
            font-size: 12px;
            font-weight: 750;
        }

        .agent-modal-close {
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            color: white;
            font-size: 18px;
            font-weight: 950;
            cursor: pointer;
        }

        .agent-modal-body {
            padding: 22px 24px 24px;
            display: grid;
            gap: 14px;
        }

        .agent-stat-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .agent-stat {
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 15px;
        }

        .agent-stat-label {
            margin: 0;
            color: #64748b;
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .agent-stat-value {
            margin: 8px 0 0;
            color: #0f172a;
            font-size: 20px;
            font-weight: 950;
        }

        .agent-modal-form {
            display: grid;
            gap: 12px;
        }

        .agent-modal-label {
            margin: 0 0 8px;
            color: #475569;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .agent-modal-input {
            width: 100%;
            height: 44px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            background: white;
            padding: 0 14px;
            color: #0f172a;
            font-size: 13px;
            font-weight: 850;
            outline: none;
        }

        .agent-modal-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }

        .agent-modal-submit {
            height: 44px;
            border: 0;
            border-radius: 14px;
            background: #2563eb;
            color: white;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            cursor: pointer;
        }

        @media (max-width: 900px) {
            .agents-hero::after {
                display: none;
            }

            .agents-filter {
                grid-template-columns: 1fr;
            }

            .agents-head {
                flex-direction: column;
            }

            .agents-back {
                width: 100%;
            }

            .agent-stat-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="agents-page">
        <section class="agents-hero">
            <p class="agents-kicker">Admin Panel</p>

            <h1 class="agents-title">
                Manage Agents
            </h1>

            <p class="agents-subtitle">
                View, filter, activate, deactivate, change passwords, and monitor agent commission balances.
            </p>
        </section>

        @if(session('success'))
            <div class="agents-alert agents-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="agents-alert agents-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="agents-card">
            <div class="agents-head">
                <div>
                    <h2 class="agents-card-title">
                        Agents List
                    </h2>

                    <p class="agents-card-sub">
                        Filter agents by search, status, and registration date.
                    </p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="agents-back">
                    Back to Dashboard
                </a>
            </div>

            <form method="GET" action="{{ route('admin.agents.index') }}" class="agents-filter">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    placeholder="Search name, email, agent code"
                    class="agents-input"
                >

                <select name="status" class="agents-select">
                    <option value="">All Status</option>
                    <option value="active" @selected(($status ?? request('status')) === 'active')>Active</option>
                    <option value="inactive" @selected(($status ?? request('status')) === 'inactive')>Inactive</option>
                </select>

                <input
                    type="date"
                    name="date_from"
                    value="{{ $dateFrom ?? request('date_from') }}"
                    class="agents-input"
                >

                <input
                    type="date"
                    name="date_to"
                    value="{{ $dateTo ?? request('date_to') }}"
                    class="agents-input"
                >

                <button class="agents-filter-btn">
                    Filter
                </button>
            </form>

            <div class="agents-table-wrap">
                <table class="agents-table">
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Agent Code</th>
                            <th>Players</th>
                            <th>Wallet</th>
                            <th>Commission</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($agents as $agent)
                            @php
                                $walletAmount = (float) ($agent->wallet_amount ?? $agent->wallet_balance ?? 0);

                                $totalPlayerBets = (float) ($agent->total_player_bets ?? 0);

                                $computedCommission = (float) ($agent->computed_commission ?? 0);

                                if ($computedCommission <= 0 && $totalPlayerBets > 0) {
                                    $computedCommission = round($totalPlayerBets * 0.02, 2);
                                }

                                $convertedToLoad = (float) ($agent->total_converted_to_load ?? 0);

                                if (
                                    $convertedToLoad <= 0 &&
                                    \Illuminate\Support\Facades\Schema::hasTable('commission_transactions')
                                ) {
                                    $agentColumn = \Illuminate\Support\Facades\Schema::hasColumn('commission_transactions', 'agent_id')
                                        ? 'agent_id'
                                        : 'user_id';

                                    $convertedToLoad = (float) \Illuminate\Support\Facades\DB::table('commission_transactions')
                                        ->where($agentColumn, $agent->id)
                                        ->whereIn('type', [
                                            'convert_to_load',
                                            'commission_converted_to_load',
                                            'commission_to_load',
                                            'agent_commission_convert',
                                            'agent_commission_converted',
                                        ])
                                        ->where('direction', 'debit')
                                        ->sum('amount');
                                }

                                $cashoutHeld = 0;

                                if (\Illuminate\Support\Facades\Schema::hasTable('commission_withdrawal_requests')) {
                                    $cashoutHeld = (float) \Illuminate\Support\Facades\DB::table('commission_withdrawal_requests')
                                        ->where('agent_id', $agent->id)
                                        ->whereIn('status', ['pending', 'approved'])
                                        ->sum('amount');
                                }

                                $balanceFromDb = (float) ($agent->available_commission ?? $agent->commission_balance ?? 0);

                                $computedAvailable = round($computedCommission - $convertedToLoad - $cashoutHeld, 2);

                                if ($computedAvailable < 0) {
                                    $computedAvailable = 0;
                                }

                                $availableCommission = $balanceFromDb > 0
                                    ? $balanceFromDb
                                    : $computedAvailable;

                                $displayCommission = $computedCommission > 0
                                    ? $computedCommission
                                    : $availableCommission;
                            @endphp

                            <tr>
                                <td>
                                    <p class="agents-name">
                                        {{ $agent->name }}
                                    </p>

                                    <p class="agents-muted">
                                        {{ $agent->email }}
                                    </p>
                                </td>

                                <td>
                                    <p class="agents-name">
                                        {{ $agent->agent_code ?? 'N/A' }}
                                    </p>
                                </td>

                                <td>
                                    <p class="agents-name">
                                        {{ $agent->players_count ?? 0 }}
                                    </p>

                                    <p class="agents-muted">
                                        Assigned players
                                    </p>
                                </td>

                                <td>
                                    <p class="agents-money">
                                        ₱{{ number_format($walletAmount, 2) }}
                                    </p>
                                </td>

                                <td>
                                    <p class="agents-commission">
                                        ₱{{ number_format($displayCommission, 2) }}
                                    </p>

                                    <p class="agents-muted">
                                        Total earned commission
                                    </p>

                                    <p class="agents-commission-available">
                                        Available: ₱{{ number_format($availableCommission, 2) }}
                                    </p>
                                </td>

                                <td>
                                    @if($agent->is_active)
                                        <span class="agents-pill active">
                                            Active
                                        </span>
                                    @else
                                        <span class="agents-pill inactive">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <p class="agents-muted">
                                        {{ $agent->created_at ? $agent->created_at->format('M d, Y h:i A') : 'N/A' }}
                                    </p>
                                </td>

                                <td>
                                    <div class="agents-actions">
                                        <button
                                            type="button"
                                            class="agents-btn agents-btn-blue"
                                            onclick="openCommissionModal(
                                                '{{ $agent->id }}',
                                                @js($agent->name),
                                                @js($agent->email),
                                                '{{ number_format($availableCommission, 2) }}',
                                                '{{ number_format($computedCommission, 2) }}',
                                                '{{ number_format($totalPlayerBets, 2) }}',
                                                '{{ $agent->players_count ?? 0 }}',
                                                '{{ number_format($walletAmount, 2) }}'
                                            )"
                                        >
                                            Manage Commission
                                        </button>

                                        <button
                                            type="button"
                                            class="agents-btn agents-btn-amber"
                                            onclick="openPasswordModal('{{ $agent->id }}', @js($agent->name))"
                                        >
                                            Change Pass
                                        </button>

                                        @if($agent->is_active)
                                            <form method="POST" action="{{ route('admin.agents.deactivate', $agent) }}">
                                                @csrf

                                                <button class="agents-btn agents-btn-red">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.agents.activate', $agent) }}">
                                                @csrf

                                                <button class="agents-btn agents-btn-green">
                                                    Activate
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="agents-empty">
                                        No agents found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($agents, 'links'))
                <div class="agents-pagination">
                    {{ $agents->links() }}
                </div>
            @endif
        </section>
    </div>

    <div id="commissionModal" class="agent-modal">
        <div class="agent-modal-card">
            <div class="agent-modal-head">
                <div>
                    <h3 class="agent-modal-title">
                        Manage Commission
                    </h3>

                    <p class="agent-modal-sub" id="commissionAgentName">
                        Agent
                    </p>
                </div>

                <button type="button" class="agent-modal-close" onclick="closeModal('commissionModal')">
                    ×
                </button>
            </div>

            <div class="agent-modal-body">
                <div class="agent-stat-grid">
                    <div class="agent-stat">
                        <p class="agent-stat-label">Available Commission</p>
                        <p class="agent-stat-value" id="modalAvailableCommission">₱0.00</p>
                    </div>

                    <div class="agent-stat">
                        <p class="agent-stat-label">Computed Commission</p>
                        <p class="agent-stat-value" id="modalComputedCommission">₱0.00</p>
                    </div>

                    <div class="agent-stat">
                        <p class="agent-stat-label">Total Player Bets</p>
                        <p class="agent-stat-value" id="modalTotalBets">₱0.00</p>
                    </div>

                    <div class="agent-stat">
                        <p class="agent-stat-label">Assigned Players</p>
                        <p class="agent-stat-value" id="modalPlayers">0</p>
                    </div>

                    <div class="agent-stat">
                        <p class="agent-stat-label">Agent Wallet</p>
                        <p class="agent-stat-value" id="modalWallet">₱0.00</p>
                    </div>

                    <div class="agent-stat">
                        <p class="agent-stat-label">Agent Email</p>
                        <p class="agent-stat-value" id="modalEmail" style="font-size:13px;">N/A</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="passwordModal" class="agent-modal">
        <div class="agent-modal-card">
            <div class="agent-modal-head">
                <div>
                    <h3 class="agent-modal-title">
                        Change Agent Password
                    </h3>

                    <p class="agent-modal-sub" id="passwordAgentName">
                        Agent
                    </p>
                </div>

                <button type="button" class="agent-modal-close" onclick="closeModal('passwordModal')">
                    ×
                </button>
            </div>

            <div class="agent-modal-body">
                <form method="POST" id="passwordForm" class="agent-modal-form">
                    @csrf

                    <div>
                        <p class="agent-modal-label">
                            New Password
                        </p>

                        <input
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            class="agent-modal-input"
                            placeholder="Enter new password"
                        >
                    </div>

                    <div>
                        <p class="agent-modal-label">
                            Confirm Password
                        </p>

                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            minlength="8"
                            class="agent-modal-input"
                            placeholder="Confirm new password"
                        >
                    </div>

                    <button class="agent-modal-submit">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCommissionModal(id, name, email, availableCommission, computedCommission, totalBets, playersCount, walletAmount) {
            document.getElementById('commissionAgentName').innerText = name + ' • ' + email;
            document.getElementById('modalAvailableCommission').innerText = '₱' + availableCommission;
            document.getElementById('modalComputedCommission').innerText = '₱' + computedCommission;
            document.getElementById('modalTotalBets').innerText = '₱' + totalBets;
            document.getElementById('modalPlayers').innerText = playersCount;
            document.getElementById('modalWallet').innerText = '₱' + walletAmount;
            document.getElementById('modalEmail').innerText = email;

            document.getElementById('commissionModal').classList.add('show');
        }

        function openPasswordModal(id, name) {
            document.getElementById('passwordAgentName').innerText = name;

            const form = document.getElementById('passwordForm');
            form.action = "{{ url('/admin/agents') }}/" + id + "/password";

            document.getElementById('passwordModal').classList.add('show');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal('commissionModal');
                closeModal('passwordModal');
            }
        });

        document.querySelectorAll('.agent-modal').forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.remove('show');
                }
            });
        });
    </script>
</x-layouts.app>