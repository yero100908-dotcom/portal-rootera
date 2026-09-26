<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamAuthenticationMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test login one-by-one for all email credentials in the matrix.
     */
    public function test_all_team_members_can_login_by_email(): void
    {
        $credentials = [
            'wibowo@smartattend.com',
            'rafael@smartattend.com',
            'mono@smartattend.com',
            'yero@smartattend.com',
            'desty@smartattend.com',
            'prayoga@smartattend.com',
            'maura@smartattend.com',
            'rafa@smartattend.com',
            'ghaitsaa@smartattend.com',
        ];

        foreach ($credentials as $email) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'password123',
            ]);

            $this->assertAuthenticated();
            $response->assertRedirect(route('dashboard', absolute: false));

            $this->post('/logout');
            $this->assertGuest();
        }
    }

    /**
     * Test login one-by-one using username prefixes.
     */
    public function test_all_team_members_can_login_by_username(): void
    {
        $usernames = [
            'wibowo',
            'rafael',
            'mono',
            'yero',
            'desty',
            'prayoga',
            'maura',
            'rafa',
            'ghaitsaa',
        ];

        foreach ($usernames as $username) {
            $response = $this->post('/login', [
                'email' => $username,
                'password' => 'password123',
            ]);

            $this->assertAuthenticated();
            $response->assertRedirect(route('dashboard', absolute: false));

            $this->post('/logout');
            $this->assertGuest();
        }
    }

    /**
     * Test login using role alias shortcuts.
     */
    public function test_role_aliases_login_successfully(): void
    {
        $roleAliases = ['owner', 'admin', 'mentor', 'it', 'sosmed', 'cs'];

        foreach ($roleAliases as $role) {
            $response = $this->post('/login', [
                'email' => $role,
                'password' => 'password123',
            ]);

            $this->assertAuthenticated();
            $response->assertRedirect(route('dashboard', absolute: false));

            $this->post('/logout');
            $this->assertGuest();
        }
    }

    /**
     * Test login using legacy email credentials.
     */
    public function test_legacy_emails_login_successfully(): void
    {
        $legacyEmails = [
            'owner@absensi.com',
            'admin@absensi.com',
            'mentor@absensi.com',
            'karyawan@absensi.com',
        ];

        foreach ($legacyEmails as $email) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'password123',
            ]);

            $this->assertAuthenticated();
            $response->assertRedirect(route('dashboard', absolute: false));

            $this->post('/logout');
            $this->assertGuest();
        }
    }

    /**
     * Test Role-Based Access Control (RBAC) route permissions for each role.
     */
    public function test_rbac_route_permissions_per_division(): void
    {
        // 1. CS Staff (Ghaitsaa) -> Can access orders, cannot access finance
        $cs = User::where('email', 'ghaitsaa@smartattend.com')->first();
        $this->actingAs($cs)->get(route('orders.index'))->assertStatus(200);
        $this->actingAs($cs)->get(route('finance.index'))->assertStatus(403);

        // 2. Sosmed Staff (Prayoga) -> Can access content calendar, cannot access orders
        $sosmed = User::where('email', 'prayoga@smartattend.com')->first();
        $this->actingAs($sosmed)->get(route('content.index'))->assertStatus(200);
        $this->actingAs($sosmed)->get(route('orders.index'))->assertStatus(403);

        // 3. IT Staff (Yero) -> Can access audit logs and user management, cannot access finance
        $it = User::where('email', 'yero@smartattend.com')->first();
        $this->actingAs($it)->get(route('audit_logs.index'))->assertStatus(200);
        $this->actingAs($it)->get(route('users.index'))->assertStatus(200);
        $this->actingAs($it)->get(route('finance.index'))->assertStatus(403);

        // 4. Admin Staff (Mono) -> Can access finance, orders, users, content
        $admin = User::where('email', 'mono@smartattend.com')->first();
        $this->actingAs($admin)->get(route('finance.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('orders.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('finance.profit_loss'))->assertStatus(403); // Owner only

        // 5. Owner (Wibowo) -> Can access everything including profit_loss
        $owner = User::where('email', 'wibowo@smartattend.com')->first();
        $this->actingAs($owner)->get(route('finance.profit_loss'))->assertStatus(200);
        $this->actingAs($owner)->get(route('audit_logs.index'))->assertStatus(200);
    }

    /**
     * Test Maura Aurellia has extended access to finance (pembukuan & kas), orders, and content.
     */
    public function test_maura_has_extended_finance_orders_and_content_access(): void
    {
        $maura = User::where('email', 'maura@smartattend.com')->first();

        $this->assertTrue($maura->canManageFinance(), 'Maura should be able to manage finance (pembukuan & kas).');
        $this->assertTrue($maura->canManageOrders(), 'Maura should be able to manage orders.');
        $this->assertTrue($maura->canManageContent(), 'Maura should be able to manage content.');

        $this->actingAs($maura)->get(route('finance.index'))->assertStatus(200);
        $this->actingAs($maura)->get(route('orders.index'))->assertStatus(200);
        $this->actingAs($maura)->get(route('content.index'))->assertStatus(200);
    }
}
