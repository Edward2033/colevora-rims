<?php

use App\Models\HeroSlide;
use App\Models\Food;
use App\Models\Category;
use App\Models\Testimonial;
use App\Models\SiteSetting;
use App\Models\NewsletterSubscriber;
use Livewire\Volt\Component;

new #[\Livewire\Attributes\Layout('components.layouts.public')]
class extends Component {
    public $slides;
    public $featuredFoods;
    public $categories;
    public $testimonials;
    public string $newsletterEmail = '';
    public bool $newsletterDone = false;

    public function mount(): void
    {
        $this->slides        = HeroSlide::where('status', 'active')->orderBy('ordering')->get();
        $this->categories    = Category::where('status', 'active')->withCount('foods')->limit(8)->get();
        $this->featuredFoods = Food::where('status', 'active')->where('availability', true)
            ->with('category')->inRandomOrder()->limit(6)->get();
        $this->testimonials  = Testimonial::where('status', 'active')->orderBy('order')->limit(6)->get();
    }

    public function addToCart(int $foodId): void
    {
        $food = Food::findOrFail($foodId);
        $price = $food->discount_price ?? $food->price;

        if (!auth()->check()) {
            $sessionCart = session()->get('guest_cart', []);
            if (isset($sessionCart[$foodId])) {
                $sessionCart[$foodId]['quantity']++;
            } else {
                $sessionCart[$foodId] = ['quantity' => 1, 'price' => $price];
            }
            session()->put('guest_cart', $sessionCart);
            $this->dispatch('cart-updated');
            $this->dispatch('notify', message: 'Added to cart!', type: 'success');
            return;
        }
        $cart = \App\Models\Cart::firstOrCreate(['user_id' => auth()->id(), 'status' => 'active']);
        $cart->addItem($food, 1);
        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Added to cart!', type: 'success');
    }

    public function subscribeNewsletter(): void
    {
        $this->validate(['newsletterEmail' => 'required|email']);
        NewsletterSubscriber::firstOrCreate(['email' => $this->newsletterEmail], ['active' => true]);
        $this->newsletterDone  = true;
        $this->newsletterEmail = '';
    }
}; ?>

<div>
    {{-- Hero Slider --}}
    <section class="relative overflow-hidden"
             x-data="{ current: 0, total: {{ max($slides->count(), 1) }} }"
             x-init="setInterval(() => { current = (current + 1) % total }, 5000)">

        @if($slides->count() > 0)
            @foreach($slides as $i => $slide)
            <div x-show="current === {{ $i }}"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="relative h-[520px] md:h-[680px]">
                @if($slide->image)
                <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}"
                     class="absolute inset-0 w-full h-full object-cover">
                @else
                <div class="absolute inset-0 bg-gradient-to-br from-orange-700 to-red-800"></div>
                @endif
                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                    <div class="text-center text-white px-4 max-w-3xl">
                        <p class="text-orange-300 font-medium tracking-widest uppercase text-sm mb-3">{{ SiteSetting::get('restaurant_tagline', 'Fine Dining Experience') }}</p>
                        <h1 class="text-4xl md:text-6xl font-extrabold mb-5 leading-tight">{{ $slide->title }}</h1>
                        @if($slide->subtitle)
                        <p class="text-lg md:text-xl text-gray-200 mb-8">{{ $slide->subtitle }}</p>
                        @endif
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            @if($slide->button_text && $slide->button_link)
                            <a href="{{ $slide->button_link }}" class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-8 py-4 rounded-xl transition">
                                {{ $slide->button_text }}
                            </a>
                            @endif
                            <a href="{{ route('reservation') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur text-white font-bold px-8 py-4 rounded-xl border border-white/40 transition">
                                Book a Table
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
                @foreach($slides as $i => $slide)
                <button @click="current = {{ $i }}" class="h-3 rounded-full transition-all"
                        :class="current === {{ $i }} ? 'bg-orange-500 w-6' : 'bg-white/60 w-3'"></button>
                @endforeach
            </div>
        @else
        <div class="relative h-[520px] md:h-[680px] bg-gradient-to-br from-orange-600 to-red-700 flex items-center justify-center">
            <div class="text-center text-white px-4 max-w-3xl">
                <p class="text-orange-200 font-medium tracking-widest uppercase text-sm mb-3">{{ SiteSetting::get('restaurant_tagline', 'Fine Dining Experience') }}</p>
                <h1 class="text-4xl md:text-6xl font-extrabold mb-5">Welcome to {{ SiteSetting::get('restaurant_name', 'Colevora') }}</h1>
                <p class="text-lg text-orange-100 mb-8">Crafted with passion. Served with love.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('menu') }}" class="bg-white text-orange-600 hover:bg-orange-50 font-bold px-8 py-4 rounded-xl transition">Explore Menu</a>
                    <a href="{{ route('reservation') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur text-white font-bold px-8 py-4 rounded-xl border border-white/40 transition">Book a Table</a>
                </div>
            </div>
        </div>
        @endif
    </section>

    {{-- Stats Bar --}}
    <section class="bg-orange-600 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                @foreach([['10+','Years of Excellence'],['50+','Expert Chefs'],['200+','Menu Items'],['10k+','Happy Customers']] as [$num,$label])
                <div>
                    <div class="text-3xl font-extrabold">{{ $num }}</div>
                    <div class="text-orange-200 text-sm mt-1">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Categories --}}
    @if($categories->count() > 0)
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10">
                <p class="text-orange-600 font-semibold uppercase tracking-widest text-sm mb-2">Browse By</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Food Categories</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-4">
                @foreach($categories as $cat)
                <a href="{{ route('menu', ['category' => $cat->slug]) }}" class="group flex flex-col items-center p-4 bg-white rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition">
                    @if($cat->image)
                    <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}" class="w-14 h-14 rounded-full object-cover mb-3">
                    @else
                    <div class="w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    @endif
                    <span class="text-xs font-semibold text-gray-700 group-hover:text-orange-600 text-center transition">{{ $cat->name }}</span>
                    <span class="text-xs text-gray-400 mt-0.5">{{ $cat->foods_count }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Featured Dishes --}}
    @if($featuredFoods->count() > 0)
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10">
                <p class="text-orange-600 font-semibold uppercase tracking-widest text-sm mb-2">Chef's Pick</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Featured Dishes</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredFoods as $food)
                <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden group">
                    <div class="relative h-52 overflow-hidden">
                        @if($food->image)
                        <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-100 to-red-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        @endif
                        @if($food->discount_price)
                        <div class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                            {{ round((($food->price - $food->discount_price) / $food->price) * 100) }}% OFF
                        </div>
                        @endif
                        <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition">
                            <a href="{{ route('food.show', $food->id) }}" class="w-9 h-9 bg-white rounded-full flex items-center justify-center shadow text-gray-700 hover:text-orange-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </div>
                    </div>
                    <div class="p-5">
                        <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full">{{ $food->category->name ?? 'Uncategorized' }}</span>
                        <h3 class="font-bold text-lg text-gray-900 mt-2 mb-1">{{ $food->name }}</h3>
                        <p class="text-gray-500 text-sm line-clamp-2 mb-4">{{ $food->description }}</p>
                        <div class="flex items-center justify-between">
                            <div>
                                @if($food->discount_price)
                                <span class="text-sm text-gray-400 line-through mr-1">{{ SiteSetting::get('currency_symbol','$') }}{{ number_format($food->price, 2) }}</span>
                                <span class="text-xl font-bold text-orange-600">{{ SiteSetting::get('currency_symbol','$') }}{{ number_format($food->discount_price, 2) }}</span>
                                @else
                                <span class="text-xl font-bold text-orange-600">{{ SiteSetting::get('currency_symbol','$') }}{{ number_format($food->price, 2) }}</span>
                                @endif
                            </div>
                            <button wire:click="addToCart({{ $food->id }})"
                                    class="bg-orange-600 hover:bg-orange-700 text-white p-2.5 rounded-xl transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-10">
                <a href="{{ route('menu') }}" class="inline-block bg-orange-600 hover:bg-orange-700 text-white font-bold px-10 py-4 rounded-xl transition">
                    View Full Menu
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- Why Choose Us --}}
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10">
                <p class="text-orange-600 font-semibold uppercase tracking-widest text-sm mb-2">Why Us</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">The Colevora Difference</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['🌿','Fresh Ingredients','We source only the finest local and seasonal ingredients daily.'],
                    ['👨‍🍳','Expert Chefs','Our award-winning chefs bring decades of culinary mastery.'],
                    ['⚡','Fast Service','From kitchen to table in under 20 minutes, guaranteed.'],
                    ['❤️','Made with Love','Every dish is crafted with passion and attention to detail.'],
                ] as [$icon,$title,$desc])
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition text-center">
                    <div class="text-4xl mb-4">{{ $icon }}</div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $title }}</h3>
                    <p class="text-gray-500 text-sm">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Reservation CTA --}}
    <section class="py-20 bg-gradient-to-r from-orange-600 to-red-600 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/3 translate-y-1/3"></div>
        </div>
        <div class="container mx-auto px-4 text-center relative z-10">
            <h2 class="text-3xl md:text-5xl font-extrabold mb-4">Reserve Your Table Today</h2>
            <p class="text-orange-100 text-lg mb-8 max-w-xl mx-auto">Experience an unforgettable dining experience. Book your table in advance and we'll make it perfect.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('reservation') }}" class="bg-white text-orange-600 hover:bg-orange-50 font-bold px-10 py-4 rounded-xl transition">Book a Table</a>
                <a href="{{ route('menu') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur text-white font-bold px-10 py-4 rounded-xl border border-white/40 transition">View Menu</a>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    @if($testimonials->count() > 0)
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10">
                <p class="text-orange-600 font-semibold uppercase tracking-widest text-sm mb-2">Reviews</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">What Our Guests Say</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($testimonials as $t)
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    <div class="flex text-orange-400 mb-3">
                        @for($s = 1; $s <= 5; $s++)
                        <svg class="w-4 h-4 {{ $s <= $t->rating ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">"{{ $t->content }}"</p>
                    <div class="flex items-center space-x-3">
                        @if($t->customer_photo)
                        <img src="{{ asset('storage/' . $t->customer_photo) }}" alt="{{ $t->customer_name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-sm">
                            {{ strtoupper(substr($t->customer_name, 0, 1)) }}
                        </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $t->customer_name }}</p>
                            @if($t->customer_title)
                            <p class="text-gray-400 text-xs">{{ $t->customer_title }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Newsletter --}}
    <section class="py-14 bg-gray-900 text-white">
        <div class="container mx-auto px-4 max-w-2xl text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Stay in the Loop</h2>
            <p class="text-gray-400 mb-6">Get exclusive offers, new menu updates, and event invites straight to your inbox.</p>
            @if($newsletterDone)
            <div class="bg-green-600/20 border border-green-500 text-green-400 px-6 py-4 rounded-xl">
                🎉 You're subscribed! Thank you for joining us.
            </div>
            @else
            <form wire:submit="subscribeNewsletter" class="flex flex-col sm:flex-row gap-3">
                <input type="email" wire:model="newsletterEmail" placeholder="Enter your email address"
                       class="flex-1 px-5 py-3 rounded-xl bg-gray-800 border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:border-orange-500">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-7 py-3 rounded-xl transition whitespace-nowrap">
                    Subscribe
                </button>
            </form>
            @error('newsletterEmail') <p class="mt-2 text-red-400 text-sm">{{ $message }}</p> @enderror
            @endif
        </div>
    </section>
</div>
