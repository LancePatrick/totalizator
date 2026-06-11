<x-layouts.app :title="__('Manage Players')">
    <style>
        .page { display:flex; flex-direction:column; gap:18px; }
        .hero {
            position:relative; overflow:hidden; border-radius:22px; padding:28px; color:white;
            background:linear-gradient(135deg,#03142f 0%,#041a4d 52%,#0848b9 100%);
            box-shadow:0 18px 42px rgba(2,18,54,.18);
        }
        .hero::after {
            content:"PL"; position:absolute; right:42px; top:18px; font-size:100px; font-weight:950;
            color:rgba(56,189,248,.20); transform:rotate(-8deg);
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
            display:grid; grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr 1fr auto; gap:10px; margin-bottom:18px;
        }
        .input,.select {
            width:100%; height:42px; border-radius:12px; border:1px solid #dce6f2;
            background:white; padding:0 12px; color:#0f172a; font-size:13px; font-weight:800; outline:none;
        }

        .textarea {
            width:100%;
            border-radius:12px;
            border:1px solid #dce6f2;
            background:white;
            padding:10px 12px;
            color:#0f172a;
            font-size:12px;
            font-weight:800;
            outline:none;
            resize:vertical;
        }

        .btn {
            min-height:40px; border:0; border-radius:12px; padding:0 14px; font-size:12px; font-weight:950;
            cursor:pointer; text-transform:uppercase; text-decoration:none; display:inline-flex; align-items:center; justify-content:center;
            gap:8px;
        }
        .btn-blue { background:#2563eb; color:white; }
        .btn-green { background:#16a34a; color:white; }
        .btn-red { background:#dc2626; color:white; }
        .btn-dark { background:#0f172a; color:white; }
        .btn-amber { background:#f59e0b; color:#111827; }
        .btn-soft { background:#e2e8f0; color:#0f172a; }
        .btn-outline { background:white; color:#0f172a; border:1px solid #dce6f2; }

        .table-wrap { width:100%; overflow-x:auto; border:1px solid #e7edf6; border-radius:18px; }
        table { width:100%; min-width:1080px; border-collapse:collapse; text-align:left; font-size:14px; }
        thead { background:#f8fbff; }
        th {
            padding:14px; color:#64748b; font-size:12px; font-weight:950;
            text-transform:uppercase; letter-spacing:.08em; border-bottom:1px solid #e7edf6; white-space:nowrap;
        }
        td { padding:16px 14px; border-bottom:1px solid #eef2f7; vertical-align:top; }
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
        .pill-approved { background:#dcfce7; color:#15803d; }
        .pill-rejected { background:#fee2e2; color:#dc2626; }
        .pill-pending { background:#fef3c7; color:#b45309; }
        .pill-default { background:#f1f5f9; color:#475569; }

        .actions {
            display:flex;
            align-items:center;
            gap:8px;
            min-width:130px;
            max-width:160px;
        }

        .empty { padding:34px 20px; text-align:center; color:#64748b; font-weight:850; }
        .pagination { margin-top:16px; }

        .modal-backdrop {
            position:fixed;
            inset:0;
            z-index:9999;
            display:none;
            align-items:center;
            justify-content:center;
            padding:20px;
            background:rgba(15,23,42,.62);
            backdrop-filter:blur(6px);
        }

        .modal-backdrop.is-open { display:flex; }

        .modal-card {
            width:100%;
            max-width:620px;
            max-height:90vh;
            overflow-y:auto;
            border-radius:24px;
            background:#ffffff;
            border:1px solid #dce6f2;
            box-shadow:0 30px 80px rgba(2,6,23,.35);
        }

        .modal-head {
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:16px;
            padding:20px 22px;
            border-bottom:1px solid #e7edf6;
        }

        .modal-title {
            margin:0;
            color:#0f172a;
            font-size:20px;
            font-weight:950;
            letter-spacing:-.03em;
        }

        .modal-sub {
            margin:5px 0 0;
            color:#64748b;
            font-size:13px;
            font-weight:700;
            line-height:1.5;
        }

        .modal-close {
            width:38px;
            height:38px;
            border:0;
            border-radius:12px;
            background:#f1f5f9;
            color:#0f172a;
            cursor:pointer;
            font-size:20px;
            font-weight:950;
        }

        .modal-body { padding:22px; }

        .modal-section {
            border:1px solid #e7edf6;
            border-radius:18px;
            padding:16px;
            background:#f8fbff;
            margin-bottom:14px;
        }

        .modal-section-danger {
            border-color:#fecaca;
            background:#fff7f7;
        }

        .modal-section-appeal {
            border-color:#fde68a;
            background:#fffbeb;
        }

        .modal-section-password {
            border-color:#fed7aa;
            background:#fff7ed;
        }

        .section-title {
            margin:0;
            color:#0f172a;
            font-size:12px;
            font-weight:950;
            text-transform:uppercase;
            letter-spacing:.08em;
        }

        .section-text {
            margin:8px 0 0;
            color:#475569;
            font-size:13px;
            font-weight:700;
            line-height:1.6;
            word-break:break-word;
        }

        .proof-link {
            display:inline-flex;
            margin-top:10px;
            color:#2563eb;
            font-size:13px;
            font-weight:950;
            text-decoration:underline;
        }

        .modal-actions {
            display:grid;
            gap:10px;
            margin-top:14px;
        }

        .modal-grid {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:12px;
        }

        @media(max-width:1300px) { .filter { grid-template-columns:1fr 1fr 1fr; } }
        @media(max-width:700px) {
            .filter { grid-template-columns:1fr; }
            .head { flex-direction:column; }
            .modal-card { max-height:92vh; }
            .modal-grid { grid-template-columns:1fr; }
            table { min-width:1000px; }
        }
    </style>

    <div class="page">
        <section class="hero">
            <div class="hero-inner">
                <p class="kicker">Admin Panel</p>
                <h1 class="title">Manage Players</h1>
                <p class="subtitle">View, filter, activate, deactivate, monitor player balances, review appeals, and manage assigned agents.</p>
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
                    <h2 class="card-title">Players List</h2>
                    <p class="card-sub">Filter players by agent, status, KYC, appeal status, and registration date.</p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="btn btn-dark">Back to Dashboard</a>
            </div>

            <form method="GET" action="{{ route('admin.players.index') }}" class="filter">
                <input class="input" type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone, location">

                <select class="select" name="agent_id">
                    <option value="">All Agents</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" @selected((string) request('agent_id') === (string) $agent->id)>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>

                <select class="select" name="status">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>

                <select class="select" name="kyc_status">
                    <option value="">All KYC</option>
                    <option value="not_submitted" @selected(request('kyc_status') === 'not_submitted')>Not Submitted</option>
                    <option value="pending" @selected(request('kyc_status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('kyc_status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('kyc_status') === 'rejected')>Rejected</option>
                </select>

                <select class="select" name="appeal_status">
                    <option value="">All Appeals</option>
                    <option value="pending" @selected(request('appeal_status') === 'pending')>Pending Appeal</option>
                    <option value="approved" @selected(request('appeal_status') === 'approved')>Approved Appeal</option>
                    <option value="rejected" @selected(request('appeal_status') === 'rejected')>Rejected Appeal</option>
                </select>

                <input class="input" type="date" name="date_from" value="{{ request('date_from') }}">
                <input class="input" type="date" name="date_to" value="{{ request('date_to') }}">

                <button class="btn btn-blue">Filter</button>
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Agent</th>
                            <th>Wallet</th>
                            <th>Status</th>
                            <th>KYC</th>
                            <th>Location</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($players as $player)
                            @php
                                $kycClass = match ($player->kyc_status) {
                                    'approved' => 'pill-approved',
                                    'pending' => 'pill-pending',
                                    'rejected' => 'pill-rejected',
                                    default => 'pill-default',
                                };

                                $appealClass = match ($player->appeal_status) {
                                    'approved' => 'pill-approved',
                                    'pending' => 'pill-pending',
                                    'rejected' => 'pill-rejected',
                                    default => 'pill-default',
                                };

                                $statusLabel = method_exists($player, 'statusLabel')
                                    ? $player->statusLabel()
                                    : ($player->is_active ? 'Active' : 'Inactive');

                                $kycLabel = method_exists($player, 'kycLabel')
                                    ? $player->kycLabel()
                                    : ucfirst(str_replace('_', ' ', $player->kyc_status ?? 'not submitted'));

                                $appealMessage = $player->appeal_message ?? $player->appeal_reason ?? null;
                                $appealProof = $player->appeal_proof ?? $player->appeal_image ?? null;
                            @endphp

                            <tr>
                                <td>
                                    <p class="name">{{ $player->name }}</p>
                                    <p class="muted">{{ $player->email }}</p>

                                    @if($player->phone)
                                        <p class="muted">{{ $player->phone }}</p>
                                    @endif
                                </td>

                                <td>
                                    <p class="name">{{ $player->agent?->name ?? 'No Agent' }}</p>
                                    <p class="muted">{{ $player->agent?->agent_code ?? 'N/A' }}</p>
                                </td>

                                <td>
                                    <span class="amount">₱{{ number_format($player->wallet_balance ?? 0, 2) }}</span>
                                </td>

                                <td>
                                    <span class="pill {{ $player->is_active ? 'pill-active' : 'pill-inactive' }}">
                                        {{ $statusLabel }}
                                    </span>

                                    @if(!$player->is_active && $player->deactivated_at)
                                        <p class="muted">
                                            Deactivated: {{ $player->deactivated_at?->format('M d, Y h:i A') }}
                                        </p>
                                    @endif
                                </td>

                                <td>
                                    <span class="pill {{ $kycClass }}">
                                        {{ $kycLabel }}
                                    </span>
                                </td>

                                <td>
                                    <p class="muted">{{ $player->location ?: 'N/A' }}</p>
                                </td>

                                <td>
                                    <p class="muted">{{ $player->created_at?->format('M d, Y h:i A') }}</p>
                                </td>

                                <td>
                                    <div class="actions">
                                        <button
                                            type="button"
                                            class="btn btn-dark"
                                            style="width:100%;"
                                            data-open-modal="action-modal-{{ $player->id }}"
                                        >
                                            View Action
                                        </button>
                                    </div>

                                    <div class="modal-backdrop" id="action-modal-{{ $player->id }}">
                                        <div class="modal-card">
                                            <div class="modal-head">
                                                <div>
                                                    <h3 class="modal-title">Player Actions</h3>
                                                    <p class="modal-sub">
                                                        {{ $player->name }} — {{ $player->email }}
                                                    </p>
                                                </div>

                                                <button type="button" class="modal-close" data-close-modal>&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="modal-grid">
                                                    <div class="modal-section">
                                                        <p class="section-title">Account Status</p>
                                                        <p style="margin:10px 0 0;">
                                                            <span class="pill {{ $player->is_active ? 'pill-active' : 'pill-inactive' }}">
                                                                {{ $statusLabel }}
                                                            </span>
                                                        </p>

                                                        @if(!$player->is_active && $player->deactivated_at)
                                                            <p class="section-text">
                                                                Deactivated: {{ $player->deactivated_at?->format('M d, Y h:i A') }}
                                                            </p>
                                                        @endif
                                                    </div>

                                                    <div class="modal-section">
                                                        <p class="section-title">KYC Status</p>
                                                        <p style="margin:10px 0 0;">
                                                            <span class="pill {{ $kycClass }}">
                                                                {{ $kycLabel }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="modal-section modal-section-password">
                                                    <p class="section-title" style="color:#92400e;">
                                                        Change Player Password
                                                    </p>

                                                    <p class="section-text">
                                                        Set a new password for this player account.
                                                    </p>

                                                    <form method="POST" action="{{ route('admin.players.password.update', $player) }}" style="margin-top:12px;">
                                                        @csrf

                                                        <div class="modal-grid">
                                                            <input
                                                                type="password"
                                                                name="password"
                                                                required
                                                                minlength="8"
                                                                class="input"
                                                                placeholder="New password"
                                                            >

                                                            <input
                                                                type="password"
                                                                name="password_confirmation"
                                                                required
                                                                minlength="8"
                                                                class="input"
                                                                placeholder="Confirm password"
                                                            >
                                                        </div>

                                                        <button
                                                            type="submit"
                                                            class="btn btn-amber"
                                                            style="width:100%; margin-top:10px;"
                                                            onclick="return confirm('Change this player password?')"
                                                        >
                                                            Change Password
                                                        </button>
                                                    </form>
                                                </div>

                                                <div class="modal-section {{ $player->is_active ? '' : 'modal-section-danger' }}">
                                                    <p class="section-title" style="{{ !$player->is_active ? 'color:#991b1b;' : '' }}">
                                                        Activate / Deactivate
                                                    </p>

                                                    @if($player->is_active)
                                                        <p class="section-text">
                                                            Enter a reason before deactivating this player.
                                                        </p>

                                                        <form method="POST" action="{{ route('admin.players.deactivate', $player) }}" style="margin-top:12px;">
                                                            @csrf

                                                            <textarea
                                                                name="deactivation_reason"
                                                                rows="4"
                                                                required
                                                                class="textarea"
                                                                style="border-color:#fecaca;"
                                                                placeholder="Enter deactivate reason..."
                                                            >{{ old('deactivation_reason') }}</textarea>

                                                            <button
                                                                type="submit"
                                                                class="btn btn-red"
                                                                style="width:100%; margin-top:10px;"
                                                                onclick="return confirm('Deactivate this player?')"
                                                            >
                                                                Confirm Deactivate
                                                            </button>
                                                        </form>
                                                    @else
                                                        <p class="section-text" style="color:#7f1d1d;">
                                                            <strong>Deactivation Reason:</strong>
                                                            <br>
                                                            {{ $player->deactivation_reason ?: 'No reason provided.' }}
                                                        </p>

                                                        <form method="POST" action="{{ route('admin.players.activate', $player) }}" style="margin-top:12px;">
                                                            @csrf

                                                            <button
                                                                class="btn btn-green"
                                                                style="width:100%;"
                                                                onclick="return confirm('Activate this player?')"
                                                            >
                                                                Activate Player
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>

                                                <div class="modal-section modal-section-appeal">
                                                    <p class="section-title" style="color:#92400e;">
                                                        Appeal Details
                                                    </p>

                                                    @if($player->appeal_status)
                                                        <p style="margin:10px 0 0;">
                                                            <span class="pill {{ $appealClass }}">
                                                                {{ $player->appeal_status }}
                                                            </span>
                                                        </p>

                                                        @if($player->appeal_submitted_at)
                                                            <p class="section-text">
                                                                Submitted: {{ $player->appeal_submitted_at?->format('M d, Y h:i A') }}
                                                            </p>
                                                        @endif

                                                        <div style="margin-top:14px;">
                                                            <p class="section-title">Appeal Message</p>

                                                            <p class="section-text">
                                                                {{ $appealMessage ?: 'No appeal message submitted.' }}
                                                            </p>
                                                        </div>

                                                        @if($appealProof)
                                                            <a
                                                                href="{{ asset('storage/' . $appealProof) }}"
                                                                target="_blank"
                                                                class="proof-link"
                                                            >
                                                                View Uploaded Proof / Image
                                                            </a>
                                                        @else
                                                            <p class="section-text">
                                                                No proof/image uploaded.
                                                            </p>
                                                        @endif

                                                        @if($player->appeal_admin_note)
                                                            <div style="margin-top:14px;">
                                                                <p class="section-title">Admin Note</p>
                                                                <p class="section-text">{{ $player->appeal_admin_note }}</p>
                                                            </div>
                                                        @endif

                                                        @if($player->appeal_status === 'pending')
                                                            <div class="modal-actions">
                                                                <form method="POST" action="{{ route('admin.players.appeal.approve', $player) }}">
                                                                    @csrf

                                                                    <button
                                                                        class="btn btn-green"
                                                                        style="width:100%;"
                                                                        onclick="return confirm('Approve this appeal and activate this player?')"
                                                                    >
                                                                        Approve Appeal
                                                                    </button>
                                                                </form>

                                                                <form method="POST" action="{{ route('admin.players.appeal.reject', $player) }}">
                                                                    @csrf

                                                                    <input
                                                                        type="text"
                                                                        name="appeal_admin_note"
                                                                        placeholder="Reject reason / admin note"
                                                                        class="input"
                                                                        style="border-color:#fecaca;"
                                                                    >

                                                                    <button
                                                                        class="btn btn-red"
                                                                        style="width:100%; margin-top:8px;"
                                                                        onclick="return confirm('Reject this appeal?')"
                                                                    >
                                                                        Reject Appeal
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <p class="section-text">
                                                            No appeal submitted yet.
                                                        </p>
                                                    @endif
                                                </div>

                                                <button type="button" class="btn btn-soft" style="width:100%;" data-close-modal>
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty">No players found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $players->links() }}
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openButtons = document.querySelectorAll('[data-open-modal]');
            const closeButtons = document.querySelectorAll('[data-close-modal]');
            const modals = document.querySelectorAll('.modal-backdrop');

            function closeAllModals() {
                modals.forEach(function (modal) {
                    modal.classList.remove('is-open');
                });

                document.body.style.overflow = '';
            }

            openButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const modalId = button.getAttribute('data-open-modal');
                    const modal = document.getElementById(modalId);

                    if (!modal) {
                        return;
                    }

                    closeAllModals();

                    modal.classList.add('is-open');
                    document.body.style.overflow = 'hidden';
                });
            });

            closeButtons.forEach(function (button) {
                button.addEventListener('click', closeAllModals);
            });

            modals.forEach(function (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeAllModals();
                    }
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeAllModals();
                }
            });
        });
    </script>
</x-layouts.app>