<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['user_type'] = 'customer';

        $user = User::create($validated);

        // Assign Customer role by default
        $customerRole = \App\Models\Role::where('name', 'Customer')->first();
        if ($customerRole) {
            $user->roles()->attach($customerRole->id);
        }

        event(new Registered($user));

        Auth::login($user);

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

        $this->redirect(route('customer.dashboard'), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header 
        title="Join Colevora" 
        description="Create your account to get started" 
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center text-gold-400" :status="session('status')" />

    <form wire:submit.prevent="register" class="flex flex-col gap-5">
        <!-- Name -->
        <div class="relative">
            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                <svg class="inline w-4 h-4 mr-1 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Full Name
            </label>
            <flux:input 
                wire:model="name" 
                id="name"
                type="text" 
                name="name" 
                required 
                autofocus 
                autocomplete="name" 
                placeholder="John Doe"
                class="w-full px-4 py-3 bg-white/5 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:border-gold-500 focus:ring-2 focus:ring-gold-500/50 transition-all duration-200"
            />
        </div>

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
                autocomplete="email" 
                placeholder="your@email.com"
                class="w-full px-4 py-3 bg-white/5 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:border-gold-500 focus:ring-2 focus:ring-gold-500/50 transition-all duration-200"
            />
        </div>

        <!-- Password -->
        <div class="relative">
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                <svg class="inline w-4 h-4 mr-1 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Password
            </label>
            <flux:input
                wire:model="password"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Create a strong password"
                class="w-full px-4 py-3 bg-white/5 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:border-gold-500 focus:ring-2 focus:ring-gold-500/50 transition-all duration-200"
            />
        </div>

        <!-- Confirm Password -->
        <div class="relative">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                <svg class="inline w-4 h-4 mr-1 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Confirm Password
            </label>
            <flux:input
                wire:model="password_confirmation"
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Re-enter your password"
                class="w-full px-4 py-3 bg-white/5 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:border-gold-500 focus:ring-2 focus:ring-gold-500/50 transition-all duration-200"
            />
        </div>

        <!-- Terms Agreement -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input 
                    id="terms" 
                    type="checkbox" 
                    required
                    class="w-4 h-4 text-gold-500 bg-white/5 border-gray-700 rounded focus:ring-gold-500 focus:ring-2"
                />
            </div>
            <label for="terms" class="ml-2 text-xs text-gray-400">
                I agree to the <a href="#" class="text-gold-400 hover:text-gold-300 underline">Terms of Service</a> and <a href="#" class="text-gold-400 hover:text-gold-300 underline">Privacy Policy</a>
            </label>
        </div>

        <!-- Register Button -->
        <button 
            type="submit" 
            class="w-full py-3.5 px-6 bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-gold-500/50 transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2 group"
        >
            <span>Create Account</span>
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <span class="bg-gray-900/50 px-3 text-gray-500">Already have an account?</span>
        </div>
    </div>

    <!-- Login Link -->
    <div class="text-center">
        <a 
            href="{{ route('login') }}" 
            class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gold-400 transition-colors duration-200 group"
        >
            <span>Sign in to your account</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</div>


