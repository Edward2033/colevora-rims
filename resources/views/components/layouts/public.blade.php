<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? \App\Models\SiteSetting::get('restaurant_name', 'Colevora Restaurant') }}</title>
    <meta name="description" content="{{ \App\Models\SiteSetting::get('meta_description', '') }}">
    <meta name="keywords" content="{{ \App\Models\SiteSetting::get('meta_keywords', '') }}">
    <meta property="og:title" content="{{ $title ?? \App\Models\SiteSetting::get('restaurant_name', 'Colevora Restaurant') }}">
    <meta property="og:description" content="{{ \App\Models\SiteSetting::get('meta_description', '') }}">
    @if(\App\Models\SiteSetting::get('og_image'))
    <meta property="og:image" content="{{ asset('storage/' . \App\Models\SiteSetting::get('og_image')) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Base Styles */
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        html, body {
            overflow-x: hidden;
            max-width: 100%;
            position: relative;
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Container responsiveness */
        .public-container {
            width: 100%;
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        @media (min-width: 640px) {
            .public-container {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }
        
        @media (min-width: 1024px) {
            .public-container {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }
        
        /* Navigation Height */
        nav {
            height: auto;
            min-height: 64px;
        }
        
        /* Main Content Layout */
        main {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }
        
        /* Footer - Prevent Full Height */
        footer {
            width: 100%;
            max-width: 100%;
        }
        
        /* Floating Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        /* Touch Targets */
        button, a {
            min-height: 44px;
            min-width: 44px;
        }
        
        /* Scrollbar Styles */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.3);
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(203, 148, 61, 0.5);
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(203, 148, 61, 0.7);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ mobileMenuOpen: false, showBackToTop: false, cookieConsent: localStorage.getItem('cookie_consent') === 'true' }"
      x-init="window.addEventListener('scroll', () => { showBackToTop = window.scrollY > 400 })">

    {{-- Toast Notification --}}
    <div x-data="{ toasts: [] }"
         @notify.window="toasts.push({ id: Date.now(), message: $event.detail[0]?.message ?? $event.detail?.message, type: $event.detail[0]?.type ?? $event.detail?.type ?? 'success' }); setTimeout(() => toasts.shift(), 3500)"
         class="fixed top-4 right-4 z-[100] space-y-2 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="pointer-events-auto flex items-center space-x-3 px-6 py-4 rounded-xl shadow-2xl text-white text-sm font-medium backdrop-blur-sm"
                 :class="toast.type === 'success' ? 'bg-green-600/90' : (toast.type === 'error' ? 'bg-red-600/90' : 'bg-gold-600/90')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    {{-- Navigation --}}
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="public-container">
            <div class="flex justify-between items-center h-14 sm:h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    @if(\App\Models\SiteSetting::get('logo'))
                        <img src="{{ asset('storage/' . \App\Models\SiteSetting::get('logo')) }}" alt="{{ \App\Models\SiteSetting::get('restaurant_name', 'Colevora') }}" class="h-10 w-auto">
                    @else
                        <div class="w-9 h-9 bg-gradient-to-br from-gold-500 to-gold-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">{{ \App\Models\SiteSetting::get('restaurant_name', 'Colevora') }}</span>
                    @endif
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center space-x-1">
                    @php $current = request()->route()->getName(); @endphp
                    @foreach([['home','Home'],['menu','Menu'],['about','About'],['gallery','Gallery'],['reservation','Reserve'],['contact','Contact']] as [$route,$label])
                    <a href="{{ route($route) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $current === $route ? 'text-gold-600 bg-gold-50 shadow-sm' : 'text-gray-700 hover:text-gold-600 hover:bg-gold-50' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                {{-- Right Actions --}}
                <div class="hidden md:flex items-center space-x-3">
                    {{-- Cart icon always visible --}}
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-700 hover:text-gold-600 transition-all duration-200 hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-gradient-to-r from-gold-500 to-gold-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold shadow-lg animate-glow"
                              x-data x-text="$store.cart.count" x-show="$store.cart.count > 0">0</span>
                    </a>
                    @auth
                        <a href="{{ route('customer.dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-gold-600 transition-all duration-200">
                            {{ auth()->user()->name }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gold-600 transition-all duration-200">Login</a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 hover:-translate-y-0.5">Register</a>
                    @endauth
                </div>

                {{-- Mobile Toggle --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-700 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileMenuOpen" x-cloak x-transition class="md:hidden py-2 border-t border-gray-100">
                <div class="flex flex-col space-y-0.5">
                    @foreach([['home','Home'],['menu','Menu'],['about','About'],['gallery','Gallery'],['reservation','Reserve'],['contact','Contact']] as [$route,$label])
                    <a href="{{ route($route) }}" class="px-4 py-2 text-gray-700 hover:text-gold-600 hover:bg-gold-50 rounded-lg font-medium transition-all duration-200">{{ $label }}</a>
                    @endforeach
                    <div class="border-t border-gray-100 pt-2 mt-1">
                        @auth
                            <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:text-gold-600 font-medium">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-700 hover:text-gold-600 font-medium">Login</a>
                            <a href="{{ route('register') }}" class="block px-4 py-2 text-gold-600 font-medium">Register</a>
                        @endauth
                        <a href="{{ route('cart.index') }}" class="block px-4 py-2 text-gray-700 hover:text-gold-600 font-medium">Cart (<span x-data x-text="$store.cart.count">0</span>)</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>{{ $slot }}</main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-white">
        <div class="public-container py-8 sm:py-10 md:py-14">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 md:gap-10">
                {{-- Brand --}}
                <div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">{{ \App\Models\SiteSetting::get('restaurant_name', 'Colevora Restaurant') }}</h3>
                    <p class="text-gray-400 text-xs sm:text-sm leading-relaxed mb-3 sm:mb-5">{{ \App\Models\SiteSetting::get('footer_about', 'Experience the finest dining with our exquisite menu and exceptional service.') }}</p>
                    <div class="flex space-x-3">
                        @if($fb = \App\Models\SiteSetting::get('facebook'))
                        <a href="{{ $fb }}" target="_blank" class="w-9 h-9 bg-gray-800 hover:bg-gold-600 rounded-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                        @endif
                        @if($ig = \App\Models\SiteSetting::get('instagram'))
                        <a href="{{ $ig }}" target="_blank" class="w-9 h-9 bg-gray-800 hover:bg-gold-600 rounded-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke-width="2"/><path stroke-width="2" d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke-width="2"/></svg>
                        </a>
                        @endif
                        @if($tw = \App\Models\SiteSetting::get('twitter'))
                        <a href="{{ $tw }}" target="_blank" class="w-9 h-9 bg-gray-800 hover:bg-gold-600 rounded-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                        </a>
                        @endif
                        @if($wa = \App\Models\SiteSetting::get('whatsapp'))
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $wa) }}" target="_blank" class="w-9 h-9 bg-gray-800 hover:bg-green-600 rounded-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        @foreach([['home','Home'],['menu','Our Menu'],['about','About Us'],['gallery','Gallery'],['reservation','Reservations'],['contact','Contact']] as [$route,$label])
                        <li><a href="{{ route($route) }}" class="text-gray-400 hover:text-gold-400 transition-colors duration-200">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="text-lg font-bold mb-4">Contact Us</h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        @if($addr = \App\Models\SiteSetting::get('address'))
                        <li class="flex items-start space-x-2">
                            <svg class="w-4 h-4 mt-0.5 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ $addr }}</span>
                        </li>
                        @endif
                        @if($phone = \App\Models\SiteSetting::get('phone'))
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>{{ $phone }}</span>
                        </li>
                        @endif
                        @if($email = \App\Models\SiteSetting::get('email'))
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>{{ $email }}</span>
                        </li>
                        @endif
                    </ul>
                </div>

                {{-- Hours --}}
                <div>
                    <h3 class="text-lg font-bold mb-4">Opening Hours</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li class="flex justify-between"><span>Mon – Fri</span><span class="text-white">{{ \App\Models\SiteSetting::get('opening_hours_mon_fri', '11:00 AM – 10:00 PM') }}</span></li>
                        <li class="flex justify-between"><span>Saturday</span><span class="text-white">{{ \App\Models\SiteSetting::get('opening_hours_sat', '10:00 AM – 11:00 PM') }}</span></li>
                        <li class="flex justify-between"><span>Sunday</span><span class="text-white">{{ \App\Models\SiteSetting::get('opening_hours_sun', '10:00 AM – 9:00 PM') }}</span></li>
                    </ul>
                    <div class="mt-5">
                        <a href="{{ route('reservation') }}" class="inline-block bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white text-sm font-medium px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 hover:-translate-y-0.5">
                            Book a Table
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-800">
            <div class="container mx-auto px-4 py-5 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                <p>{{ \App\Models\SiteSetting::get('footer_content', '© ' . date('Y') . ' Colevora Restaurant. All rights reserved.') }}</p>
                <div class="flex space-x-4 mt-2 md:mt-0">
                    @if($privacy = \App\Models\SiteSetting::get('privacy_policy_url'))
                    <a href="{{ $privacy }}" class="hover:text-gold-400 transition-colors duration-200">Privacy Policy</a>
                    @endif
                    @if($terms = \App\Models\SiteSetting::get('terms_url'))
                    <a href="{{ $terms }}" class="hover:text-gold-400 transition-colors duration-200">Terms of Service</a>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    {{-- WhatsApp Floating Button --}}
    @if($wa = \App\Models\SiteSetting::get('whatsapp'))
    <a href="https://wa.me/{{ preg_replace('/\D/', '', $wa) }}" target="_blank"
       class="fixed bottom-20 right-5 z-50 w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110 animate-float">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>
    @endif

    {{-- Back to Top --}}
    <button @click="window.scrollTo({top:0,behavior:'smooth'})"
            x-show="showBackToTop" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="fixed bottom-5 right-5 z-50 w-12 h-12 bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
        </svg>
    </button>

    {{-- Cookie Consent --}}
    <div x-show="!cookieConsent" x-cloak
         class="fixed bottom-0 left-0 right-0 z-50 bg-gray-900 text-white px-6 py-4 shadow-2xl">
        <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-300">{{ \App\Models\SiteSetting::get('cookie_consent_text', 'We use cookies to enhance your experience.') }}</p>
            <div class="flex space-x-3 flex-shrink-0">
                @if($privacy = \App\Models\SiteSetting::get('privacy_policy_url'))
                <a href="{{ $privacy }}" class="text-sm text-gold-400 hover:text-gold-300 underline transition-colors duration-200">Learn more</a>
                @endif
                <button @click="cookieConsent = true; localStorage.setItem('cookie_consent', 'true')"
                        class="bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    Accept
                </button>
            </div>
        </div>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                count: 0,
                init() { this.updateCount(); },
                updateCount() {
                    fetch('{{ route('api.cart.count') }}').then(r => r.json()).then(d => this.count = d.count).catch(() => {});
                }
            });
        });
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('cart-updated', () => Alpine.store('cart').updateCount());
        });
    </script>
</body>
</html>
