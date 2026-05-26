<x-layouts.app :title="__('Agent Wallet')">
    @php
        $user = auth()->user();
    @endphp

    <style>
        .aw-page { display:flex; flex-direction:column; gap:18px; }
        .aw-hero {
            position:relative; overflow:hidden; border-radius:22px; padding:28px; color:white;
            background:linear-gradient(135deg,#03142f 0%,#041a4d 52%,#0848b9 100%);
            box-shadow:0 18px 42px rgba(2,18,54,.18);
        }
        .aw-hero::after {
            content:"💰"; position:absolute; right:42px; top:12px; font-size:112px;
            transform:rotate(-10deg); filter:drop-shadow(0 18px 24px rgba(0,0,0,.30));
        }
        .aw-hero-inner {
            position:relative; z-index:2; display:grid; grid-template-columns:minmax(0,1fr) 300px; gap:24px; align-items:center;
        }
        .aw-kicker { margin:0; color:#38bdf8; font-size:12px; font-weight:900; text-transform:uppercase; letter-spacing:.18em; }
        .aw-title { margin:8px 0 0; color:white; font-size:34px; line-height:1.1; font-weight:950; letter-spacing:-.04em; }
        .aw-subtitle { margin:10px 0 0; color:rgba(255,255,255,.74); font-size:14px; font-weight:700; line-height:1.6; }
        .aw-balance { border-radius:18px; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15); padding:18px; }
        .aw-balance-label { margin:0; color:rgba(255,255,255,.65); font-size:12px; font-weight:900; text-transform:uppercase; letter-spacing:.12em; }
        .aw-balance-value { margin:8px 0 0; color:#facc15; font-size:34px; font-weight:950; }

        .aw-alert { border-radius:16px; padding:14px 16px; font-size:14px; font-weight:850; }
        .aw-alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
        .aw-alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

        .aw-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; align-items:start; }
        .aw-card { background:white; border:1px solid #dce6f2; border-radius:20px; padding:20px; box-shadow:0 10px 24px rgba(15,23,42,.045); }
        .aw-card-title { margin:0; color:#0f172a; font-size:22px; font-weight:950; }
        .aw-card-sub { margin:6px 0 0; color:#64748b; font-size:13px; font-weight:700; line-height:1.5; }
        .aw-form { margin-top:18px; display:grid; gap:12px; }
        .aw-input,.aw-textarea {
            width:100%; border-radius:14px; border:1px solid #dce6f2; background:white;
            padding:0 14px; outline:none; color:#0f172a; font-size:14px; font-weight:800;
        }
        .aw-input { height:48px; }
        .aw-textarea { min-height:96px; padding-top:12px; resize:vertical; }
        .aw-file {
            width:100%; border-radius:14px; border:1px dashed #93c5fd; background:#eff6ff;
            padding:12px; color:#1d4ed8; font-size:13px; font-weight:800;
        }
        .aw-btn { height:48px; border:0; border-radius:14px; color:white; font-size:14px; font-weight:950; cursor:pointer; }
        .aw-btn-blue { background:#2563eb; }
        .aw-btn-purple { background:#7c3aed; }

        .aw-history { display:grid; gap:10px; margin-top:14px; }
        .aw-item { border:1px solid #e7edf6; border-radius:14px; padding:14px; display:flex; justify-content:space-between; gap:12px; }
        .aw-amount { margin:0; color:#0f172a; font-size:16px; font-weight:950; }
        .aw-meta { margin:5px 0 0; color:#64748b; font-size:12px; font-weight:700; }
        .aw-status { border-radius:999px; padding:7px 10px; font-size:11px; font-weight:950; text-transform:uppercase; background:#f1f5f9; color:#334155; }
        .aw-status.pending { background:#fef3c7; color:#b45309; }
        .aw-status.approved { background:#dcfce7; color:#15803d; }
        .aw-status.rejected { background:#fee2e2; color:#dc2626; }
        .aw-empty { border-radius:16px; padding:22px; background:#f8fafc; border:1px dashed #cbd5e1; text-align:center; color:#64748b; font-weight:800; }

        @media(max-width:1000px) {
            .aw-hero-inner,.aw-grid { grid-template-columns:1fr; }
            .aw-hero::after { display:none; }
        }
    </style>

    <div class="aw-page">
        <section class="aw-hero">
            <div class="aw-hero-inner">
                <div>
                    <p class="aw-kicker">Agent Wallet</p>
                    <h1 class="aw-title">Request Funds from Admin</h1>
                    <p class="aw-subtitle">
                        Upload proof of payment and wait for admin approval.
                    </p>
                </div>

                <div class="aw-balance">
                    <p class="aw-balance-label">Agent Balance</p>
                    <h2 class="aw-balance-value">₱{{ number_format($user->wallet_balance ?? 0, 2) }}</h2>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="aw-alert aw-alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="aw-alert aw-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="aw-grid">
            <div class="aw-card">
                <h2 class="aw-card-title">Request Money</h2>
                <p class="aw-card-sub">Request agent wallet balance from admin.</p>

                <form method="POST" action="{{ route('agent.wallet.request-money') }}" enctype="multipart/form-data" class="aw-form">
                    @csrf

                    <input type="number" name="amount" min="1" step="0.01" required placeholder="Amount" class="aw-input">
                    <input name="payment_method" placeholder="Payment Method e.g. GCash / Bank" class="aw-input">
                    <input name="reference_number" placeholder="Reference Number" class="aw-input">
                    <input type="file" name="proof_image" accept="image/*" class="aw-file">
                    <textarea name="notes" placeholder="Notes optional" class="aw-textarea"></textarea>

                    <button class="aw-btn aw-btn-blue">
                        Submit Request
                    </button>
                </form>
            </div>

            <div class="aw-card">
                <h2 class="aw-card-title">Withdraw Agent Balance</h2>
                <p class="aw-card-sub">Submit withdrawal request to admin.</p>

                <form method="POST" action="{{ route('agent.wallet.withdraw') }}" class="aw-form">
                    @csrf

                    <input type="number" name="amount" min="1" step="0.01" required placeholder="Amount" class="aw-input">
                    <input name="payment_method" required placeholder="Payment Method e.g. GCash" class="aw-input">
                    <input name="account_name" required placeholder="Account Name" class="aw-input">
                    <input name="account_number" required placeholder="Account Number" class="aw-input">
                    <textarea name="notes" placeholder="Notes optional" class="aw-textarea"></textarea>

                    <button class="aw-btn aw-btn-purple">
                        Submit Withdrawal
                    </button>
                </form>
            </div>
        </section>

        <section class="aw-grid">
            <div class="aw-card">
                <h2 class="aw-card-title">Money Request History</h2>

                <div class="aw-history">
                    @forelse($moneyRequests as $requestMoney)
                        @php $status = strtolower($requestMoney->status ?? 'pending'); @endphp

                        <div class="aw-item">
                            <div>
                                <p class="aw-amount">₱{{ number_format($requestMoney->amount, 2) }}</p>
                                <p class="aw-meta">{{ $requestMoney->created_at->format('M d, Y h:i A') }}</p>
                                @if($requestMoney->reference_number)
                                    <p class="aw-meta">Ref: {{ $requestMoney->reference_number }}</p>
                                @endif
                            </div>

                            <span class="aw-status {{ $status }}">{{ $requestMoney->status }}</span>
                        </div>
                    @empty
                        <div class="aw-empty">No money requests yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="aw-card">
                <h2 class="aw-card-title">Withdrawal History</h2>

                <div class="aw-history">
                    @forelse($withdrawals as $withdrawal)
                        @php $status = strtolower($withdrawal->status ?? 'pending'); @endphp

                        <div class="aw-item">
                            <div>
                                <p class="aw-amount">₱{{ number_format($withdrawal->amount, 2) }}</p>
                                <p class="aw-meta">{{ $withdrawal->payment_method }} - {{ $withdrawal->account_number }}</p>
                                <p class="aw-meta">{{ $withdrawal->created_at->format('M d, Y h:i A') }}</p>
                            </div>

                            <span class="aw-status {{ $status }}">{{ $withdrawal->status }}</span>
                        </div>
                    @empty
                        <div class="aw-empty">No withdrawals yet.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>