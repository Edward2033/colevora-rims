<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RestaurantTableController extends Controller
{
    public function index()
    {
        return view('livewire.admin.tables.index');
    }

    public function create()
    {
        return view('livewire.admin.tables.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.tables.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.tables.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.tables.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.tables.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.tables.index');
    }
}
