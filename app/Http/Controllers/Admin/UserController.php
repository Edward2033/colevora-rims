<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        return view('livewire.admin.users.index');
    }

    public function create()
    {
        return view('livewire.admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
            'user_type' => 'required|in:admin,employee,customer',
            'account_status' => 'required|in:active,inactive,suspended',
        ]);

        $user = User::create($validated);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(string $id)
    {
        return view('livewire.admin.users.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.users.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$id}",
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:admin,employee,customer',
            'account_status' => 'required|in:active,inactive,suspended',
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::min(8)];
        }

        $validated = $request->validate($rules);

        if (! $request->filled('password')) {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->id !== auth()->id()) {
            $user->delete();
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
