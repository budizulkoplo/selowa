<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerTransactionReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $customers = Customer::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedCustomer = null;
        $transactions = collect();
        $summary = [
            'transactions' => 0,
            'qty' => 0,
            'income' => 0,
        ];

        if ($request->filled('customer_id')) {
            $selectedCustomer = Customer::findOrFail($request->integer('customer_id'));

            $query = Transaction::query()
                ->with(['customer', 'deliveryRun.vehicle', 'creator'])
                ->where('status', 1)
                ->where('customer_id', $selectedCustomer->id);

            $summary = [
                'transactions' => (clone $query)->count(),
                'qty' => (clone $query)->sum('qty'),
                'income' => (int) (clone $query)->selectRaw('COALESCE(SUM(qty * price), 0) as total')->value('total'),
            ];

            $transactions = $query
                ->orderByDesc('created_at')
                ->paginate(25)
                ->withQueryString();
        }

        return view('reports.customer-transactions', [
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer,
            'transactions' => $transactions,
            'summary' => $summary,
        ]);
    }
}
