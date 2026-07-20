<?php

use App\Models\SiteSetting;
use function Livewire\Volt\{state, computed, mount, layout};

layout('components.layouts.admin');

state([
    // Restaurant
    'restaurant_name' => '', 'restaurant_email' => '', 'restaurant_phone' => '',
    'restaurant_whatsapp' => '', 'restaurant_address' => '',
    // Business
    'currency' => 'USD', 'currency_symbol' => '$', 'vat_rate' => '0',
    'tax_rate' => '0', 'timezone' => 'UTC',
    // SEO
    'meta_title' => '', 'meta_description' => '', 'meta_keywords' => '',
    // Social
    'facebook_url' => '', 'instagram_url' => '', 'twitter_url' => '',
    // Footer
    'footer_copyright' => '',
    'activeTab' => 'restaurant',
]);

mount(function () {
    $keys = [
        'restaurant_name', 'restaurant_email', 'restaurant_phone',
        'restaurant_whatsapp', 'restaurant_address',
        'currency', 'currency_symbol', 'vat_rate', 'tax_rate', 'timezone',
        'meta_title', 'meta_description', 'meta_keywords',
        'facebook_url', 'instagram_url', 'twitter_url', 'footer_copyright',
    ];
    foreach ($keys as $key) {
        $this->$key = SiteSetting::get($key, $this->$key);
    }
});

$save = function () {
    $this->validate([
        'restaurant_name'  => 'required|string|max:200',
        'restaurant_email' => 'nullable|email|max:200',
        'restaurant_phone' => 'nullable|string|max:50',
        'currency'         => 'required|string|max:10',
        'currency_symbol'  => 'required|string|max:5',
        'vat_rate'         => 'required|numeric|min:0|max:100',
        'tax_rate'         => 'required|numeric|min:0|max:100',
    ]);

    $settings = [
        'restaurant_name', 'restaurant_email', 'restaurant_phone',
        'restaurant_whatsapp', 'restaurant_address',
        'currency', 'currency_symbol', 'vat_rate', 'tax_rate', 'timezone',
        'meta_title', 'meta_description', 'meta_keywords',
        'facebook_url', 'instagram_url', 'twitter_url', 'footer_copyright',
    ];

    foreach ($settings as $key) {
        SiteSetting::set($key, $this->$key);
    }

    session()->flash('success', 'Settings saved successfully.');
};

?>

<div>
    <div class="p-6 max-w-3xl">
        <x-admin.page-header
            title="Site Settings"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'CMS'],['label'=>'Settings']]"
        />

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <!-- Tabs -->
        <div class="flex gap-1 mb-6 bg-gray-100 p-1 rounded-lg w-fit">
            @foreach(['restaurant' => 'Restaurant', 'business' => 'Business', 'seo' => 'SEO', 'social' => 'Social'] as $tab => $label)
                <button wire:click="$set('activeTab', '{{ $tab }}')"
                    class="px-4 py-1.5 text-sm font-medium rounded-md transition {{ $activeTab === $tab ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <form wire:submit="save">
            <!-- Restaurant Tab -->
            <div class="{{ $activeTab === 'restaurant' ? '' : 'hidden' }} bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Restaurant Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="restaurant_name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('restaurant_name') border-red-400 @enderror">
                        @error('restaurant_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Email</label>
                        <input type="email" wire:model="restaurant_email"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('restaurant_email') border-red-400 @enderror">
                        @error('restaurant_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Phone</label>
                        <input type="text" wire:model="restaurant_phone"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">WhatsApp</label>
                        <input type="text" wire:model="restaurant_whatsapp"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Address</label>
                        <textarea wire:model="restaurant_address" rows="2"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Business Tab -->
            <div class="{{ $activeTab === 'business' ? '' : 'hidden' }} bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Currency Code <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="currency"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="USD">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Currency Symbol <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="currency_symbol"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="$">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">VAT Rate (%)</label>
                        <input type="number" wire:model="vat_rate" step="0.01" min="0" max="100"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Tax Rate (%)</label>
                        <input type="number" wire:model="tax_rate" step="0.01" min="0" max="100"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Timezone</label>
                        <select wire:model="timezone" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}">{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Footer Copyright</label>
                        <input type="text" wire:model="footer_copyright"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                            placeholder="© 2025 Colevora. All rights reserved.">
                    </div>
                </div>
            </div>

            <!-- SEO Tab -->
            <div class="{{ $activeTab === 'seo' ? '' : 'hidden' }} bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Meta Title</label>
                    <input type="text" wire:model="meta_title"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                        placeholder="Colevora Restaurant - Fine Dining">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Meta Description</label>
                    <textarea wire:model="meta_description" rows="3"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                        placeholder="Discover our delicious menu..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Keywords</label>
                    <input type="text" wire:model="meta_keywords"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                        placeholder="restaurant, food, dining, delivery">
                </div>
            </div>

            <!-- Social Tab -->
            <div class="{{ $activeTab === 'social' ? '' : 'hidden' }} bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Facebook URL</label>
                    <input type="url" wire:model="facebook_url"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                        placeholder="https://facebook.com/yourpage">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Instagram URL</label>
                    <input type="url" wire:model="instagram_url"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                        placeholder="https://instagram.com/yourpage">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Twitter / X URL</label>
                    <input type="url" wire:model="twitter_url"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                        placeholder="https://twitter.com/yourpage">
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <x-admin.btn type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Save Settings</span>
                    <span wire:loading>Saving...</span>
                </x-admin.btn>
            </div>
        </form>
    </div>
</div>



