<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $photo = null;

    public function mount(): void
    {
        $this->name  = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->photo) {
            // Delete old photo
            if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = $this->photo->store('profile-photos', 'public');
            $this->photo = null;
        }

        $user->fill(array_filter($validated, fn($k) => in_array($k, ['name', 'email', 'profile_photo']), ARRAY_FILTER_USE_KEY));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Refresh the authenticated user instance to ensure session has updated data
        Auth::setUser($user->fresh());

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function removePhoto(): void
    {
        $user = Auth::user();
        if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
        }
        $user->update(['profile_photo' => null]);
        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }
        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout heading="Profile" subheading="Update your name, email address and profile photo">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">

            {{-- Profile Photo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Profile Photo</label>
                <div class="flex items-center gap-5">
                    {{-- Current / Preview --}}
                    <div class="relative flex-shrink-0">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview"
                                 class="h-20 w-20 rounded-full object-cover border-2 border-indigo-500/40">
                            <span class="absolute -bottom-1 -right-1 bg-indigo-500 text-white text-xs rounded-full px-1.5 py-0.5">New</span>
                        @elseif(auth()->user()->profile_photo)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}"
                                 class="h-20 w-20 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff&size=80"
                                 alt="{{ auth()->user()->name }}"
                                 class="h-20 w-20 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                        @endif
                    </div>

                    {{-- Upload controls --}}
                    <div class="flex flex-col gap-2">
                        <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            {{ $photo ? 'Change Photo' : 'Upload Photo' }}
                            <input type="file" wire:model="photo" accept="image/*" class="sr-only">
                        </label>
                        @if(auth()->user()->profile_photo && !$photo)
                            <button type="button" wire:click="removePhoto"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Remove Photo
                            </button>
                        @endif
                        @if($photo)
                            <button type="button" wire:click="$set('photo', null)"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                                Cancel
                            </button>
                        @endif
                        <p class="text-xs text-gray-400">JPG, PNG or GIF · max 2MB</p>
                    </div>
                </div>
                <div wire:loading wire:target="photo" class="mt-2 text-xs text-indigo-500">Uploading…</div>
                @error('photo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Name --}}
            <flux:input wire:model="name" label="{{ __('Name') }}" type="text" name="name" required autofocus autocomplete="name" />

            {{-- Email --}}
            <div>
                <flux:input wire:model="email" label="{{ __('Email') }}" type="email" name="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    <div>
                        <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                            {{ __('Your email address is unverified.') }}
                            <button wire:click.prevent="resendVerificationNotification"
                                class="rounded-md text-sm text-gray-600 dark:text-gray-400 underline hover:text-gray-900 dark:hover:text-gray-100 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm font-medium text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" class="w-full">{{ __('Save Changes') }}</flux:button>
                <x-action-message class="me-3" on="profile-updated">{{ __('Saved.') }}</x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
