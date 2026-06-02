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
        $from = $request->date('from') ?: now()->startOfMonth();
        $to = $request->date('to') ?: now()->endOfMonth();

        $rows = Transaction::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as transaction_count, SUM(qty) as qty_total, SUM(qty * price) as income_total')
            ->where('status', 1)
            ->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc('date')
            ->get();

        return view('income.index', compact('rows', 'from', 'to'));
    }
}
