<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function edit(): View
    {
        return view('company.edit', [
            'company' => Company::firstOrCreate(['id' => 1], ['name' => 'Selowa']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $company = Company::firstOrCreate(['id' => 1], ['name' => 'Selowa']);
        $company->update($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
            'embed_map' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
        ]));

        return back()->with('success', 'Konfigurasi perusahaan berhasil disimpan.');
    }
}
