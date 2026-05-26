<x-layouts.app :title="__('KYC Verification')">
    @php
        $user = auth()->user();
        $kycStatus = $user->kycLabel();
        $statusRaw = strtolower($latestKyc->status ?? $user->kyc_status ?? 'not submitted');

        $statusColor = match ($statusRaw) {
            'approved' => '#16a34a',
            'pending' => '#d97706',
            'rejected', 'disapproved' => '#dc2626',
            default => '#64748b',
        };

        $statusBg = match ($statusRaw) {
            'approved' => '#dcfce7',
            'pending' => '#fef3c7',
            'rejected', 'disapproved' => '#fee2e2',
            default => '#f1f5f9',
        };
    @endphp

    <style>
        .kyc-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .kyc-hero {
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

        .kyc-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 54px 54px;
            opacity: .45;
        }

        .kyc-hero::after {
            content: "🛡️";
            position: absolute;
            right: 46px;
            top: 16px;
            font-size: 112px;
            transform: rotate(-8deg);
            filter: drop-shadow(0 18px 24px rgba(0,0,0,.30));
        }

        .kyc-hero-inner {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 24px;
            align-items: center;
        }

        .kyc-kicker {
            margin: 0;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .kyc-title {
            margin: 8px 0 0;
            color: white;
            font-size: 34px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .kyc-subtitle {
            margin: 10px 0 0;
            color: rgba(255,255,255,.74);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
        }

        .kyc-status-box {
            border-radius: 18px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            padding: 18px;
            backdrop-filter: blur(10px);
        }

        .kyc-status-label {
            margin: 0;
            color: rgba(255,255,255,.65);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .kyc-status-value {
            margin: 8px 0 0;
            color: #facc15;
            font-size: 34px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .kyc-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 850;
        }

        .kyc-alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .kyc-alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .kyc-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 380px;
            gap: 18px;
            align-items: start;
        }

        .kyc-card {
            background: white;
            border: 1px solid #dce6f2;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(15,23,42,.045);
        }

        .kyc-card-head {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
        }

        .kyc-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 950;
            flex-shrink: 0;
        }

        .kyc-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
        }

        .kyc-card-sub {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .kyc-form {
            display: grid;
            gap: 14px;
        }

        .kyc-field {
            display: grid;
            gap: 7px;
        }

        .kyc-label {
            color: #334155;
            font-size: 13px;
            font-weight: 900;
        }

        .kyc-input,
        .kyc-select {
            width: 100%;
            height: 48px;
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

        .kyc-file {
            width: 100%;
            min-height: 48px;
            border-radius: 14px;
            border: 1px dashed #bfdbfe;
            background: #eff6ff;
            padding: 12px 14px;
            outline: none;
            color: #1e3a8a;
            font-size: 14px;
            font-weight: 800;
            transition: .16s ease;
        }

        .kyc-input:focus,
        .kyc-select:focus,
        .kyc-file:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.12);
        }

        .kyc-btn {
            height: 50px;
            border: 0;
            border-radius: 14px;
            background: #2563eb;
            color: white;
            font-size: 14px;
            font-weight: 950;
            cursor: pointer;
            transition: .16s ease;
            box-shadow: 0 12px 24px rgba(37,99,235,.18);
        }

        .kyc-btn:hover {
            transform: translateY(-1px);
            filter: brightness(.96);
        }

        .kyc-side-stack {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .kyc-status-panel {
            border-radius: 18px;
            padding: 18px;
            background: #f8fbff;
            border: 1px solid #e7edf6;
        }

        .kyc-small-label {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 850;
        }

        .kyc-big-status {
            margin: 8px 0 0;
            color: #0f172a;
            font-size: 30px;
            line-height: 1.1;
            font-weight: 950;
        }

        .kyc-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            margin-top: 12px;
        }

        .kyc-info-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 16px;
        }

        .kyc-info-item {
            border: 1px solid #e7edf6;
            border-radius: 14px;
            padding: 14px;
            background: white;
        }

        .kyc-info-label {
            margin: 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .kyc-info-value {
            margin: 6px 0 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 900;
            line-height: 1.5;
        }

        .kyc-empty {
            border-radius: 16px;
            padding: 22px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            font-weight: 800;
            margin-top: 16px;
        }

        .kyc-guide-list {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .kyc-guide-item {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            color: #475569;
            font-size: 13px;
            font-weight: 750;
            line-height: 1.5;
        }

        .kyc-guide-dot {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 950;
            flex-shrink: 0;
        }

        @media (max-width: 1100px) {
            .kyc-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .kyc-hero-inner {
                grid-template-columns: 1fr;
            }

            .kyc-hero::after {
                display: none;
            }
        }
    </style>

    <div class="kyc-page">
        <section class="kyc-hero">
            <div class="kyc-hero-inner">
                <div>
                    <p class="kyc-kicker">KYC Verification</p>

                    <h1 class="kyc-title">
                        Submit Your Verification
                    </h1>

                    <p class="kyc-subtitle">
                        Upload your identity details so admin can approve your account.
                    </p>
                </div>

                <div class="kyc-status-box">
                    <p class="kyc-status-label">Account KYC</p>
                    <h2 class="kyc-status-value">{{ $kycStatus }}</h2>
                    <p class="kyc-subtitle" style="margin-top:8px;">Current verification status</p>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="kyc-alert kyc-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="kyc-alert kyc-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="kyc-grid">
            <div class="kyc-card">
                <div class="kyc-card-head">
                    <div class="kyc-card-icon" style="background:#eff6ff;color:#2563eb;">
                        🛡️
                    </div>

                    <div>
                        <h2 class="kyc-card-title">KYC Form</h2>
                        <p class="kyc-card-sub">
                            Fill in your correct details and upload clear ID/selfie images.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('player.kyc.store') }}" enctype="multipart/form-data" class="kyc-form">
                    @csrf

                    <div class="kyc-field">
                        <label class="kyc-label">Full Name</label>
                        <input
                            name="full_name"
                            value="{{ old('full_name', auth()->user()->name) }}"
                            required
                            class="kyc-input"
                            placeholder="Enter full name"
                        >
                    </div>

                    <div class="kyc-field">
                        <label class="kyc-label">Birthdate</label>
                        <input
                            type="date"
                            name="birthdate"
                            value="{{ old('birthdate') }}"
                            class="kyc-input"
                        >
                    </div>

                    <div class="kyc-field">
                        <label class="kyc-label">ID Type</label>
                        <select name="id_type" required class="kyc-select">
                            <option value="">Select ID Type</option>
                            <option value="National ID" {{ old('id_type') === 'National ID' ? 'selected' : '' }}>National ID</option>
                            <option value="Driver License" {{ old('id_type') === 'Driver License' ? 'selected' : '' }}>Driver License</option>
                            <option value="Passport" {{ old('id_type') === 'Passport' ? 'selected' : '' }}>Passport</option>
                            <option value="Voter ID" {{ old('id_type') === 'Voter ID' ? 'selected' : '' }}>Voter ID</option>
                            <option value="Other" {{ old('id_type') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="kyc-field">
                        <label class="kyc-label">ID Number</label>
                        <input
                            name="id_number"
                            value="{{ old('id_number') }}"
                            required
                            class="kyc-input"
                            placeholder="Enter ID number"
                        >
                    </div>

                    <div class="kyc-field">
                        <label class="kyc-label">Upload ID Image</label>
                        <input
                            type="file"
                            name="id_image"
                            accept="image/*"
                            class="kyc-file"
                        >
                    </div>

                    <div class="kyc-field">
                        <label class="kyc-label">Upload Selfie Image</label>
                        <input
                            type="file"
                            name="selfie_image"
                            accept="image/*"
                            class="kyc-file"
                        >
                    </div>

                    <button type="submit" class="kyc-btn">
                        Submit KYC
                    </button>
                </form>
            </div>

            <div class="kyc-side-stack">
                <div class="kyc-card">
                    <div class="kyc-card-head">
                        <div class="kyc-card-icon" style="background:#fef3c7;color:#d97706;">
                            ✓
                        </div>

                        <div>
                            <h2 class="kyc-card-title">Current Status</h2>
                            <p class="kyc-card-sub">Track your latest verification submission.</p>
                        </div>
                    </div>

                    <div class="kyc-status-panel">
                        <p class="kyc-small-label">Account KYC</p>

                        <h3 class="kyc-big-status">
                            {{ $kycStatus }}
                        </h3>

                        <span
                            class="kyc-pill"
                            style="background:{{ $statusBg }}; color:{{ $statusColor }};"
                        >
                            {{ $latestKyc?->status ?? $kycStatus }}
                        </span>
                    </div>

                    @if($latestKyc)
                        <div class="kyc-info-list">
                            <div class="kyc-info-item">
                                <p class="kyc-info-label">Status</p>
                                <p class="kyc-info-value">
                                    {{ strtoupper($latestKyc->status) }}
                                </p>
                            </div>

                            <div class="kyc-info-item">
                                <p class="kyc-info-label">Name</p>
                                <p class="kyc-info-value">
                                    {{ $latestKyc->full_name }}
                                </p>
                            </div>

                            <div class="kyc-info-item">
                                <p class="kyc-info-label">ID Details</p>
                                <p class="kyc-info-value">
                                    {{ $latestKyc->id_type }} / {{ $latestKyc->id_number }}
                                </p>
                            </div>

                            @if($latestKyc->admin_notes)
                                <div class="kyc-info-item">
                                    <p class="kyc-info-label">Admin Notes</p>
                                    <p class="kyc-info-value">
                                        {{ $latestKyc->admin_notes }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="kyc-empty">
                            No KYC submitted yet.
                        </div>
                    @endif
                </div>

                <div class="kyc-card">
                    <div class="kyc-card-head">
                        <div class="kyc-card-icon" style="background:#dcfce7;color:#16a34a;">
                            i
                        </div>

                        <div>
                            <h2 class="kyc-card-title">Upload Guide</h2>
                            <p class="kyc-card-sub">Make sure your files are clear before submitting.</p>
                        </div>
                    </div>

                    <div class="kyc-guide-list">
                        <div class="kyc-guide-item">
                            <span class="kyc-guide-dot">1</span>
                            Use a clear photo of your valid ID.
                        </div>

                        <div class="kyc-guide-item">
                            <span class="kyc-guide-dot">2</span>
                            Make sure your name and ID number are readable.
                        </div>

                        <div class="kyc-guide-item">
                            <span class="kyc-guide-dot">3</span>
                            Upload a selfie that matches your ID details.
                        </div>

                        <div class="kyc-guide-item">
                            <span class="kyc-guide-dot">4</span>
                            Wait for admin approval after submitting.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>