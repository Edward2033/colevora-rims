<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('livewire.admin.categories.index');
    }

    public function create()
    {
        return view('livewire.admin.categories.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.categories.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.categories.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.categories.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.categories.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.categories.index');
    }
}
