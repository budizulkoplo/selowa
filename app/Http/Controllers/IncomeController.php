<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(Request $request): View
    {
        $canFilter = auth()->user()?->hasAnyRole(['owner', 'superadmin']) ?? false;
        $from = $canFilter ? ($request->date('from') ?: now()->startOfMonth()) : now()->startOfDay();
        $to = $canFilter ? ($request->date('to') ?: now()->endOfMonth()) : now()->endOfDay();

        $rows = Transaction::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as transaction_count, SUM(qty) as qty_total, SUM(qty * price) as income_total')
            ->where('status', 1)
            ->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc('date')
            ->get();

        return view('income.index', compact('rows', 'from', 'to', 'canFilter'));
    }
}
