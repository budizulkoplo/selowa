<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryRun;
use App\Models\Menu;
use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $canSeeMonthly = auth()->user()?->hasAnyRole(['owner', 'superadmin']) ?? false;
        $start = $canSeeMonthly ? now()->copy()->startOfMonth() : now()->copy()->startOfDay();
        $end = $canSeeMonthly ? now()->copy()->endOfMonth() : now()->copy()->endOfDay();
        $periodLabel = $canSeeMonthly ? now()->translatedFormat('F Y') : now()->translatedFormat('d F Y');
        $transactionLabel = $canSeeMonthly ? 'Transaksi Bulan Ini' : 'Transaksi Hari Ini';
        $incomeLabel = $canSeeMonthly ? 'Pendapatan Bulan Ini' : 'Pendapatan Hari Ini';
        $transactionQuery = Transaction::where('status', 1)
            ->whereBetween('created_at', [$start, $end])
            ->when(! $canSeeMonthly, fn ($query) => $query->where('created_by', auth()->id()));

        return view('dashboard.index', [
            'pageTitle' => 'Dashboard Selowa',
            'summaryCards' => [
                ['label' => 'Pelanggan', 'value' => number_format(Customer::where('is_active', true)->count()), 'icon' => 'fa-address-book', 'color' => 'navy', 'subtext' => 'pelanggan aktif'],
                ['label' => $transactionLabel, 'value' => number_format((clone $transactionQuery)->count()), 'icon' => 'fa-shopping-cart', 'color' => 'blue', 'subtext' => $periodLabel],
                ['label' => $incomeLabel, 'value' => 'Rp '.number_format((int) (clone $transactionQuery)->selectRaw('COALESCE(SUM(qty * price), 0) as total')->value('total'), 0, ',', '.'), 'icon' => 'fa-money', 'color' => 'green', 'subtext' => 'total transaksi aktif'],
                ['label' => 'Menu Aktif', 'value' => number_format(Menu::where('is_active', true)->count()), 'icon' => 'fa-sitemap', 'color' => 'yellow', 'subtext' => 'navigasi aplikasi'],
            ],
            'customers' => Customer::with('village.district.city')->where('is_active', true)->orderBy('name')->get(),
            'deliveryRuns' => DeliveryRun::with('vehicle')->where('status', 1)->orderByDesc('run_date')->orderByDesc('id')->limit(100)->get(),
            'serverNow' => now()->format('Y-m-d\TH:i'),
        ]);
    }
}
