<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return view('livewire.admin.suppliers.index');
    }

    public function create()
    {
        return view('livewire.admin.suppliers.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.suppliers.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.suppliers.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.suppliers.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        Supplier::findOrFail($id)->update($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.suppliers.index');
    }
}
