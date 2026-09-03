<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_login_and_open_the_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@abcn.test',
            'password' => Hash::make('strong-password'),
            'role' => 'admin',
        ]);

        $login = $this->postJson('/api/v1/admin/login', [
            'email' => 'admin@abcn.test',
            'password' => 'strong-password',
        ])->assertOk()->assertJsonStructure(['token', 'user']);

        $token = $login->json('token');

        $this->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk()
            ->assertJsonStructure(['counts', 'requests']);
    }

    public function test_dashboard_rejects_an_invalid_token(): void
    {
        $this->withToken('invalid-token')
            ->getJson('/api/v1/admin/dashboard')
            ->assertUnauthorized();
    }
}
