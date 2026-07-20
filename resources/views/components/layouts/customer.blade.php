<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Customer Dashboard' }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxStyles
    <style>
        .luxury-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(203, 148, 61, 0.1);
        }
        .gold-gradient {
            background: linear-gradient(135deg, #cb943d 0%, #f4d03f 50%, #cb943d 100%);
        }
        .hover-glow:hover {
            box-shadow: 0 0 20px rgba(203, 148, 61, 0.3);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased luxury-bg min-h-screen text-gray-100">
    <div class="min-h-screen">
        <!-- Top Navigation -->
        <nav class="glass-card border-b border-gold-500/10 sticky top-0 z-50">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center py-4">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-xl gold-gradient flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-gold-400 to-gold-600 bg-clip-text text-transparent">COLEVORA</span>
                    </a>

                    <div class="flex items-center space-x-6">
                        <!-- Real-time Date & Time -->
                        <div class="hidden md:block text-right">
                            <p class="text-xs text-gray-400">
                                <span id="customerCurrentDate"></span>
                            </p>
                            <p class="text-xs text-gold-400 font-medium">
                                <span id="customerCurrentTime"></span>
                            </p>
                        </div>
                        
                        <a href="{{ route('home') }}" class="text-gray-300 hover:text-gold-400 transition-colors">Home</a>
                        <a href="{{ route('menu') }}" class="text-gray-300 hover:text-gold-400 transition-colors">Menu</a>
                        <a href="{{ route('customer.dashboard') }}" class="text-gray-300 hover:text-gold-400 transition-colors">Dashboard</a>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 px-3 py-2 rounded-xl hover:bg-white/5 transition-all">
                                <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=cb943d&color=fff' }}" 
                                     alt="{{ auth()->user()->name }}" 
                                     class="h-8 w-8 rounded-full border-2 border-gold-500/30 object-cover">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 glass-card rounded-2xl shadow-2xl border border-gold-500/20 py-2">
                                <a href="{{ route('settings.profile') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-all">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/5 transition-all">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content with Sidebar -->
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="glass-card rounded-2xl p-6 border border-gold-500/20 hover-glow">
                        <div class="text-center mb-6 pb-6 border-b border-gold-500/10">
                            <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=cb943d&color=fff' }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="h-24 w-24 rounded-full mx-auto mb-3 border-4 border-gold-500/30 object-cover">
                            <h3 class="font-semibold text-white">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-gray-400 mt-1">{{ auth()->user()->email }}</p>
                        </div>

                        <nav class="space-y-2">
                            <a href="{{ route('customer.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('customer.dashboard') ? 'bg-gradient-to-r from-gold-500/20 to-gold-600/20 border border-gold-500/30 text-gold-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <a href="{{ route('customer.orders') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('customer.orders*') ? 'bg-gradient-to-r from-gold-500/20 to-gold-600/20 border border-gold-500/30 text-gold-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span>My Orders</span>
                            </a>

                            <a href="{{ route('customer.notifications') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('customer.notifications') ? 'bg-gradient-to-r from-gold-500/20 to-gold-600/20 border border-gold-500/30 text-gold-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span>Notifications</span>
                            </a>

                            <a href="{{ route('settings.profile') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('settings.*') ? 'bg-gradient-to-r from-gold-500/20 to-gold-600/20 border border-gold-500/30 text-gold-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Profile</span>
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
    @fluxScripts
    
    <script>
        // Update real-time date and time for customer layout
        function updateDateTime() {
            const now = new Date();
            const dateEl = document.getElementById('customerCurrentDate');
            const timeEl = document.getElementById('customerCurrentTime');
            
            if (dateEl) {
                dateEl.textContent = now.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }
            
            if (timeEl) {
                timeEl.textContent = now.toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                });
            }
        }
        
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</body>
</html>
