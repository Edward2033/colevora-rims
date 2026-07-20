<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('livewire.admin.purchases.index');
    }

    public function create()
    {
        return view('livewire.admin.purchases.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.purchases.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.purchases.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.purchases.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        $purchase = Purchase::findOrFail($id);
        $action = $request->input('action');

        match ($action) {
            'approve' => $purchase->update(['status' => 'approved']),
            'receive' => $purchase->markReceived(auth()->user()),
            'cancel' => $purchase->update(['status' => 'cancelled']),
            default => null,
        };

        return redirect()->route('admin.purchases.show', $purchase)
            ->with('success', 'Purchase updated.');
    }

    public function destroy(string $id)
    {
        Purchase::findOrFail($id)->delete();

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase deleted.');
    }
}
