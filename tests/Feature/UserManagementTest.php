<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_owner_can_view_users_index_page(): void
    {
        $owner = User::where('role', 'owner')->first();

        $response = $this->actingAs($owner)->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen User');
        $response->assertSee('Semua Role');
    }

    public function test_owner_can_filter_users_by_role(): void
    {
        $owner = User::where('role', 'owner')->first();

        $response = $this->actingAs($owner)->get(route('users.index', ['role' => 'admin']));

        $response->assertStatus(200);
        $response->assertSee('Ghaitsaa Kahla');
    }
}
