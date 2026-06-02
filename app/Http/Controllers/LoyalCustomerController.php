<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoyalCustomerController extends Controller
{
    public function index(): View
    {
        $canSeeMonthly = auth()->user()?->hasAnyRole(['owner', 'superadmin']) ?? false;

        $transactionSummary = DB::table('transactions')
            ->where('status', 1)
            ->when(! $canSeeMonthly, fn ($query) => $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]))
            ->select('customer_id')
            ->selectRaw('COALESCE(SUM(qty), 0) as total_qty')
            ->selectRaw('COALESCE(SUM(qty * price), 0) as total_spend')
            ->groupBy('customer_id');

        return view('loyal-customers.index', [
            'customers' => Customer::query()
                ->with('village.district.city')
                ->joinSub($transactionSummary, 'transaction_summary', fn ($join) => $join->on('transaction_summary.customer_id', '=', 'customers.id'))
                ->select('customers.*')
                ->selectRaw('transaction_summary.total_qty')
                ->selectRaw('transaction_summary.total_spend')
                ->orderByDesc('total_spend')
                ->paginate(25),
        ]);
    }
}
