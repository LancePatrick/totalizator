<x-layouts.app :title="__('Account Inactive')">
    <style>
        .inactive-page {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at top left, rgba(239, 68, 68, .16), transparent 32%),
                radial-gradient(circle at bottom right, rgba(37, 99, 235, .14), transparent 34%),
                linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
        }

        .inactive-card {
            width: 100%;
            max-width: 760px;
            overflow: hidden;
            border-radius: 28px;
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .14);
        }

        .inactive-hero {
            position: relative;
            padding: 34px;
            color: white;
            background:
                radial-gradient(circle at 82% 24%, rgba(248, 113, 113, .55), transparent 30%),
                linear-gradient(135deg, #7f1d1d 0%, #dc2626 52%, #991b1b 100%);
        }

        .inactive-hero::after {
            content: "!";
            position: absolute;
            right: 34px;
            top: 18px;
            width: 86px;
            height: 86px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .22);
            font-size: 54px;
            font-weight: 950;
            color: white;
        }

        .inactive-kicker {
            margin: 0;
            color: #fecaca;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .18em;
        }

        .inactive-title {
            margin: 10px 0 0;
            max-width: 520px;
            color: white;
            font-size: 34px;
            line-height: 1.08;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .inactive-subtitle {
            margin: 12px 0 0;
            max-width: 560px;
            color: rgba(255,255,255,.82);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
        }

        .inactive-body {
            padding: 28px 34px 34px;
            display: grid;
            gap: 18px;
        }

        .inactive-alert {
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 850;
        }

        .inactive-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .inactive-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .inactive-box {
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 18px;
        }

        .inactive-label {
            margin: 0;
            color: #64748b;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .inactive-text {
            margin: 8px 0 0;
            color: #0f172a;
            font-size: 15px;
            font-weight: 850;
            line-height: 1.6;
        }

        .inactive-status {
            display: inline-flex;
            margin-top: 10px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
        }

        .inactive-status.pending {
            background: #fef3c7;
            color: #b45309;
        }

        .inactive-status.rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .inactive-status.approved {
            background: #dcfce7;
            color: #15803d;
        }

        .inactive-status.default {
            background: #e2e8f0;
            color: #475569;
        }

        .inactive-form {
            display: grid;
            gap: 14px;
        }

        .inactive-textarea {
            width: 100%;
            min-height: 140px;
            resize: vertical;
            border-radius: 18px;
            border: 1px solid #cbd5e1;
            background: white;
            padding: 14px 16px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 750;
            line-height: 1.5;
            outline: none;
        }

        .inactive-textarea:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, .12);
        }

        .inactive-file-wrap {
            border-radius: 18px;
            border: 1px dashed #cbd5e1;
            background: #ffffff;
            padding: 14px;
        }

        .inactive-file {
            width: 100%;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 12px;
            color: #0f172a;
            font-size: 13px;
            font-weight: 800;
        }

        .inactive-file::file-selector-button {
            margin-right: 14px;
            border: 0;
            border-radius: 12px;
            background: #dc2626;
            color: white;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            cursor: pointer;
        }

        .inactive-file:hover::file-selector-button {
            background: #991b1b;
        }

        .inactive-uploaded-link {
            margin-top: 10px;
            display: inline-flex;
            color: #2563eb;
            font-size: 12px;
            font-weight: 950;
            text-decoration: none;
        }

        .inactive-uploaded-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .inactive-help {
            margin: 8px 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.5;
        }

        .inactive-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .inactive-btn {
            min-height: 46px;
            border: 0;
            border-radius: 14px;
            padding: 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 950;
            text-transform: uppercase;
            text-decoration: none;
            cursor: pointer;
        }

        .inactive-btn-primary {
            background: #dc2626;
            color: white;
            box-shadow: 0 12px 24px rgba(220, 38, 38, .18);
        }

        .inactive-btn-primary:hover {
            background: #991b1b;
        }

        .inactive-btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .inactive-btn-secondary:hover {
            background: #cbd5e1;
        }

        .inactive-note {
            margin: 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.6;
        }

        @media (max-width: 700px) {
            .inactive-page {
                padding: 14px;
            }

            .inactive-hero {
                padding: 26px 22px;
            }

            .inactive-hero::after {
                display: none;
            }

            .inactive-title {
                font-size: 28px;
            }

            .inactive-body {
                padding: 22px;
            }

            .inactive-actions {
                display: grid;
            }

            .inactive-btn {
                width: 100%;
            }
        }
    </style>

    <div class="inactive-page">
        <section class="inactive-card">
            <div class="inactive-hero">
                <p class="inactive-kicker">Account Restricted</p>

                <h1 class="inactive-title">
                    Your account is currently inactive.
                </h1>

                <p class="inactive-subtitle">
                    Your player account has been deactivated by the admin. You can submit an appeal below for admin review.
                </p>
            </div>

            <div class="inactive-body">
                @if(session('success'))
                    <div class="inactive-alert inactive-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="inactive-alert inactive-error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="inactive-box">
                    <p class="inactive-label">
                        Admin Reason
                    </p>

                    <p class="inactive-text">
                        {{ $user->deactivation_reason ?: 'No reason provided by admin.' }}
                    </p>
                </div>

                <div class="inactive-box">
                    <p class="inactive-label">
                        Appeal Status
                    </p>

                    @php
                        $appealStatus = strtolower($user->appeal_status ?? 'default');

                        if (!in_array($appealStatus, ['pending', 'approved', 'rejected'])) {
                            $appealStatus = 'default';
                        }

                        $appealLabel = match ($appealStatus) {
                            'pending' => 'Pending Review',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            default => 'No Appeal Submitted',
                        };
                    @endphp

                    <span class="inactive-status {{ $appealStatus }}">
                        {{ $appealLabel }}
                    </span>

                    @if($user->appeal_reason)
                        <p class="inactive-text">
                            {{ $user->appeal_reason }}
                        </p>
                    @endif

                    @if($user->appeal_image)
                        <a
                            href="{{ asset('storage/' . $user->appeal_image) }}"
                            target="_blank"
                            class="inactive-uploaded-link"
                        >
                            View uploaded appeal image
                        </a>
                    @endif
                </div>

                @if($user->appeal_status !== 'pending')
                    <form
                        method="POST"
                        action="{{ route('player.account.appeal') }}"
                        enctype="multipart/form-data"
                        class="inactive-form"
                    >
                        @csrf

                        <div>
                            <p class="inactive-label" style="margin-bottom: 8px;">
                                Submit Appeal
                            </p>

                            <textarea
                                name="appeal_reason"
                                class="inactive-textarea"
                                placeholder="Explain why your account should be reactivated..."
                                required
                            >{{ old('appeal_reason') }}</textarea>
                        </div>

                        <div class="inactive-file-wrap">
                            <p class="inactive-label" style="margin-bottom: 8px;">
                                Upload Appeal Image / Proof
                            </p>

                            <input
                                type="file"
                                name="appeal_image"
                                accept="image/*"
                                class="inactive-file"
                            >

                            <p class="inactive-help">
                                Optional: Upload screenshot, receipt, valid proof, or any image that can help admin review your appeal. Max file size depends on controller validation.
                            </p>

                            @if($user->appeal_image)
                                <a
                                    href="{{ asset('storage/' . $user->appeal_image) }}"
                                    target="_blank"
                                    class="inactive-uploaded-link"
                                >
                                    View current uploaded appeal image
                                </a>
                            @endif
                        </div>

                        <div class="inactive-actions">
                            <button type="submit" class="inactive-btn inactive-btn-primary">
                                Submit Appeal
                            </button>

                            <a href="{{ route('dashboard') }}" class="inactive-btn inactive-btn-secondary">
                                Back
                            </a>
                        </div>
                    </form>
                @else
                    <div class="inactive-box">
                        <p class="inactive-label">
                            Pending
                        </p>

                        <p class="inactive-text">
                            Your appeal is already submitted. Please wait for admin review.
                        </p>

                        @if($user->appeal_image)
                            <a
                                href="{{ asset('storage/' . $user->appeal_image) }}"
                                target="_blank"
                                class="inactive-uploaded-link"
                            >
                                View uploaded appeal image
                            </a>
                        @endif
                    </div>

                    <div class="inactive-actions">
                        <a href="{{ route('dashboard') }}" class="inactive-btn inactive-btn-secondary">
                            Back
                        </a>
                    </div>
                @endif

                <p class="inactive-note">
                    While your account is inactive, you cannot access wallet, games, betting, cash in, or cash out features.
                </p>
            </div>
        </section>
    </div>
</x-layouts.app>