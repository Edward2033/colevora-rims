<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <style>
            /* Animated Gradient Background */
            .auth-bg {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #334155 50%, #1e293b 75%, #0f172a 100%);
                background-size: 400% 400%;
                animation: gradientShift 15s ease infinite;
                position: relative;
                overflow: hidden;
            }

            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            /* Floating Particles */
            .particle {
                position: absolute;
                background: radial-gradient(circle, rgba(201, 122, 34, 0.8) 0%, rgba(201, 122, 34, 0) 70%);
                border-radius: 50%;
                pointer-events: none;
                animation: float-particle linear infinite;
            }

            @keyframes float-particle {
                0% {
                    transform: translateY(100vh) scale(0);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100vh) scale(1);
                    opacity: 0;
                }
            }

            .particle:nth-child(1) { left: 10%; width: 80px; height: 80px; animation-duration: 15s; animation-delay: 0s; }
            .particle:nth-child(2) { left: 20%; width: 60px; height: 60px; animation-duration: 18s; animation-delay: 2s; }
            .particle:nth-child(3) { left: 30%; width: 100px; height: 100px; animation-duration: 20s; animation-delay: 4s; }
            .particle:nth-child(4) { left: 40%; width: 70px; height: 70px; animation-duration: 16s; animation-delay: 1s; }
            .particle:nth-child(5) { left: 50%; width: 90px; height: 90px; animation-duration: 19s; animation-delay: 3s; }
            .particle:nth-child(6) { left: 60%; width: 75px; height: 75px; animation-duration: 17s; animation-delay: 5s; }
            .particle:nth-child(7) { left: 70%; width: 85px; height: 85px; animation-duration: 21s; animation-delay: 2.5s; }
            .particle:nth-child(8) { left: 80%; width: 65px; height: 65px; animation-duration: 14s; animation-delay: 4.5s; }
            .particle:nth-child(9) { left: 90%; width: 95px; height: 95px; animation-duration: 22s; animation-delay: 1.5s; }
            .particle:nth-child(10) { left: 15%; width: 55px; height: 55px; animation-duration: 18s; animation-delay: 3.5s; }

            /* Glassmorphism Card */
            .glass-card {
                background: rgba(15, 23, 42, 0.7);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(201, 122, 34, 0.2);
                box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37),
                            0 0 60px 0 rgba(201, 122, 34, 0.1);
                border-radius: 24px;
                transition: all 0.3s ease;
            }

            .glass-card:hover {
                border-color: rgba(201, 122, 34, 0.4);
                box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37),
                            0 0 80px 0 rgba(201, 122, 34, 0.2);
                transform: translateY(-5px);
            }

            /* Gold Glow Effect */
            .gold-glow {
                text-shadow: 0 0 20px rgba(201, 122, 34, 0.5);
            }
        </style>
    </head>
    <body class="min-h-screen antialiased auth-bg">
        <!-- Floating Particles -->
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>

        <div class="flex min-h-screen flex-col items-center justify-center gap-6 p-6 md:p-10 relative z-10">
            <div class="w-full max-w-md">
                <!-- Logo & Brand -->
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 mb-8 group" wire:navigate>
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-gold-500 to-gold-700 shadow-lg group-hover:shadow-gold-500/50 transition-all duration-300 group-hover:scale-110">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-white gold-glow">Colevora</h1>
                        <p class="text-sm text-gray-400 mt-1">Restaurant Management System</p>
                    </div>
                </a>

                <!-- Auth Card -->
                <div class="glass-card p-8 animate-fade-in">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="text-center mt-6">
                    <p class="text-xs text-gray-500">
                        &copy; {{ date('Y') }} Colevora. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
        @fluxScripts
        @livewireScripts
    </body>
</html>
