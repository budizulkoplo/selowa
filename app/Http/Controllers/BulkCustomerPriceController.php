<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BulkCustomerPriceController extends Controller
{
    public function edit(): View
    {
        return view('customers.bulk-price', [
            'currentPrices' => Customer::where('is_active', true)->select('customer_price')->distinct()->orderBy('customer_price')->pluck('customer_price'),
            'villages' => Village::with('district.city')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->merge([
            'old_price' => $this->numericValue($request->input('old_price')),
            'new_price' => $this->numericValue($request->input('new_price')),
        ]);

        $data = $request->validate([
            'mode' => ['required', 'in:all,village,price'],
            'old_price' => ['nullable', 'required_if:mode,price', 'integer', 'min:0'],
            'village_id' => ['nullable', 'required_if:mode,village', 'exists:villages,id'],
            'new_price' => ['required', 'integer', 'min:0'],
        ]);

        $query = Customer::where('is_active', true);

        if ($data['mode'] === 'price') {
            $query->where('customer_price', $data['old_price']);
        }

        if ($data['mode'] === 'village') {
            $query->where('village_id', $data['village_id']);
        }

        $count = $query->update(['customer_price' => $data['new_price']]);

        return back()->with('success', $count.' pelanggan berhasil diperbarui.');
    }

    private function numericValue(mixed $value): int
    {
        return (int) preg_replace('/\D+/', '', (string) $value);
    }
}
