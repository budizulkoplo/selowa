<?php

namespace App\Http\Controllers;

use App\Models\Gallon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GallonController extends Controller
{
    public function index(): View
    {
        return view('gallons.index', ['gallons' => Gallon::orderBy('name')->paginate(20)]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gallon::create($this->validated($request));

        return back()->with('success', 'Data galon berhasil ditambahkan.');
    }

    public function update(Request $request, Gallon $gallon): RedirectResponse
    {
        $gallon->update($this->validated($request));

        return back()->with('success', 'Stok galon berhasil diperbarui.');
    }

    public function destroy(Gallon $gallon): RedirectResponse
    {
        $gallon->update(['is_active' => false]);

        return back()->with('success', 'Galon dinonaktifkan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'stock' => ['required', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
