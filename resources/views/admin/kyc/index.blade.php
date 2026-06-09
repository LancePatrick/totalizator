<x-layouts.app :title="__('Admin KYC Review')">
    <style>
        .akyc-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .akyc-hero {
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

        .akyc-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 54px 54px;
            opacity: .45;
        }

        .akyc-hero::after {
            content: "🛡️";
            position: absolute;
            right: 46px;
            top: 14px;
            font-size: 112px;
            transform: rotate(-8deg);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.30));
        }

        .akyc-hero-inner {
            position: relative;
            z-index: 2;
            max-width: 860px;
        }

        .akyc-kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .akyc-title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .akyc-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.74);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
        }

        .akyc-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 850;
        }

        .akyc-alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .akyc-alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .akyc-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .akyc-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .akyc-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .akyc-card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .akyc-back-btn {
            min-height: 42px;
            border-radius: 12px;
            background: #2563eb;
            color: white;
            padding: 0 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 950;
            box-shadow: 0 12px 24px rgba(37,99,235,.18);
            text-decoration: none;
            white-space: nowrap;
        }

        .akyc-table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e7edf6;
            border-radius: 18px;
        }

        .akyc-table {
            width: 100%;
            min-width: 980px;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .akyc-table thead {
            background: #f8fbff;
        }

        .akyc-table th {
            padding: 14px 14px;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
            border-bottom: 1px solid #e7edf6;
            white-space: nowrap;
        }

        .akyc-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: top;
        }

        .akyc-table tbody tr:hover {
            background: #f8fbff;
        }

        .akyc-player-name {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
        }

        .akyc-muted {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .akyc-strong {
            margin: 0;
            color: #334155;
            font-size: 14px;
            font-weight: 850;
        }

        .akyc-pill {
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

        .akyc-pill.approved {
            background: #dcfce7;
            color: #15803d;
        }

        .akyc-pill.pending {
            background: #fef3c7;
            color: #b45309;
        }

        .akyc-pill.rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .akyc-pill.default {
            background: #f1f5f9;
            color: #475569;
        }

        .akyc-file-list {
            display: grid;
            gap: 8px;
        }

        .akyc-file-btn {
            min-height: 36px;
            border-radius: 11px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 950;
            text-decoration: none;
            white-space: nowrap;
        }

        .akyc-file-id {
            background: #eff6ff;
            color: #2563eb;
        }

        .akyc-file-selfie {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .akyc-no-file {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 800;
        }

        .akyc-action-stack {
            display: grid;
            gap: 8px;
            min-width: 150px;
        }

        .akyc-btn {
            width: 100%;
            min-height: 38px;
            border: 0;
            border-radius: 12px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 950;
            cursor: pointer;
            text-transform: uppercase;
            transition: .16s ease;
        }

        .akyc-btn:hover {
            transform: translateY(-1px);
            filter: brightness(.96);
        }

        .akyc-btn-approve {
            background: #16a34a;
            color: white;
        }

        .akyc-btn-reject {
            background: #dc2626;
            color: white;
        }

        .akyc-reject-form {
            display: grid;
            gap: 8px;
        }

        .akyc-input {
            width: 100%;
            height: 38px;
            border-radius: 12px;
            border: 1px solid #dce6f2;
            background: white;
            padding: 0 12px;
            outline: none;
            color: #0f172a;
            font-size: 12px;
            font-weight: 800;
        }

        .akyc-input:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239,68,68,.12);
        }

        .akyc-reviewed {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 850;
        }

        .akyc-empty {
            padding: 36px 20px;
            text-align: center;
        }

        .akyc-empty-title {
            margin: 0;
            color: #334155;
            font-size: 18px;
            font-weight: 950;
        }

        .akyc-empty-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .akyc-pagination {
            margin-top: 16px;
        }

        @media (max-width: 900px) {
            .akyc-hero::after {
                display: none;
            }

            .akyc-head {
                flex-direction: column;
            }

            .akyc-back-btn {
                width: 100%;
            }

            .akyc-title {
                font-size: 28px;
            }
        }
    </style>

    <div class="akyc-page">
        <section class="akyc-hero">
            <div class="akyc-hero-inner">
                <p class="akyc-kicker">Admin KYC Review</p>

                <h1 class="akyc-title">
                    KYC Requests
                </h1>

                <p class="akyc-subtitle">
                    Review, approve, or reject player verification submissions.
                </p>
            </div>
        </section>

        @if(session('success'))
            <div class="akyc-alert akyc-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="akyc-alert akyc-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="akyc-card">
            <div class="akyc-head">
                <div>
                    <h2 class="akyc-card-title">
                        Submitted KYC
                    </h2>

                    <p class="akyc-card-sub">
                        All player KYC submissions are listed below.
                    </p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="akyc-back-btn">
                    Back to Dashboard
                </a>
            </div>

            <div class="akyc-table-wrap">
                <table class="akyc-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Full Name</th>
                            <th>Birthdate</th>
                            <th>ID Details</th>
                            <th>Status</th>
                            <th>Files</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($kycUsers as $player)
                            @php
                                $status = strtolower($player->kyc_status ?? 'default');

                                if (!in_array($status, ['approved', 'pending', 'rejected'])) {
                                    $status = 'default';
                                }

                                $statusLabel = match ($status) {
                                    'approved' => 'Approved',
                                    'pending' => 'Pending',
                                    'rejected' => 'Rejected',
                                    default => 'Not Submitted',
                                };
                            @endphp

                            <tr>
                                <td>
                                    <p class="akyc-player-name">
                                        {{ $player->name }}
                                    </p>

                                    <p class="akyc-muted">
                                        {{ $player->email }}
                                    </p>

                                    <p class="akyc-muted">
                                        Submitted:
                                        {{ $player->kyc_submitted_at ? $player->kyc_submitted_at->format('M d, Y h:i A') : 'N/A' }}
                                    </p>
                                </td>

                                <td>
                                    <p class="akyc-strong">
                                        {{ $player->kyc_full_name ?? 'N/A' }}
                                    </p>

                                    <p class="akyc-muted">
                                        {{ $player->kyc_address ?? 'No address' }}
                                    </p>
                                </td>

                                <td>
                                    <p class="akyc-strong">
                                        {{ $player->kyc_birthdate ? $player->kyc_birthdate->format('M d, Y') : 'N/A' }}
                                    </p>
                                </td>

                                <td>
                                    <p class="akyc-strong">
                                        {{ $player->kyc_valid_id_type ?? 'N/A' }}
                                    </p>

                                    <p class="akyc-muted">
                                        {{ $player->kyc_valid_id_number ?? 'N/A' }}
                                    </p>
                                </td>

                                <td>
                                    <span class="akyc-pill {{ $status }}">
                                        {{ $statusLabel }}
                                    </span>

                                    @if($status === 'rejected' && $player->kyc_rejection_reason)
                                        <p class="akyc-muted" style="color:#dc2626;">
                                            {{ $player->kyc_rejection_reason }}
                                        </p>
                                    @endif
                                </td>

                                <td>
                                    <div class="akyc-file-list">
                                        @if($player->kyc_valid_id_image)
                                            <a
                                                href="{{ asset('storage/' . $player->kyc_valid_id_image) }}"
                                                target="_blank"
                                                class="akyc-file-btn akyc-file-id"
                                            >
                                                View ID
                                            </a>
                                        @else
                                            <div class="akyc-no-file">
                                                No ID image
                                            </div>
                                        @endif

                                        @if($player->kyc_selfie_image)
                                            <a
                                                href="{{ asset('storage/' . $player->kyc_selfie_image) }}"
                                                target="_blank"
                                                class="akyc-file-btn akyc-file-selfie"
                                            >
                                                View Selfie
                                            </a>
                                        @else
                                            <div class="akyc-no-file">
                                                No selfie image
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    @if($status === 'pending')
                                        <div class="akyc-action-stack">
                                            <form method="POST" action="{{ route('admin.kyc.approve', $player) }}">
                                                @csrf

                                                <button class="akyc-btn akyc-btn-approve">
                                                    Approve
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.kyc.reject', $player) }}" class="akyc-reject-form">
                                                @csrf

                                                <input
                                                    name="reason"
                                                    placeholder="Reject reason"
                                                    class="akyc-input"
                                                >

                                                <button class="akyc-btn akyc-btn-reject">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="akyc-reviewed">
                                            Reviewed
                                        </div>

                                        @if($player->kyc_reviewed_at)
                                            <div class="akyc-muted">
                                                {{ $player->kyc_reviewed_at->format('M d, Y h:i A') }}
                                            </div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="akyc-empty">
                                        <p class="akyc-empty-title">
                                            No KYC submissions yet.
                                        </p>

                                        <p class="akyc-empty-sub">
                                            Player submissions will appear here.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($kycUsers, 'links'))
                <div class="akyc-pagination">
                    {{ $kycUsers->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>