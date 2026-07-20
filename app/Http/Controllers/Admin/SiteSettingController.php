<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function index()
    {
        return view('livewire.admin.cms.settings.index');
    }

    public function create()
    {
        return view('livewire.admin.cms.settings.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.cms.settings.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.cms.settings.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.cms.settings.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.cms.settings.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.cms.settings.index');
    }
}
