<?php

use function Livewire\Volt\{layout};

layout('components.layouts.admin');

?>

@php
    $id = $id ?? request()->route('id');
    $user = \App\Models\User::with('roles')->findOrFail($id);
    $roles = \App\Models\Role::orderBy('name')->get();
    $userRoleIds = $user->roles->pluck('id')->toArray();
@endphp

<div>
    <div class="p-6 max-w-2xl">
        <x-admin.page-header
            title="Edit User"
            :breadcrumbs="[['label'=>'Admin','url'=>route('admin.dashboard')],['label'=>'Users','url'=>route('admin.users.index')],['label'=>'Edit']]"
        />

        <div class="glass-card rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('name') border-red-400 @enderror">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('email') border-red-400 @enderror">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">New Password <span class="text-gray-400 font-normal">(leave blank to keep)</span></label>
                        <input type="password" name="password"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('password') border-red-400 @enderror">
                        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">User Type</label>
                        <select name="user_type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="customer" {{ old('user_type', $user->user_type) === 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="employee" {{ old('user_type', $user->user_type) === 'employee' ? 'selected' : '' }}>Employee</option>
                            <option value="admin" {{ old('user_type', $user->user_type) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Account Status</label>
                        <select name="account_status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="active" {{ old('account_status', $user->account_status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('account_status', $user->account_status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('account_status', $user->account_status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Roles</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        {{ in_array($role->id, old('roles', $userRoleIds)) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                    {{ $role->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.btn type="submit">Update User</x-admin.btn>
                    <x-admin.btn href="{{ route('admin.users.index') }}" variant="secondary">Cancel</x-admin.btn>
                </div>
            </form>
        </div>
    </div>
</div>
