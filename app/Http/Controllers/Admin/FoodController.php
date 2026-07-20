<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('livewire.admin.foods.index');
    }

    public function create()
    {
        return view('livewire.admin.foods.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.foods.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.foods.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.foods.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.foods.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.foods.index');
    }
}
