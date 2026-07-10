<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use App\Models\DeliveryVehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = collect(['owner', 'superadmin', 'admin', 'operator'])
            ->mapWithKeys(fn (string $name) => [$name => Role::firstOrCreate(['name' => $name, 'guard_name' => 'web'])]);

        $owner = User::firstOrCreate([
            'email' => 'owner@selowa.local',
        ], [
            'name' => 'Owner Selowa',
            'password' => 'password',
            'is_active' => true,
        ]);

        $owner->assignRole('owner');

        Company::firstOrCreate(['id' => 1], ['name' => 'Selowa']);
        DeliveryVehicle::updateOrCreate(['name' => 'Mobil 1'], ['user_id' => $owner->id, 'is_active' => true]);

        Menu::where('title', 'Hak Akses')->whereNull('parent_id')->update(['title' => 'Setting']);
        Menu::where('title', 'Master Data')->whereNull('parent_id')->update(['title' => 'Data Master']);

        $master = Menu::updateOrCreate(
            ['title' => 'Data Master', 'parent_id' => null],
            ['icon' => 'fa-database', 'sort_order' => 10, 'is_active' => true]
        );

        $transaksi = Menu::updateOrCreate(
            ['title' => 'Transaksi', 'parent_id' => null],
            ['icon' => 'fa-shopping-cart', 'sort_order' => 20, 'is_active' => true]
        );

        $laporan = Menu::updateOrCreate(
            ['title' => 'Laporan', 'parent_id' => null],
            ['icon' => 'fa-bar-chart', 'sort_order' => 30, 'is_active' => true]
        );

        $setting = Menu::updateOrCreate(
            ['title' => 'Setting', 'parent_id' => null],
            ['icon' => 'fa-cogs', 'sort_order' => 40, 'is_active' => true]
        );

        $menus = collect([
            Menu::updateOrCreate(['route_name' => 'profile.edit'], ['title' => 'Profile', 'parent_id' => null, 'icon' => 'fa-user-circle', 'sort_order' => 5, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'customers.index'], ['title' => 'Pelanggan', 'parent_id' => $master->id, 'icon' => 'fa-address-book', 'sort_order' => 10, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'customer-addresses.index'], ['title' => 'Alamat Pelanggan', 'parent_id' => $master->id, 'icon' => 'fa-map-marker', 'sort_order' => 20, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'customers.bulk-price.edit'], ['title' => 'Update Harga Pelanggan', 'parent_id' => $master->id, 'icon' => 'fa-refresh', 'sort_order' => 30, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'users.index'], ['title' => 'User', 'parent_id' => $master->id, 'icon' => 'fa-users', 'sort_order' => 40, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'transactions.index'], ['title' => 'Daftar Transaksi', 'parent_id' => $transaksi->id, 'icon' => 'fa-list-alt', 'sort_order' => 10, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'delivery-runs.index'], ['title' => 'Mobil Berjalan', 'parent_id' => $transaksi->id, 'icon' => 'fa-truck', 'sort_order' => 20, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'income.index'], ['title' => 'Pendapatan', 'parent_id' => $transaksi->id, 'icon' => 'fa-money', 'sort_order' => 30, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'loyal-customers.index'], ['title' => 'Pelanggan Setia', 'parent_id' => $transaksi->id, 'icon' => 'fa-star', 'sort_order' => 40, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'gallons.index'], ['title' => 'Stok Galon', 'parent_id' => $transaksi->id, 'icon' => 'fa-tint', 'sort_order' => 50, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'reports.index'], ['title' => 'Laporan Operasional', 'parent_id' => $laporan->id, 'icon' => 'fa-line-chart', 'sort_order' => 10, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'reports.customer-transactions'], ['title' => 'Transaksi per Pelanggan', 'parent_id' => $laporan->id, 'icon' => 'fa-address-card', 'sort_order' => 20, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'company.edit'], ['title' => 'Company', 'parent_id' => $setting->id, 'icon' => 'fa-building', 'sort_order' => 10, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'roles.index'], ['title' => 'Role', 'parent_id' => $setting->id, 'icon' => 'fa-id-badge', 'sort_order' => 20, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'menus.index'], ['title' => 'Menu', 'parent_id' => $setting->id, 'icon' => 'fa-sitemap', 'sort_order' => 30, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'role-menus.index'], ['title' => 'Role Menu', 'parent_id' => $setting->id, 'icon' => 'fa-check-square-o', 'sort_order' => 40, 'is_active' => true]),
            Menu::updateOrCreate(['route_name' => 'logout.menu'], ['title' => 'Logout', 'parent_id' => null, 'icon' => 'fa-sign-out', 'sort_order' => 35, 'is_active' => true]),
        ])->push($master, $transaksi, $laporan, $setting);

        $roles->each(fn (Role $role) => $role->menus()->syncWithoutDetaching($menus->pluck('id')));
    }
}
