<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoyalCustomerController extends Controller
{
    public function index(): View
    {
        return view('loyal-customers.index', [
            'customers' => Customer::query()
                ->with('village.district.city')
                ->leftJoin('transactions', function ($join): void {
                    $join->on('transactions.customer_id', '=', 'customers.id')->where('transactions.status', 1);
                })
                ->select('customers.*')
                ->selectRaw('COALESCE(SUM(transactions.qty), 0) as total_qty')
                ->selectRaw('COALESCE(SUM(transactions.qty * transactions.price), 0) as total_spend')
                ->groupBy('customers.id')
                ->havingRaw('total_qty > 0')
                ->orderByDesc('total_spend')
                ->paginate(25),
        ]);
    }
}
