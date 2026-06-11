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
        $customers = Customer::with(['village.district.city', 'registeredBy'])
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
            'customer' => new Customer(['is_active' => true, 'is_member' => false, 'discount_type' => 'none']),
            'villages' => Village::with('district.city')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Customer::create($this->validated($request) + [
            'registered_at' => now(),
            'registered_by' => auth()->id(),
        ]);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer): View
    {
        $customer->load('registeredBy');

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
        $request->merge([
            'customer_price' => $this->numericValue($request->input('customer_price')),
            'discount_value' => $this->numericValue($request->input('discount_value')),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'village_id' => ['nullable', 'exists:villages,id'],
            'rw' => ['nullable', 'string', 'max:10'],
            'rt' => ['nullable', 'string', 'max:10'],
            'timetable' => ['nullable', 'string', 'max:20'],
            'customer_price' => ['required', 'integer', 'min:0'],
            'is_member' => ['nullable', 'boolean'],
            'discount_type' => ['nullable', 'in:none,amount,percent'],
            'discount_value' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => false,
            'is_member' => false,
            'discount_type' => 'none',
            'discount_value' => 0,
        ];
    }

    private function numericValue(mixed $value): int
    {
        return (int) preg_replace('/\D+/', '', (string) $value);
    }
}
