<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryRun;
use App\Models\Gallon;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->format('Y-m-d')))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->format('Y-m-d')))->endOfDay();

        $base = Transaction::where('status', 1)->whereBetween('created_at', [$from, $to]);

        return view('reports.index', [
            'from' => $from,
            'to' => $to,
            'summary' => [
                'transactions' => (clone $base)->count(),
                'qty' => (clone $base)->sum('qty'),
                'income' => (clone $base)->selectRaw('COALESCE(SUM(qty * price), 0) as total')->value('total'),
                'customers' => (clone $base)->distinct('customer_id')->count('customer_id'),
            ],
            'dailyRows' => (clone $base)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as transaction_count, SUM(qty) as qty_total, SUM(qty * price) as income_total')
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderByDesc('date')
                ->get(),
            'customerRows' => Transaction::query()
                ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
                ->where('transactions.status', 1)
                ->whereBetween('transactions.created_at', [$from, $to])
                ->selectRaw('COALESCE(customers.name, transactions.customer_name_snapshot, "Tanpa customer") as name')
                ->selectRaw('COALESCE(customers.phone, "-") as phone')
                ->selectRaw('SUM(transactions.qty) as qty_total, SUM(transactions.qty * transactions.price) as income_total')
                ->groupByRaw('COALESCE(customers.name, transactions.customer_name_snapshot, "Tanpa customer"), COALESCE(customers.phone, "-")')
                ->orderByDesc('income_total')
                ->limit(25)
                ->get(),
            'vehicleRows' => DeliveryRun::query()
                ->with('vehicle')
                ->withCount(['transactions' => fn ($query) => $query->where('status', 1)->whereBetween('created_at', [$from, $to])])
                ->withSum(['transactions as qty_total' => fn ($query) => $query->where('status', 1)->whereBetween('created_at', [$from, $to])], 'qty')
                ->whereBetween('run_date', [$from->toDateString(), $to->toDateString()])
                ->orderByDesc('run_date')
                ->get(),
            'gallons' => Gallon::orderBy('name')->get(),
        ]);
    }
}
