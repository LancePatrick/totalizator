<x-layouts.app :title="__('KYC Verification')">
    @php
        $status = $user->kyc_status ?? null;

        $statusLabel = match ($status) {
            'approved' => 'Verified',
            'pending' => 'Pending Review',
            'rejected' => 'Rejected',
            default => 'Not Submitted',
        };

        $statusClass = match ($status) {
            'approved' => 'bg-green-100 text-green-700 border-green-200',
            'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'rejected' => 'bg-red-100 text-red-700 border-red-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    @endphp

    <div class="space-y-6">
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-blue-950 to-blue-700 p-6 text-white shadow-xl sm:p-8">
            <div class="absolute inset-0 opacity-30 [background-image:linear-gradient(90deg,rgba(255,255,255,.08)_1px,transparent_1px),linear-gradient(rgba(255,255,255,.06)_1px,transparent_1px)] [background-size:54px_54px]"></div>

            <div class="relative z-10 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.22em] text-cyan-300">
                        Player Verification
                    </p>

                    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                        KYC Verification
                    </h1>

                    <p class="mt-3 max-w-3xl text-sm font-bold leading-6 text-white/80">
                        You must be verified before you can use wallet, cash in, cash out, play games, and place bets.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs font-black uppercase tracking-[.14em] text-white/70">
                        Status
                    </p>

                    <h2 class="mt-2 text-2xl font-black">
                        {{ $statusLabel }}
                    </h2>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-100 px-4 py-3 text-sm font-extrabold text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-100 px-4 py-3 text-sm font-extrabold text-red-800">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-950">
                        Verification Status
                    </h2>

                    <p class="mt-1 text-sm font-bold text-slate-500">
                        Current status of your submitted KYC.
                    </p>
                </div>

                <span class="inline-flex w-fit items-center rounded-full border px-4 py-2 text-xs font-black uppercase {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>
            </div>

            @if($status === 'approved')
                <div class="mt-6 rounded-3xl border border-green-200 bg-green-50 p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-green-600 text-2xl text-white">
                            ✓
                        </div>

                        <div>
                            <h3 class="text-xl font-black text-green-800">
                                Your KYC is verified.
                            </h3>

                            <p class="mt-2 text-sm font-bold leading-6 text-green-700">
                                You can now access wallet, cash in, cash out, play games, and place bets.
                            </p>

                            <a
                                href="{{ route('player.dashboard') }}"
                                class="mt-5 inline-flex h-11 items-center justify-center rounded-2xl bg-green-600 px-5 text-xs font-black uppercase tracking-wide text-white transition hover:bg-green-700"
                            >
                                Go to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @elseif($status === 'pending')
                <div class="mt-6 rounded-3xl border border-yellow-200 bg-yellow-50 p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-yellow-500 text-2xl text-white">
                            ⏳
                        </div>

                        <div>
                            <h3 class="text-xl font-black text-yellow-800">
                                Your KYC is pending review.
                            </h3>

                            <p class="mt-2 text-sm font-bold leading-6 text-yellow-700">
                                Please wait for admin approval. Other features are locked until your KYC is verified.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                @if($status === 'rejected')
                    <div class="mt-6 rounded-3xl border border-red-200 bg-red-50 p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-red-600 text-2xl text-white">
                                !
                            </div>

                            <div>
                                <h3 class="text-xl font-black text-red-800">
                                    Your KYC was rejected.
                                </h3>

                                <p class="mt-2 text-sm font-bold leading-6 text-red-700">
                                    Please submit another KYC with correct details.
                                </p>

                                @if(!empty($user->kyc_rejection_reason))
                                    <div class="mt-4 rounded-2xl border border-red-200 bg-white px-4 py-3 text-sm font-bold text-red-700">
                                        Reason: {{ $user->kyc_rejection_reason }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('player.kyc.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                    @csrf

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[.14em] text-slate-600">
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="full_name"
                                value="{{ old('full_name', $user->kyc_full_name ?? $user->name) }}"
                                required
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-950 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[.14em] text-slate-600">
                                Birthdate
                            </label>

                            <input
                                type="date"
                                name="birthdate"
                                value="{{ old('birthdate', $user->kyc_birthdate ? \Carbon\Carbon::parse($user->kyc_birthdate)->format('Y-m-d') : '') }}"
                                required
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-950 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[.14em] text-slate-600">
                            Address
                        </label>

                        <textarea
                            name="address"
                            rows="3"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-950 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >{{ old('address', $user->kyc_address ?? '') }}</textarea>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[.14em] text-slate-600">
                                Valid ID Type
                            </label>

                            <input
                                type="text"
                                name="valid_id_type"
                                value="{{ old('valid_id_type', $user->kyc_valid_id_type ?? '') }}"
                                placeholder="Passport, Driver License, National ID"
                                required
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-950 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[.14em] text-slate-600">
                                Valid ID Number
                            </label>

                            <input
                                type="text"
                                name="valid_id_number"
                                value="{{ old('valid_id_number', $user->kyc_valid_id_number ?? '') }}"
                                required
                                class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-950 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[.14em] text-slate-600">
                                Upload Valid ID
                            </label>

                            <input
                                type="file"
                                name="valid_id_image"
                                accept="image/*"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-950 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-xs file:font-black file:text-white"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[.14em] text-slate-600">
                                Upload Selfie
                            </label>

                            <input
                                type="file"
                                name="selfie_image"
                                accept="image/*"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-950 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-xs file:font-black file:text-white"
                            >
                        </div>
                    </div>

                    <button class="h-12 w-full rounded-2xl bg-blue-600 text-sm font-black uppercase tracking-wide text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700">
                        {{ $status === 'rejected' ? 'Submit Another KYC' : 'Submit KYC' }}
                    </button>
                </form>
            @endif
        </section>
    </div>
</x-layouts.app>