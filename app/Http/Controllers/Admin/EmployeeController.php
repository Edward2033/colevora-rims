<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('livewire.admin.employees.index');
    }

    public function create()
    {
        return view('livewire.admin.employees.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.employees.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.employees.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.employees.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.employees.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.employees.index');
    }
}
