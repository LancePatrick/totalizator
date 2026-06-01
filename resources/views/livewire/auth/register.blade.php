<x-layouts::auth :title="__('Register')">
    @php
        $agentFromLink = request('agent');
        $oldRole = old('role', $agentFromLink ? 'player' : 'player');
        $oldAgentCode = old('agent_code', $agentFromLink);
    @endphp

    <div
        style="
            width:100%;
            max-width:460px;
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
                TS
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
                SAMPLE SUGAL
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
                Create Account
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
                Register as Player, Agent, or Admin.
            </p>

            @if($agentFromLink)
                <div
                    style="
                        margin-top:16px;
                        border-radius:16px;
                        padding:12px 14px;
                        background:#eff6ff;
                        color:#1d4ed8;
                        border:1px solid #bfdbfe;
                        font-size:13px;
                        font-weight:900;
                        line-height:1.5;
                    "
                >
                    You are registering under agent code:
                    <strong>{{ $agentFromLink }}</strong>
                </div>
            @endif
        </div>

        <div style="margin-top:22px;">
            <x-auth-session-status
                style="text-align:center;"
                :status="session('status')"
            />
        </div>

        @if($errors->any())
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
                "
            >
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('register.store') }}"
            style="
                margin-top:22px;
                display:grid;
                gap:15px;
            "
        >
            @csrf

            <div>
                <label
                    for="name"
                    style="
                        display:block;
                        margin-bottom:7px;
                        color:#334155;
                        font-size:13px;
                        font-weight:900;
                    "
                >
                    Full Name
                </label>

                <input
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Juan Dela Cruz"
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
                <label
                    for="role"
                    style="
                        display:block;
                        margin-bottom:7px;
                        color:#334155;
                        font-size:13px;
                        font-weight:900;
                    "
                >
                    Account Type
                </label>

                <select
                    name="role"
                    id="role"
                    required
                    onchange="toggleRegisterFields()"
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
                        font-weight:900;
                    "
                >
                    <option value="player" @selected($oldRole === 'player')>
                        Player
                    </option>

                    @if(!$agentFromLink)
                        <option value="agent" @selected($oldRole === 'agent')>
                            Agent
                        </option>

                        <option value="admin" @selected($oldRole === 'admin')>
                            Admin
                        </option>
                    @endif
                </select>

                @if($agentFromLink)
                    <p style="margin:6px 0 0;color:#64748b;font-size:12px;font-weight:800;">
                        Agent link detected. This account will be registered as player.
                    </p>
                @endif

                @error('role')
                    <p style="margin:6px 0 0;color:#dc2626;font-size:12px;font-weight:800;">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div id="agent-code-wrapper">
                <label
                    for="agent_code"
                    style="
                        display:block;
                        margin-bottom:7px;
                        color:#334155;
                        font-size:13px;
                        font-weight:900;
                    "
                >
                    Agent Code
                </label>

                <input
                    id="agent_code"
                    name="agent_code"
                    value="{{ $oldAgentCode }}"
                    type="text"
                    placeholder="Enter agent code"
                    @if($agentFromLink) readonly @endif
                    style="
                        width:100%;
                        height:50px;
                        border-radius:14px;
                        border:1px solid #dce6f2;
                        background:{{ $agentFromLink ? '#f8fafc' : '#ffffff' }};
                        padding:0 14px;
                        outline:none;
                        color:#0f172a;
                        font-size:14px;
                        font-weight:800;
                    "
                >

                @error('agent_code')
                    <p style="margin:6px 0 0;color:#dc2626;font-size:12px;font-weight:800;">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div id="admin-code-wrapper" style="display:none;">
                <label
                    for="admin_code"
                    style="
                        display:block;
                        margin-bottom:7px;
                        color:#334155;
                        font-size:13px;
                        font-weight:900;
                    "
                >
                    Admin Secret Code
                </label>

                <input
                    id="admin_code"
                    name="admin_code"
                    type="password"
                    placeholder="Enter admin code"
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

                @error('admin_code')
                    <p style="margin:6px 0 0;color:#dc2626;font-size:12px;font-weight:800;">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label
                    for="password"
                    style="
                        display:block;
                        margin-bottom:7px;
                        color:#334155;
                        font-size:13px;
                        font-weight:900;
                    "
                >
                    Password
                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
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

            <div>
                <label
                    for="password_confirmation"
                    style="
                        display:block;
                        margin-bottom:7px;
                        color:#334155;
                        font-size:13px;
                        font-weight:900;
                    "
                >
                    Confirm Password
                </label>

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm password"
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

            <button
                type="submit"
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
                Create Account
            </button>
        </form>

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
            <span>Already have an account?</span>

            <a
                href="{{ route('login') }}"
                style="
                    color:#2563eb;
                    font-weight:950;
                    text-decoration:none;
                "
                wire:navigate
            >
                Log in
            </a>
        </div>
    </div>

    <script>
        function toggleRegisterFields() {
            const role = document.getElementById('role');
            const adminCodeWrapper = document.getElementById('admin-code-wrapper');
            const agentCodeWrapper = document.getElementById('agent-code-wrapper');

            if (!role || !adminCodeWrapper || !agentCodeWrapper) {
                return;
            }

            if (role.value === 'admin') {
                adminCodeWrapper.style.display = 'block';
                agentCodeWrapper.style.display = 'none';
            } else if (role.value === 'player') {
                adminCodeWrapper.style.display = 'none';
                agentCodeWrapper.style.display = 'block';
            } else {
                adminCodeWrapper.style.display = 'none';
                agentCodeWrapper.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', toggleRegisterFields);
        toggleRegisterFields();
    </script>
</x-layouts::auth>