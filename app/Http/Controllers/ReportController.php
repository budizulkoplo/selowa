<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRun;
use App\Models\Gallon;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('reports.index', $this->reportData($request));
    }

    public function export(Request $request, string $section, string $format): Response
    {
        $data = $this->reportData($request);
        $sections = [
            'daily' => ['title' => 'Rekap Harian', 'rows' => $data['dailyRows']],
            'customers' => ['title' => 'Pelanggan Teratas', 'rows' => $data['customerRows']],
            'vehicles' => ['title' => 'Laporan Mobil Berjalan', 'rows' => $data['vehicleRows']],
            'gallons' => ['title' => 'Stok Galon', 'rows' => $data['gallons']],
        ];
        abort_unless(isset($sections[$section]), 404);

        $payload = [
            'section' => $section,
            'title' => $sections[$section]['title'],
            'rows' => $sections[$section]['rows'],
            'from' => $data['from'],
            'to' => $data['to'],
        ];
        $filename = 'laporan-'.str_replace('_', '-', $section).'-'.$data['from']->format('Ymd').'-'.$data['to']->format('Ymd').'.'.$this->extension($format);

        if ($format === 'pdf') {
            return Pdf::loadView('exports.report-section', $payload)->setPaper('a4', $section === 'vehicles' ? 'landscape' : 'portrait')->download($filename);
        }

        return response()
            ->view('exports.report-section', $payload)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function reportData(Request $request): array
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->format('Y-m-d')))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->format('Y-m-d')))->endOfDay();

        $base = Transaction::where('status', 1)->whereBetween('created_at', [$from, $to]);

        return [
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
        ];
    }

    private function extension(string $format): string
    {
        return $format === 'pdf' ? 'pdf' : 'xls';
    }
}
