<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BulkCustomerPriceController extends Controller
{
    public function edit(): View
    {
        return view('customers.bulk-price', [
            'currentPrices' => Customer::where('is_active', true)->select('customer_price')->distinct()->orderBy('customer_price')->pluck('customer_price'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'in:all,price'],
            'old_price' => ['nullable', 'required_if:mode,price', 'integer', 'min:0'],
            'new_price' => ['required', 'integer', 'min:0'],
        ]);

        $query = Customer::where('is_active', true);

        if ($data['mode'] === 'price') {
            $query->where('customer_price', $data['old_price']);
        }

        $count = $query->update(['customer_price' => $data['new_price']]);

        return back()->with('success', $count.' pelanggan berhasil diperbarui.');
    }
}
