<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return view('livewire.admin.roles.index');
    }

    public function create()
    {
        return view('livewire.admin.roles.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.roles.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.roles.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.roles.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.roles.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.roles.index');
    }
}
