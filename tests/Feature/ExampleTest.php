<?php

namespace Tests\Feature;

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
}
