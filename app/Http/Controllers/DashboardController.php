<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Menu;
use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return view('dashboard.index', [
            'pageTitle' => 'Dashboard Selowa',
            'summaryCards' => [
                ['label' => 'Pelanggan', 'value' => number_format(Customer::where('is_active', true)->count()), 'icon' => 'fa-address-book', 'color' => 'navy', 'subtext' => 'pelanggan aktif'],
                ['label' => 'Transaksi Bulan Ini', 'value' => number_format(Transaction::where('status', 1)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count()), 'icon' => 'fa-shopping-cart', 'color' => 'blue', 'subtext' => now()->translatedFormat('F Y')],
                ['label' => 'Pendapatan Bulan Ini', 'value' => 'Rp '.number_format((int) Transaction::where('status', 1)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->selectRaw('COALESCE(SUM(qty * price), 0) as total')->value('total'), 0, ',', '.'), 'icon' => 'fa-money', 'color' => 'green', 'subtext' => 'total transaksi aktif'],
                ['label' => 'Menu Aktif', 'value' => number_format(Menu::where('is_active', true)->count()), 'icon' => 'fa-sitemap', 'color' => 'yellow', 'subtext' => 'navigasi aplikasi'],
            ],
            'customers' => Customer::with('village.district.city')->where('is_active', true)->orderBy('name')->get(),
            'serverNow' => now()->format('Y-m-d\TH:i'),
        ]);
    }
}
