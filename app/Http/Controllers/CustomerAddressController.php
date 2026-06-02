<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerAddressController extends Controller
{
    public function index(): View
    {
        return view('customer-addresses.index', [
            'cities' => City::with('districts.villages')->orderBy('name')->get(),
            'districts' => District::with('city')->orderBy('name')->get(),
            'villages' => Village::with('district.city')->orderBy('name')->get(),
        ]);
    }

    public function storeCity(Request $request): RedirectResponse
    {
        City::create($request->validate(['name' => ['required', 'string', 'max:100']]) + ['is_active' => true]);

        return back()->with('success', 'Kota berhasil ditambahkan.');
    }

    public function updateCity(Request $request, City $city): RedirectResponse
    {
        $city->update($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false]);

        return back()->with('success', 'Kota berhasil diperbarui.');
    }

    public function destroyCity(City $city): RedirectResponse
    {
        $city->update(['is_active' => false]);

        return back()->with('success', 'Kota dinonaktifkan.');
    }

    public function storeDistrict(Request $request): RedirectResponse
    {
        District::create($request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'name' => ['required', 'string', 'max:100'],
        ]) + ['is_active' => true]);

        return back()->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function updateDistrict(Request $request, District $district): RedirectResponse
    {
        $district->update($request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false]);

        return back()->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroyDistrict(District $district): RedirectResponse
    {
        $district->update(['is_active' => false]);

        return back()->with('success', 'Kecamatan dinonaktifkan.');
    }

    public function storeVillage(Request $request): RedirectResponse
    {
        Village::create($request->validate([
            'district_id' => ['required', 'exists:districts,id'],
            'name' => ['required', 'string', 'max:100'],
        ]) + ['is_active' => true]);

        return back()->with('success', 'Desa berhasil ditambahkan.');
    }

    public function updateVillage(Request $request, Village $village): RedirectResponse
    {
        $village->update($request->validate([
            'district_id' => ['required', 'exists:districts,id'],
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false]);

        return back()->with('success', 'Desa berhasil diperbarui.');
    }

    public function destroyVillage(Village $village): RedirectResponse
    {
        $village->update(['is_active' => false]);

        return back()->with('success', 'Desa dinonaktifkan.');
    }
}
