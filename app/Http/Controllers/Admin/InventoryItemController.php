<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    public function index()
    {
        return view('livewire.admin.inventory.items.index');
    }

    public function create()
    {
        return view('livewire.admin.inventory.items.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.inventory.items.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.inventory.items.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.inventory.items.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.inventory.items.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.inventory.items.index');
    }
}
