@props(['title' => config('app.name', 'Laravel')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')

        <title>{{ $title }}</title>
    </head>

    <body class="min-h-screen bg-slate-100 font-sans antialiased">
        <div class="min-h-screen">
            <!-- Top Bar -->
            <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
                <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-700 to-yellow-500 text-sm font-black text-white">
                            TS
                        </div>

                        <div>
                            <div class="text-sm font-black uppercase tracking-wide text-slate-900">
                                Totalizator System
                            </div>

                            <div class="text-xs font-semibold text-slate-500">
                                {{ auth()->user()?->roleLabel() ?? 'Account' }}
                            </div>
                        </div>
                    </a>

                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <div class="text-sm font-bold text-slate-900">
                                {{ auth()->user()?->name }}
                            </div>

                            <div class="text-xs font-semibold text-slate-500">
                                ₱{{ number_format(auth()->user()?->wallet_balance ?? 0, 2) }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-black text-white hover:bg-slate-700"
                            >
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @fluxScripts
    </body>
</html>