<x-layouts.app :title="__('Admin Money Requests')">
    <style>
        .amr-page { display:flex; flex-direction:column; gap:18px; }

        .amr-hero {
            position:relative;
            overflow:hidden;
            border-radius:22px;
            padding:28px;
            color:white;
            background:linear-gradient(135deg,#03142f 0%,#041a4d 52%,#0848b9 100%);
            box-shadow:0 18px 42px rgba(2,18,54,.18);
        }

        .amr-hero::after {
            content:"₱";
            position:absolute;
            right:48px;
            top:8px;
            font-size:120px;
            font-weight:950;
            color:rgba(250,204,21,.22);
            transform:rotate(-8deg);
        }

        .amr-hero-inner { position:relative; z-index:2; }

        .amr-kicker {
            margin:0;
            color:#38bdf8;
            font-size:12px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.18em;
        }

        .amr-title {
            margin:8px 0 0;
            color:white;
            font-size:34px;
            line-height:1.1;
            font-weight:950;
            letter-spacing:-.04em;
        }

        .amr-subtitle {
            margin:10px 0 0;
            color:rgba(255,255,255,.74);
            font-size:14px;
            font-weight:700;
            line-height:1.6;
        }

        .amr-alert {
            border-radius:16px;
            padding:14px 16px;
            font-size:14px;
            font-weight:850;
        }

        .amr-alert-success {
            background:#dcfce7;
            color:#166534;
            border:1px solid #bbf7d0;
        }

        .amr-alert-error {
            background:#fee2e2;
            color:#991b1b;
            border:1px solid #fecaca;
        }

        .amr-card {
            background:white;
            border:1px solid #dce6f2;
            border-radius:20px;
            padding:20px;
            box-shadow:0 10px 24px rgba(15,23,42,.045);
        }

        .amr-head {
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:14px;
            margin-bottom:18px;
        }

        .amr-card-title {
            margin:0;
            color:#0f172a;
            font-size:22px;
            font-weight:950;
        }

        .amr-card-sub {
            margin:6px 0 0;
            color:#64748b;
            font-size:13px;
            font-weight:700;
            line-height:1.5;
        }

        .amr-filter {
            display:grid;
            grid-template-columns:repeat(5,minmax(0,1fr));
            gap:10px;
            margin-bottom:18px;
        }

        .amr-input,
        .amr-select {
            width:100%;
            height:42px;
            border-radius:12px;
            border:1px solid #dce6f2;
            background:white;
            padding:0 12px;
            color:#0f172a;
            font-size:13px;
            font-weight:800;
            outline:none;
        }

        .amr-btn {
            min-height:42px;
            border:0;
            border-radius:12px;
            padding:0 14px;
            font-size:12px;
            font-weight:950;
            cursor:pointer;
            text-transform:uppercase;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            white-space:nowrap;
        }

        .amr-btn-blue { background:#2563eb; color:white; }
        .amr-btn-green { background:#16a34a; color:white; }
        .amr-btn-red { background:#dc2626; color:white; }
        .amr-btn-dark { background:#0f172a; color:white; }

        .amr-table-wrap {
            width:100%;
            overflow-x:auto;
            border:1px solid #e7edf6;
            border-radius:18px;
        }

        .amr-table {
            width:100%;
            min-width:980px;
            border-collapse:collapse;
            text-align:left;
            font-size:14px;
        }

        .amr-table thead { background:#f8fbff; }

        .amr-table th {
            padding:14px;
            color:#64748b;
            font-size:12px;
            font-weight:950;
            text-transform:uppercase;
            letter-spacing:.08em;
            border-bottom:1px solid #e7edf6;
            white-space:nowrap;
        }

        .amr-table td {
            padding:16px 14px;
            border-bottom:1px solid #eef2f7;
            vertical-align:top;
        }

        .amr-name {
            margin:0;
            color:#0f172a;
            font-size:14px;
            font-weight:950;
        }

        .amr-muted {
            margin:5px 0 0;
            color:#64748b;
            font-size:12px;
            font-weight:700;
        }

        .amr-amount {
            margin:0;
            color:#0f172a;
            font-size:16px;
            font-weight:950;
            white-space:nowrap;
        }

        .amr-status {
            display:inline-flex;
            border-radius:999px;
            padding:7px 11px;
            font-size:11px;
            font-weight:950;
            text-transform:uppercase;
            white-space:nowrap;
        }

        .amr-status.pending { background:#fef3c7; color:#b45309; }
        .amr-status.approved { background:#dcfce7; color:#15803d; }
        .amr-status.rejected { background:#fee2e2; color:#dc2626; }

        .amr-actions {
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            min-width:180px;
        }

        .amr-proof {
            color:#2563eb;
            font-size:12px;
            font-weight:950;
            text-decoration:none;
        }

        .amr-empty {
            padding:30px;
            text-align:center;
            color:#64748b;
            font-weight:850;
        }

        .amr-pagination { margin-top:16px; }

        @media(max-width:1100px) {
            .amr-filter { grid-template-columns:1fr 1fr; }
        }

        @media(max-width:700px) {
            .amr-filter { grid-template-columns:1fr; }
            .amr-head { flex-direction:column; }
        }
    </style>

    <div class="amr-page">
        <section class="amr-hero">
            <div class="amr-hero-inner">
                <p class="amr-kicker">Admin Panel</p>
                <h1 class="amr-title">Money Requests</h1>
                <p class="amr-subtitle">
                    Review agent wallet funding requests, uploaded proof of payment, and agent withdrawals.
                </p>
            </div>
        </section>

        @if(session('success'))
            <div class="amr-alert amr-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="amr-alert amr-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="amr-card">
            <div class="amr-head">
                <div>
                    <h2 class="amr-card-title">Agent Money Requests</h2>
                    <p class="amr-card-sub">
                        Approving a request will credit the agent wallet balance.
                    </p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="amr-btn amr-btn-dark">
                    Back to Dashboard
                </a>
            </div>

            <form method="GET" action="{{ route('admin.money-requests.index') }}" class="amr-filter">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search agent"
                    class="amr-input"
                >

                <select name="status" class="amr-select">
                    <option value="">All Status</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>

                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="amr-input"
                >

                <input
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="amr-input"
                >

                <button class="amr-btn amr-btn-blue">
                    Filter
                </button>
            </form>

            <div class="amr-table-wrap">
                <table class="amr-table">
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Proof</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($agentMoneyRequests as $requestMoney)
                            @php
                                $status = strtolower($requestMoney->status ?? 'pending');
                            @endphp

                            <tr>
                                <td>
                                    <p class="amr-name">{{ $requestMoney->user?->name }}</p>
                                    <p class="amr-muted">{{ $requestMoney->user?->email }}</p>
                                    <p class="amr-muted">Code: {{ $requestMoney->user?->agent_code ?? 'N/A' }}</p>
                                </td>

                                <td>
                                    <p class="amr-amount">
                                        ₱{{ number_format($requestMoney->amount, 2) }}
                                    </p>
                                </td>

                                <td>
                                    <p class="amr-name">
                                        {{ $requestMoney->payment_method ?: 'N/A' }}
                                    </p>

                                    <p class="amr-muted">
                                        Ref: {{ $requestMoney->reference_number ?: 'N/A' }}
                                    </p>

                                    @if($requestMoney->notes)
                                        <p class="amr-muted">
                                            Notes: {{ $requestMoney->notes }}
                                        </p>
                                    @endif
                                </td>

                                <td>
                                    @if($requestMoney->proof_image_path)
                                        <a
                                            href="{{ asset('storage/' . $requestMoney->proof_image_path) }}"
                                            target="_blank"
                                            class="amr-proof"
                                        >
                                            View Proof
                                        </a>
                                    @else
                                        <span class="amr-muted">No proof</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="amr-status {{ $status }}">
                                        {{ $requestMoney->status }}
                                    </span>
                                </td>

                                <td>
                                    <p class="amr-muted">
                                        {{ $requestMoney->created_at->format('M d, Y h:i A') }}
                                    </p>
                                </td>

                                <td>
                                    @if($requestMoney->status === 'pending')
                                        <div class="amr-actions">
                                            <form method="POST" action="{{ route('admin.money-requests.approve', $requestMoney) }}">
                                                @csrf
                                                <button class="amr-btn amr-btn-green">
                                                    Approve
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.money-requests.reject', $requestMoney) }}">
                                                @csrf
                                                <button class="amr-btn amr-btn-red">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="amr-muted">Reviewed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="amr-empty">
                                        No agent money requests yet.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="amr-pagination">
                {{ $agentMoneyRequests->links() }}
            </div>
        </section>

        <section class="amr-card">
            <div class="amr-head">
                <div>
                    <h2 class="amr-card-title">Agent Withdrawal Requests</h2>
                    <p class="amr-card-sub">
                        Approving a withdrawal will deduct from the agent wallet balance.
                    </p>
                </div>
            </div>

            <div class="amr-table-wrap">
                <table class="amr-table">
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Amount</th>
                            <th>Payment Details</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($agentWithdrawals as $withdrawal)
                            @php
                                $status = strtolower($withdrawal->status ?? 'pending');
                            @endphp

                            <tr>
                                <td>
                                    <p class="amr-name">{{ $withdrawal->user?->name }}</p>
                                    <p class="amr-muted">{{ $withdrawal->user?->email }}</p>
                                </td>

                                <td>
                                    <p class="amr-amount">
                                        ₱{{ number_format($withdrawal->amount, 2) }}
                                    </p>
                                </td>

                                <td>
                                    <p class="amr-name">{{ $withdrawal->payment_method }}</p>
                                    <p class="amr-muted">{{ $withdrawal->account_name }}</p>
                                    <p class="amr-muted">{{ $withdrawal->account_number }}</p>
                                </td>

                                <td>
                                    <span class="amr-status {{ $status }}">
                                        {{ $withdrawal->status }}
                                    </span>
                                </td>

                                <td>
                                    <p class="amr-muted">
                                        {{ $withdrawal->created_at->format('M d, Y h:i A') }}
                                    </p>
                                </td>

                                <td>
                                    @if($withdrawal->status === 'pending')
                                        <div class="amr-actions">
                                            <form method="POST" action="{{ route('admin.withdrawals.approve', $withdrawal) }}">
                                                @csrf
                                                <button class="amr-btn amr-btn-green">
                                                    Approve
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.withdrawals.reject', $withdrawal) }}">
                                                @csrf
                                                <button class="amr-btn amr-btn-red">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="amr-muted">Reviewed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="amr-empty">
                                        No agent withdrawal requests yet.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="amr-pagination">
                {{ $agentWithdrawals->links() }}
            </div>
        </section>
    </div>
</x-layouts.app>