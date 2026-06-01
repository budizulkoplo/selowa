<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BulkCustomerPriceController;
use App\Http\Controllers\DeliveryRunController;
use App\Http\Controllers\GallonController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\LoyalCustomerController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoleMenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('checklogin')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout.menu');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::resource('roles', RoleController::class)->except('show');
    Route::resource('users', UserController::class)->except('show');
    Route::resource('menus', MenuController::class)->except('show');
    Route::get('/role-menus', [RoleMenuController::class, 'index'])->name('role-menus.index');
    Route::put('/role-menus/{role}', [RoleMenuController::class, 'update'])->name('role-menus.update');

    Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company', [CompanyController::class, 'update'])->name('company.update');
    Route::resource('customers', CustomerController::class)->except('show');
    Route::get('/customers-bulk-price', [BulkCustomerPriceController::class, 'edit'])->name('customers.bulk-price.edit');
    Route::put('/customers-bulk-price', [BulkCustomerPriceController::class, 'update'])->name('customers.bulk-price.update');
    Route::get('/customer-addresses', [CustomerAddressController::class, 'index'])->name('customer-addresses.index');
    Route::post('/customer-addresses/cities', [CustomerAddressController::class, 'storeCity'])->name('customer-addresses.cities.store');
    Route::post('/customer-addresses/districts', [CustomerAddressController::class, 'storeDistrict'])->name('customer-addresses.districts.store');
    Route::post('/customer-addresses/villages', [CustomerAddressController::class, 'storeVillage'])->name('customer-addresses.villages.store');

    Route::resource('transactions', TransactionController::class)->except(['show', 'create']);
    Route::get('/delivery-runs', [DeliveryRunController::class, 'index'])->name('delivery-runs.index');
    Route::post('/delivery-runs/vehicles', [DeliveryRunController::class, 'storeVehicle'])->name('delivery-runs.vehicles.store');
    Route::post('/delivery-runs/routes', [DeliveryRunController::class, 'storeRoute'])->name('delivery-runs.routes.store');
    Route::post('/delivery-runs', [DeliveryRunController::class, 'store'])->name('delivery-runs.store');
    Route::put('/delivery-runs/{deliveryRun}', [DeliveryRunController::class, 'update'])->name('delivery-runs.update');
    Route::delete('/delivery-runs/{deliveryRun}', [DeliveryRunController::class, 'destroy'])->name('delivery-runs.destroy');
    Route::get('/income', [IncomeController::class, 'index'])->name('income.index');
    Route::get('/loyal-customers', [LoyalCustomerController::class, 'index'])->name('loyal-customers.index');
    Route::resource('gallons', GallonController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
