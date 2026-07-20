<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('livewire.admin.orders.index');
    }

    public function create()
    {
        return view('livewire.admin.orders.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.orders.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.orders.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.orders.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.orders.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.orders.index');
    }
}
