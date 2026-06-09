<x-layouts::auth :title="__('Log in')">
    <div
        style="
            width:100%;
            max-width:440px;
            margin:0 auto;
            border-radius:24px;
            background:#ffffff;
            border:1px solid #dce6f2;
            box-shadow:0 20px 50px rgba(15,23,42,.12);
            padding:28px;
        "
    >
        <div style="text-align:center;">
            <div
                style="
                    width:72px;
                    height:72px;
                    margin:0 auto;
                    border-radius:22px;
                    background:linear-gradient(135deg,#03142f,#0848b9);
                    color:#facc15;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:24px;
                    font-weight:950;
                    box-shadow:0 16px 32px rgba(37,99,235,.22);
                "
            >
                JL
            </div>

            <p
                style="
                    margin:18px 0 0;
                    color:#2563eb;
                    font-size:12px;
                    font-weight:900;
                    text-transform:uppercase;
                    letter-spacing:.18em;
                "
            >
                SAMPLE SYSTEM
            </p>

            <h1
                style="
                    margin:8px 0 0;
                    color:#0f172a;
                    font-size:32px;
                    line-height:1.1;
                    font-weight:950;
                    letter-spacing:-.04em;
                "
            >
                Welcome Back
            </h1>

            <p
                style="
                    margin:10px 0 0;
                    color:#64748b;
                    font-size:14px;
                    font-weight:700;
                    line-height:1.6;
                "
            >
                Log in to access your dashboard.
            </p>
        </div>

        <div style="margin-top:22px;">
            <x-auth-session-status
                style="text-align:center;"
                :status="session('status')"
            />
        </div>

        @if($errors->any())
            @php
                $hasInactiveError = collect($errors->all())->contains(function ($error) {
                    return str_contains(strtolower($error), 'inactive');
                });

                $appealUrl = null;

                if (Route::has('player.appeal.index')) {
                    $appealUrl = route('player.appeal.index');
                } elseif (Route::has('player.account.inactive')) {
                    $appealUrl = route('player.account.inactive');
                }
            @endphp

            <div
                style="
                    margin-top:18px;
                    border-radius:16px;
                    padding:14px 16px;
                    background:#fee2e2;
                    color:#991b1b;
                    border:1px solid #fecaca;
                    font-size:13px;
                    font-weight:800;
                    line-height:1.6;
                "
            >
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

                @if($hasInactiveError && $appealUrl)
                    <div style="margin-top:12px;">
                        <a
                            href="{{ $appealUrl }}"
                            style="
                                width:100%;
                                min-height:44px;
                                border-radius:13px;
                                background:#dc2626;
                                color:#ffffff;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                text-align:center;
                                font-size:12px;
                                font-weight:950;
                                text-decoration:none;
                                text-transform:uppercase;
                                letter-spacing:.06em;
                                box-shadow:0 10px 20px rgba(220,38,38,.22);
                            "
                        >
                            Submit Appeal
                        </a>
                    </div>

                    <p
                        style="
                            margin:10px 0 0;
                            color:#7f1d1d;
                            font-size:12px;
                            font-weight:700;
                            line-height:1.5;
                            text-align:center;
                        "
                    >
                        Click the button above to explain your concern and submit proof for account reactivation.
                    </p>
                @endif
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('login.store') }}"
            style="
                margin-top:22px;
                display:grid;
                gap:15px;
            "
        >
            @csrf

            <div>
                <label
                    for="email"
                    style="
                        display:block;
                        margin-bottom:7px;
                        color:#334155;
                        font-size:13px;
                        font-weight:900;
                    "
                >
                    Email Address
                </label>

                <input
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                    style="
                        width:100%;
                        height:50px;
                        border-radius:14px;
                        border:1px solid #dce6f2;
                        background:#ffffff;
                        padding:0 14px;
                        outline:none;
                        color:#0f172a;
                        font-size:14px;
                        font-weight:800;
                    "
                >
            </div>

            <div>
                <div
                    style="
                        display:flex;
                        align-items:center;
                        justify-content:space-between;
                        gap:12px;
                        margin-bottom:7px;
                    "
                >
                    <label
                        for="password"
                        style="
                            color:#334155;
                            font-size:13px;
                            font-weight:900;
                        "
                    >
                        Password
                    </label>

                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            wire:navigate
                            style="
                                color:#2563eb;
                                font-size:12px;
                                font-weight:900;
                                text-decoration:none;
                            "
                        >
                            Forgot password?
                        </a>
                    @endif
                </div>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Password"
                    style="
                        width:100%;
                        height:50px;
                        border-radius:14px;
                        border:1px solid #dce6f2;
                        background:#ffffff;
                        padding:0 14px;
                        outline:none;
                        color:#0f172a;
                        font-size:14px;
                        font-weight:800;
                    "
                >
            </div>

            <label
                style="
                    display:flex;
                    align-items:center;
                    gap:10px;
                    color:#475569;
                    font-size:14px;
                    font-weight:800;
                    cursor:pointer;
                "
            >
                <input
                    type="checkbox"
                    name="remember"
                    {{ old('remember') ? 'checked' : '' }}
                    style="
                        width:18px;
                        height:18px;
                        accent-color:#2563eb;
                        cursor:pointer;
                    "
                >

                Remember me
            </label>

            <button
                type="submit"
                data-test="login-button"
                style="
                    width:100%;
                    height:52px;
                    border:0;
                    border-radius:15px;
                    background:#2563eb;
                    color:#ffffff;
                    font-size:14px;
                    font-weight:950;
                    cursor:pointer;
                    box-shadow:0 14px 28px rgba(37,99,235,.22);
                    text-transform:uppercase;
                    letter-spacing:.05em;
                "
            >
                Log in
            </button>
        </form>

        @if (Route::has('register'))
            <div
                style="
                    margin-top:20px;
                    display:flex;
                    justify-content:center;
                    align-items:center;
                    gap:6px;
                    color:#64748b;
                    font-size:14px;
                    font-weight:700;
                "
            >
                <span>Don&apos;t have an account?</span>

                <a
                    href="{{ route('register') }}"
                    wire:navigate
                    style="
                        color:#2563eb;
                        font-weight:950;
                        text-decoration:none;
                    "
                >
                    Create account
                </a>
            </div>
        @endif
    </div>
</x-layouts::auth>