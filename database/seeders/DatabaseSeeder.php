<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
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

        $master = Menu::updateOrCreate(
            ['title' => 'Master Data', 'parent_id' => null],
            ['icon' => 'fa-database', 'sort_order' => 10, 'is_active' => true]
        );

        $akses = Menu::updateOrCreate(
            ['title' => 'Hak Akses', 'parent_id' => null],
            ['icon' => 'fa-lock', 'sort_order' => 20, 'is_active' => true]
        );

        $transaksi = Menu::updateOrCreate(
            ['title' => 'Transaksi', 'parent_id' => null],
            ['icon' => 'fa-shopping-cart', 'sort_order' => 30, 'is_active' => true]
        );

        $menus = collect([
            Menu::updateOrCreate(['title' => 'Company', 'parent_id' => $master->id], ['route_name' => 'company.edit', 'icon' => 'fa-building', 'sort_order' => 5, 'is_active' => true]),
            Menu::updateOrCreate(['title' => 'Pelanggan', 'parent_id' => $master->id], ['route_name' => 'customers.index', 'icon' => 'fa-address-book', 'sort_order' => 8, 'is_active' => true]),
            Menu::updateOrCreate(['title' => 'Alamat Pelanggan', 'parent_id' => $master->id], ['route_name' => 'customer-addresses.index', 'icon' => 'fa-map-marker', 'sort_order' => 9, 'is_active' => true]),
            Menu::updateOrCreate(['title' => 'User', 'parent_id' => $master->id], ['route_name' => 'users.index', 'icon' => 'fa-users', 'sort_order' => 10, 'is_active' => true]),
            Menu::updateOrCreate(['title' => 'Role', 'parent_id' => $akses->id], ['route_name' => 'roles.index', 'icon' => 'fa-id-badge', 'sort_order' => 10, 'is_active' => true]),
            Menu::updateOrCreate(['title' => 'Menu', 'parent_id' => $akses->id], ['route_name' => 'menus.index', 'icon' => 'fa-sitemap', 'sort_order' => 20, 'is_active' => true]),
            Menu::updateOrCreate(['title' => 'Role Menu', 'parent_id' => $akses->id], ['route_name' => 'role-menus.index', 'icon' => 'fa-check-square-o', 'sort_order' => 30, 'is_active' => true]),
            Menu::updateOrCreate(['title' => 'Daftar Transaksi', 'parent_id' => $transaksi->id], ['route_name' => 'transactions.index', 'icon' => 'fa-list-alt', 'sort_order' => 10, 'is_active' => true]),
            Menu::updateOrCreate(['title' => 'Pendapatan', 'parent_id' => $transaksi->id], ['route_name' => 'income.index', 'icon' => 'fa-money', 'sort_order' => 20, 'is_active' => true]),
            Menu::updateOrCreate(['title' => 'Pelanggan Setia', 'parent_id' => $transaksi->id], ['route_name' => 'loyal-customers.index', 'icon' => 'fa-star', 'sort_order' => 30, 'is_active' => true]),
            Menu::updateOrCreate(['title' => 'Stok Galon', 'parent_id' => $transaksi->id], ['route_name' => 'gallons.index', 'icon' => 'fa-tint', 'sort_order' => 40, 'is_active' => true]),
        ])->push($master, $akses, $transaksi);

        $roles->each(fn (Role $role) => $role->menus()->syncWithoutDetaching($menus->pluck('id')));
    }
}
