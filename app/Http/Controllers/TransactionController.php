<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryRun;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $baseQuery = Transaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 1);

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
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_at'] = $request->boolean('use_server_time') ? now() : ($data['created_at'] ?? now());
        unset($data['use_server_time']);

        Transaction::create($data + [
            'transaction_code' => 'TRX-'.now()->format('YmdHis').'-'.random_int(100, 999),
            'status' => 1,
        ]);

        return back()->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction): View
    {
        return view('transactions.form', [
            'transaction' => $transaction,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'deliveryRuns' => $this->deliveryRuns(),
        ]);
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['use_server_time']);

        $transaction->update($data);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->update(['status' => 0]);

        return back()->with('success', 'Transaksi dibatalkan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'delivery_run_id' => ['nullable', 'exists:delivery_runs,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'integer', 'min:0'],
            'created_at' => ['nullable', 'date'],
            'use_server_time' => ['nullable', 'boolean'],
        ]);
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
}
