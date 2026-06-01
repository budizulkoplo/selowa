<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::with('village.district.city')
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->q.'%')->orWhere('phone', 'like', '%'.$request->q.'%');
            }))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.form', [
            'customer' => new Customer(['is_active' => true]),
            'villages' => Village::with('district.city')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Customer::create($this->validated($request));

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer): View
    {
        return view('customers.form', [
            'customer' => $customer,
            'villages' => Village::with('district.city')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->validated($request));

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->update(['is_active' => false]);

        return redirect()->route('customers.index')->with('success', 'Pelanggan dinonaktifkan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'village_id' => ['nullable', 'exists:villages,id'],
            'rw' => ['nullable', 'string', 'max:10'],
            'rt' => ['nullable', 'string', 'max:10'],
            'timetable' => ['nullable', 'string', 'max:20'],
            'customer_price' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
