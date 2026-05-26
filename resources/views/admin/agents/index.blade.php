<x-layouts.app :title="__('Manage Agents')">
    <style>
        .page { display:flex; flex-direction:column; gap:18px; }
        .hero {
            position:relative; overflow:hidden; border-radius:22px; padding:28px; color:white;
            background:linear-gradient(135deg,#03142f 0%,#041a4d 52%,#0848b9 100%);
            box-shadow:0 18px 42px rgba(2,18,54,.18);
        }
        .hero::after {
            content:"AG"; position:absolute; right:42px; top:18px; font-size:100px; font-weight:950;
            color:rgba(250,204,21,.18); transform:rotate(-8deg);
        }
        .hero-inner { position:relative; z-index:2; }
        .kicker { margin:0; color:#38bdf8; font-size:12px; font-weight:900; text-transform:uppercase; letter-spacing:.18em; }
        .title { margin:8px 0 0; color:white; font-size:34px; font-weight:950; letter-spacing:-.04em; }
        .subtitle { margin:10px 0 0; color:rgba(255,255,255,.74); font-size:14px; font-weight:700; line-height:1.6; }

        .alert { border-radius:16px; padding:14px 16px; font-size:14px; font-weight:850; }
        .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
        .alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

        .card {
            background:white; border:1px solid #dce6f2; border-radius:20px; padding:20px;
            box-shadow:0 10px 24px rgba(15,23,42,.045);
        }
        .head { display:flex; justify-content:space-between; align-items:flex-start; gap:14px; margin-bottom:18px; }
        .card-title { margin:0; color:#0f172a; font-size:22px; font-weight:950; }
        .card-sub { margin:6px 0 0; color:#64748b; font-size:13px; font-weight:700; }

        .filter {
            display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:10px; margin-bottom:18px;
        }
        .input,.select {
            width:100%; height:42px; border-radius:12px; border:1px solid #dce6f2;
            background:white; padding:0 12px; color:#0f172a; font-size:13px; font-weight:800; outline:none;
        }
        .btn {
            min-height:40px; border:0; border-radius:12px; padding:0 14px; font-size:12px; font-weight:950;
            cursor:pointer; text-transform:uppercase; text-decoration:none; display:inline-flex; align-items:center; justify-content:center;
        }
        .btn-blue { background:#2563eb; color:white; }
        .btn-green { background:#16a34a; color:white; }
        .btn-red { background:#dc2626; color:white; }
        .btn-dark { background:#0f172a; color:white; }

        .table-wrap { width:100%; overflow-x:auto; border:1px solid #e7edf6; border-radius:18px; }
        table { width:100%; min-width:960px; border-collapse:collapse; text-align:left; font-size:14px; }
        thead { background:#f8fbff; }
        th {
            padding:14px; color:#64748b; font-size:12px; font-weight:950;
            text-transform:uppercase; letter-spacing:.08em; border-bottom:1px solid #e7edf6; white-space:nowrap;
        }
        td { padding:16px 14px; border-bottom:1px solid #eef2f7; vertical-align:middle; }
        tbody tr:hover { background:#f8fbff; }

        .name { margin:0; color:#0f172a; font-size:14px; font-weight:950; }
        .muted { margin:5px 0 0; color:#64748b; font-size:12px; font-weight:700; }
        .amount { color:#16a34a; font-weight:950; white-space:nowrap; }

        .pill {
            display:inline-flex; border-radius:999px; padding:7px 11px; font-size:11px; font-weight:950;
            text-transform:uppercase; white-space:nowrap;
        }
        .pill-active { background:#dcfce7; color:#15803d; }
        .pill-inactive { background:#fee2e2; color:#dc2626; }

        .actions { display:flex; flex-wrap:wrap; gap:8px; }
        .empty { padding:34px 20px; text-align:center; color:#64748b; font-weight:850; }
        .pagination { margin-top:16px; }

        @media(max-width:1100px) { .filter { grid-template-columns:1fr 1fr; } }
        @media(max-width:700px) { .filter { grid-template-columns:1fr; } .head { flex-direction:column; } }
    </style>

    <div class="page">
        <section class="hero">
            <div class="hero-inner">
                <p class="kicker">Admin Panel</p>
                <h1 class="title">Manage Agents</h1>
                <p class="subtitle">View, filter, activate, deactivate, and monitor agent wallet balances.</p>
            </div>
        </section>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="card">
            <div class="head">
                <div>
                    <h2 class="card-title">Agents List</h2>
                    <p class="card-sub">Filter agents by search, status, and registration date.</p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="btn btn-dark">Back to Dashboard</a>
            </div>

            <form method="GET" action="{{ route('admin.agents.index') }}" class="filter">
                <input class="input" type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, agent code">

                <select class="select" name="status">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>

                <input class="input" type="date" name="date_from" value="{{ request('date_from') }}">
                <input class="input" type="date" name="date_to" value="{{ request('date_to') }}">

                <button class="btn btn-blue">Filter</button>
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Agent Code</th>
                            <th>Players</th>
                            <th>Wallet</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($agents as $agent)
                            <tr>
                                <td>
                                    <p class="name">{{ $agent->name }}</p>
                                    <p class="muted">{{ $agent->email }}</p>
                                </td>

                                <td>
                                    <p class="name">{{ $agent->agent_code ?: 'No Code' }}</p>
                                </td>

                                <td>
                                    <p class="name">{{ $agent->total_players_count ?? $agent->players()->count() }}</p>
                                    <p class="muted">Assigned players</p>
                                </td>

                                <td>
                                    <span class="amount">₱{{ number_format($agent->wallet_balance ?? 0, 2) }}</span>
                                </td>

                                <td>
                                    <span class="pill {{ $agent->is_active ? 'pill-active' : 'pill-inactive' }}">
                                        {{ $agent->statusLabel() }}
                                    </span>
                                </td>

                                <td>
                                    <p class="muted">{{ $agent->created_at?->format('M d, Y h:i A') }}</p>
                                </td>

                                <td>
                                    <div class="actions">
                                        @if($agent->is_active)
                                            <form method="POST" action="{{ route('admin.agents.deactivate', $agent) }}">
                                                @csrf
                                                <button class="btn btn-red">Deactivate</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.agents.activate', $agent) }}">
                                                @csrf
                                                <button class="btn btn-green">Activate</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty">No agents found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $agents->links() }}
            </div>
        </section>
    </div>
</x-layouts.app>