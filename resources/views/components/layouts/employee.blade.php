<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxStyles
    <style>
        .luxury-bg { background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); }
        .glass-card { background: rgba(15,23,42,0.6); backdrop-filter: blur(20px); border: 1px solid rgba(203,148,61,0.1); }
        .gold-gradient { background: linear-gradient(135deg, #cb943d 0%, #f4d03f 50%, #cb943d 100%); }
        .hover-glow:hover { box-shadow: 0 0 20px rgba(203,148,61,0.3); }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(30,41,59,0.3); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(203,148,61,0.5); border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(203,148,61,0.7); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased luxury-bg min-h-screen text-gray-100" x-data="{ sidebarOpen: true }" x-cloak>

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <aside class="glass-card border-r border-amber-500/10 transition-all duration-300 flex-shrink-0 flex flex-col custom-scrollbar overflow-y-auto w-64">

            {{-- Logo --}}
            <div class="flex items-center gap-3 p-6 border-b border-amber-500/10">
                <div class="h-10 w-10 rounded-xl gold-gradient flex items-center justify-center shadow-lg flex-shrink-0">
                    <svg class="w-6 h-6 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white">COLEVORA</h1>
                    <p class="text-xs text-amber-400">{{ auth()->user()->roles->first()?->name ?? 'Employee' }}</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-1">
                @php $role = auth()->user()->roles->first()?->name ?? ''; @endphp

                @if($role === 'Chef')
                    <a href="{{ route('employee.chef.dashboard') }}"
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('employee.chef.*') ? 'bg-gradient-to-r from-amber-500/20 to-amber-600/20 border border-amber-500/30' : 'hover:bg-white/5' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('employee.chef.*') ? 'text-amber-400' : 'text-gray-400 group-hover:text-amber-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                        </svg>
                        <span class="{{ request()->routeIs('employee.chef.*') ? 'text-amber-400 font-semibold' : 'text-gray-300 group-hover:text-white' }} transition-colors">Kitchen Dashboard</span>
                    </a>

                @elseif($role === 'Waiter')
                    <a href="{{ route('employee.waiter.dashboard') }}"
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('employee.waiter.*') ? 'bg-gradient-to-r from-amber-500/20 to-amber-600/20 border border-amber-500/30' : 'hover:bg-white/5' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('employee.waiter.*') ? 'text-amber-400' : 'text-gray-400 group-hover:text-amber-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span class="{{ request()->routeIs('employee.waiter.*') ? 'text-amber-400 font-semibold' : 'text-gray-300 group-hover:text-white' }} transition-colors">Waiter Dashboard</span>
                    </a>

                @elseif($role === 'Cashier')
                    <a href="{{ route('employee.cashier.dashboard') }}"
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('employee.cashier.*') ? 'bg-gradient-to-r from-amber-500/20 to-amber-600/20 border border-amber-500/30' : 'hover:bg-white/5' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('employee.cashier.*') ? 'text-amber-400' : 'text-gray-400 group-hover:text-amber-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span class="{{ request()->routeIs('employee.cashier.*') ? 'text-amber-400 font-semibold' : 'text-gray-300 group-hover:text-white' }} transition-colors">Cashier Dashboard</span>
                    </a>

                @elseif($role === 'Inventory Officer')
                    <a href="{{ route('employee.inventory-officer.dashboard') }}"
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('employee.inventory-officer.*') ? 'bg-gradient-to-r from-amber-500/20 to-amber-600/20 border border-amber-500/30' : 'hover:bg-white/5' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('employee.inventory-officer.*') ? 'text-amber-400' : 'text-gray-400 group-hover:text-amber-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="{{ request()->routeIs('employee.inventory-officer.*') ? 'text-amber-400 font-semibold' : 'text-gray-300 group-hover:text-white' }} transition-colors">Inventory Dashboard</span>
                    </a>

                @elseif($role === 'Receptionist')
                    <a href="{{ route('employee.receptionist.dashboard') }}"
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('employee.receptionist.*') ? 'bg-gradient-to-r from-amber-500/20 to-amber-600/20 border border-amber-500/30' : 'hover:bg-white/5' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('employee.receptionist.*') ? 'text-amber-400' : 'text-gray-400 group-hover:text-amber-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="{{ request()->routeIs('employee.receptionist.*') ? 'text-amber-400 font-semibold' : 'text-gray-300 group-hover:text-white' }} transition-colors">Reservations</span>
                    </a>
                @endif

                {{-- Account section --}}
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Account</p>
                    <a href="{{ route('settings.profile') }}"
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('settings.*') ? 'bg-gradient-to-r from-amber-500/20 to-amber-600/20 border border-amber-500/30' : 'hover:bg-white/5' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('settings.*') ? 'text-amber-400' : 'text-gray-400 group-hover:text-amber-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="{{ request()->routeIs('settings.*') ? 'text-amber-400 font-semibold' : 'text-gray-300 group-hover:text-white' }} transition-colors">My Profile</span>
                    </a>
                </div>
            </nav>

            {{-- User footer --}}
            <div class="p-4 border-t border-amber-500/10">
                <div class="flex items-center space-x-3 px-3 py-2">
                    <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=cb943d&color=fff' }}"
                         alt="{{ auth()->user()->name }}"
                         class="h-10 w-10 rounded-full border-2 border-amber-500/30 object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->roles->first()?->name ?? 'Employee' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit"
                        class="flex items-center space-x-3 w-full px-4 py-2.5 rounded-xl text-sm text-red-400 hover:text-red-300 hover:bg-red-500/5 transition-all">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top Header --}}
            <header class="glass-card border-b border-amber-500/10 sticky top-0 z-40">
                <div class="flex items-center justify-between px-8 py-4">
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                            {{ auth()->user()->roles->first()?->name ?? 'Employee' }} Dashboard
                        </h1>
                        <p class="text-sm text-gray-400 mt-1">
                            <span id="empCurrentDate"></span> • <span id="empCurrentTime"></span>
                        </p>
                    </div>

                    <div class="flex items-center space-x-4">
                        {{-- Online badge --}}
                        <span class="flex items-center gap-1.5 text-xs text-green-400 bg-green-400/10 border border-green-400/20 rounded-full px-3 py-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                            Online
                        </span>

                        {{-- User menu --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-white/5 transition-all">
                                <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=cb943d&color=fff' }}"
                                     alt="{{ auth()->user()->name }}"
                                     class="h-9 w-9 rounded-full border-2 border-amber-500/30 object-cover">
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400">{{ auth()->user()->roles->first()?->name ?? 'Employee' }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-56 glass-card rounded-2xl shadow-2xl border border-amber-500/20 py-2 z-50">
                                <a href="{{ route('settings.profile') }}" class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>Profile</span>
                                </a>
                                <a href="{{ route('settings.password') }}" class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    <span>Change Password</span>
                                </a>
                                <hr class="my-2 border-amber-500/10">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center space-x-3 w-full px-4 py-3 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/5 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    @fluxScripts

    <script>
        function updateDateTime() {
            const now = new Date();
            const d = document.getElementById('empCurrentDate');
            const t = document.getElementById('empCurrentTime');
            if (d) d.textContent = now.toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
            if (t) t.textContent = now.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</body>
</html>
