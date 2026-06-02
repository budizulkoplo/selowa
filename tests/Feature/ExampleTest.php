<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_login_page_returns_a_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_owner_can_open_core_selowa_pages(): void
    {
        $this->seed();

        $user = User::where('email', 'owner@selowa.local')->firstOrFail();

        foreach (['/', '/profile', '/company', '/customers', '/customers-bulk-price', '/customer-addresses', '/transactions', '/delivery-runs', '/income', '/loyal-customers', '/gallons', '/reports'] as $uri) {
            $this->actingAs($user)->get($uri)->assertStatus(200);
        }
    }

    public function test_admin_can_only_edit_own_today_transactions_and_cannot_delete(): void
    {
        $this->seed();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-test@selowa.local',
            'password' => 'password',
            'is_active' => true,
        ]);
        $admin->assignRole($adminRole);
        $otherAdmin = User::create([
            'name' => 'Admin Lain',
            'email' => 'admin-other@selowa.local',
            'password' => 'password',
            'is_active' => true,
        ]);
        $otherAdmin->assignRole($adminRole);

        $customer = Customer::create(['name' => 'Pelanggan Test', 'customer_price' => 10000, 'is_active' => true]);

        $todayTransaction = Transaction::create([
            'transaction_code' => 'TRX-TODAY',
            'customer_id' => $customer->id,
            'qty' => 1,
            'price' => 10000,
            'status' => 1,
            'created_at' => now(),
            'created_by' => $admin->id,
        ]);

        $otherAdminTransaction = Transaction::create([
            'transaction_code' => 'TRX-OTHER',
            'customer_id' => $customer->id,
            'qty' => 1,
            'price' => 10000,
            'status' => 1,
            'created_at' => now(),
            'created_by' => $otherAdmin->id,
        ]);

        $oldTransaction = Transaction::create([
            'transaction_code' => 'TRX-OLD',
            'customer_id' => $customer->id,
            'qty' => 1,
            'price' => 10000,
            'status' => 1,
            'created_at' => now()->subDay(),
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->get(route('transactions.edit', $todayTransaction))->assertStatus(200);
        $this->actingAs($admin)->get(route('transactions.edit', $otherAdminTransaction))->assertStatus(403);
        $this->actingAs($admin)->get(route('transactions.edit', $oldTransaction))->assertStatus(403);
        $this->actingAs($admin)->delete(route('transactions.destroy', $todayTransaction))->assertStatus(403);
    }
}
