<?php

namespace Tests\Feature;

use Tests\TestCase;

class PortalViewTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_portal_landing_page_renders_successfully(): void
    {
        $response = $this->get('/portal');

        $response->assertStatus(200);
        $response->assertSee('Rootera');
        $response->assertSee('Fitur Cerdas Skala Enterprise');
        $response->assertSee('Log Aktivitas Presensi Terkini');
    }
}
