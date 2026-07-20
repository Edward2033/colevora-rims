<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login()
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $user = Auth::user();

        // Merge guest session cart into user's DB cart
        $sessionCart = session()->get('guest_cart', []);
        if (!empty($sessionCart)) {
            $cart = \App\Models\Cart::firstOrCreate(['user_id' => $user->id, 'status' => 'active']);
            foreach ($sessionCart as $foodId => $item) {
                $food = \App\Models\Food::find($foodId);
                if ($food) {
                    $cart->addItem($food, $item['quantity']);
                }
            }
            session()->forget('guest_cart');
        }

        // Determine redirect route based on user role
        $roles = $user->roles->pluck('name');

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($roles->contains('Chef')) {
            return redirect()->route('employee.chef.dashboard');
        }
        if ($roles->contains('Waiter')) {
            return redirect()->route('employee.waiter.dashboard');
        }
        if ($roles->contains('Cashier')) {
            return redirect()->route('employee.cashier.dashboard');
        }
        if ($roles->contains('Inventory Officer')) {
            return redirect()->route('employee.inventory-officer.dashboard');
        }
        if ($roles->contains('Receptionist')) {
            return redirect()->route('employee.receptionist.dashboard');
        }

        return redirect()->route('customer.dashboard');
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header 
        title="Welcome Back" 
        description="Sign in to your account to continue" 
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center text-gold-400" :status="session('status')" />

    <form wire:submit.prevent="login" class="flex flex-col gap-5">
        <!-- Email Address -->
        <div class="relative">
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                <svg class="inline w-4 h-4 mr-1 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
                Email Address
            </label>
            <flux:input 
                wire:model="email" 
                id="email"
                type="email" 
                name="email" 
                required 
                autofocus 
                autocomplete="email" 
                placeholder="your@email.com"
                class="w-full px-4 py-3 bg-white/5 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:border-gold-500 focus:ring-2 focus:ring-gold-500/50 transition-all duration-200"
            />
        </div>

        <!-- Password -->
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-sm font-medium text-gray-300">
                    <svg class="inline w-4 h-4 mr-1 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-gold-400 hover:text-gold-300 transition-colors duration-200">
                        Forgot password?
                    </a>
                @endif
            </div>
            <flux:input
                wire:model="password"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
                class="w-full px-4 py-3 bg-white/5 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:border-gold-500 focus:ring-2 focus:ring-gold-500/50 transition-all duration-200"
            />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <flux:checkbox 
                wire:model="remember" 
                id="remember"
                class="w-4 h-4 text-gold-500 bg-white/5 border-gray-700 rounded focus:ring-gold-500 focus:ring-2"
            />
            <label for="remember" class="ml-2 text-sm text-gray-400">
                Remember me for 30 days
            </label>
        </div>

        <!-- Login Button -->
        <button 
            type="submit" 
            wire:loading.attr="disabled"
            class="w-full py-3.5 px-6 bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-gold-500/50 transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
        >
            <svg wire:loading wire:target="login" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="login">Sign In</span>
            <span wire:loading wire:target="login">Signing in...</span>
            <svg wire:loading.remove wire:target="login" class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </button>
    </form>

    <!-- Divider -->
    <div class="relative my-2">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-700"></div>
        </div>
        <div class="relative flex justify-center text-xs uppercase">
            <span class="bg-gray-900/50 px-3 text-gray-500">New to Colevora?</span>
        </div>
    </div>

    <!-- Register Link -->
    <div class="text-center">
        <a 
            href="{{ route('register') }}" 
            class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gold-400 transition-colors duration-200 group"
        >
            <span>Create a new account</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</div>
