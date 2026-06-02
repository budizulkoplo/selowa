<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryRun;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        [$baseQuery, $month, $canSeeMonthly] = $this->filteredQuery($request);

        $transactions = (clone $baseQuery)
            ->with(['customer', 'deliveryRun.vehicle'])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('transactions.index', [
            'transactions' => $transactions,
            'month' => $month,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'deliveryRuns' => $this->deliveryRuns(),
            'total' => (clone $baseQuery)->selectRaw('COALESCE(SUM(qty * price), 0) as total')->value('total'),
            'serverNow' => now()->format('Y-m-d\TH:i'),
            'canSeeMonthly' => $canSeeMonthly,
            'periodLabel' => $canSeeMonthly ? 'Total Bulan Ini' : 'Total Hari Ini',
        ]);
    }

    public function export(Request $request, string $format): Response
    {
        [$baseQuery, $month, $canSeeMonthly, $start, $end] = $this->filteredQuery($request);
        $transactions = (clone $baseQuery)
            ->with(['customer', 'deliveryRun.vehicle'])
            ->orderByDesc('created_at')
            ->get();
        $filename = 'transaksi-selowa-'.($canSeeMonthly ? $month : now()->format('Y-m-d')).'.'.$this->extension($format);
        $data = [
            'title' => 'Data Transaksi',
            'period' => $start->format('d/m/Y').' - '.$end->format('d/m/Y'),
            'transactions' => $transactions,
            'total' => $transactions->sum(fn (Transaction $transaction) => $transaction->total()),
        ];

        if ($format === 'pdf') {
            return Pdf::loadView('exports.transactions', $data)->setPaper('a4', 'landscape')->download($filename);
        }

        return response()
            ->view('exports.transactions', $data)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_at'] = $this->canSeeMonthly()
            ? ($request->boolean('use_server_time') ? now() : ($data['created_at'] ?? now()))
            : now();
        $data['created_by'] = auth()->id();
        $data['customer_name_snapshot'] = Customer::whereKey($data['customer_id'])->value('name');
        unset($data['use_server_time']);

        Transaction::create($data + [
            'transaction_code' => 'TRX-'.now()->format('YmdHis').'-'.random_int(100, 999),
            'status' => 1,
        ]);

        return back()->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction): View
    {
        $this->authorizeTransactionDate($transaction);

        return view('transactions.form', [
            'transaction' => $transaction,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'deliveryRuns' => $this->deliveryRuns(),
        ]);
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeTransactionDate($transaction);

        $data = $this->validated($request);
        if (! $this->canSeeMonthly()) {
            unset($data['created_at']);
        }
        unset($data['use_server_time']);

        $transaction->update($data);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        abort_unless(auth()->user()?->hasAnyRole(['owner', 'superadmin']), 403);

        $transaction->update(['status' => 0]);

        return back()->with('success', 'Transaksi dibatalkan.');
    }

    private function validated(Request $request): array
    {
        $request->merge([
            'price' => $this->numericValue($request->input('price')),
        ]);

        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'delivery_run_id' => ['nullable', 'exists:delivery_runs,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'integer', 'min:0'],
            'created_at' => ['nullable', 'date'],
            'use_server_time' => ['nullable', 'boolean'],
        ]);
    }

    private function numericValue(mixed $value): int
    {
        return (int) preg_replace('/\D+/', '', (string) $value);
    }

    private function deliveryRuns()
    {
        return DeliveryRun::with('vehicle')
            ->where('status', 1)
            ->orderByDesc('run_date')
            ->orderByDesc('id')
            ->limit(100)
            ->get();
    }

    private function canSeeMonthly(): bool
    {
        return auth()->user()?->hasAnyRole(['owner', 'superadmin']) ?? false;
    }

    private function filteredQuery(Request $request): array
    {
        $canSeeMonthly = $this->canSeeMonthly();
        $month = $request->input('month', now()->format('Y-m'));
        $start = $canSeeMonthly ? Carbon::createFromFormat('Y-m', $month)->startOfMonth() : now()->copy()->startOfDay();
        $end = $canSeeMonthly ? $start->copy()->endOfMonth() : now()->copy()->endOfDay();

        $query = Transaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 1)
            ->when(! $canSeeMonthly, fn ($query) => $query->where('created_by', auth()->id()));

        return [$query, $month, $canSeeMonthly, $start, $end];
    }

    private function extension(string $format): string
    {
        return $format === 'pdf' ? 'pdf' : 'xls';
    }

    private function authorizeTransactionDate(Transaction $transaction): void
    {
        if ($this->canSeeMonthly()) {
            return;
        }

        abort_unless($transaction->created_at?->isToday() && (int) $transaction->created_by === (int) auth()->id(), 403);
    }
}
