<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HeroSlideController extends Controller
{
    public function index()
    {
        return view('livewire.admin.cms.hero-slides.index');
    }

    public function create()
    {
        return view('livewire.admin.cms.hero-slides.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.cms.hero-slides.index');
    }

    public function show(string $id)
    {
        return view('livewire.admin.cms.hero-slides.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        return view('livewire.admin.cms.hero-slides.edit', ['id' => $id]);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.cms.hero-slides.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('admin.cms.hero-slides.index');
    }
}
