<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class OrderRecapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_owner_can_view_order_recap_page(): void
    {
        $owner = User::where('role', 'owner')->first();

        $response = $this->actingAs($owner)->get(route('orders.recap'));

        $response->assertStatus(200);
        $response->assertSee('Rekap Order Bulanan');
    }

    public function test_order_recap_filters_correctly(): void
    {
        $owner = User::where('role', 'owner')->first();

        Order::create([
            'order_number' => 'ORD-TEST-001',
            'client_name' => 'PT Klien A',
            'lead_source' => 'Instagram',
            'details' => 'Pembuatan Website',
            'total_amount' => 5000000,
            'commission_rate' => 10,
            'commission_amount' => 500000,
            'transaction_date' => now()->format('Y-m-d'),
            'payment_status' => 'lunas',
            'created_by' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->get(route('orders.recap', [
            'month' => date('m'),
            'year' => date('Y'),
            'lead_source' => 'Instagram',
        ]));

        $response->assertStatus(200);
        $response->assertSee('PT Klien A');
        $response->assertSee('Instagram');
        $response->assertSee('5.000.000');
    }

    public function test_owner_can_download_excel_recap(): void
    {
        Excel::fake();

        $owner = User::where('role', 'owner')->first();

        $response = $this->actingAs($owner)->get(route('orders.recap.export', [
            'month' => date('m'),
            'year' => date('Y'),
        ]));

        $response->assertStatus(200);
        Excel::assertDownloaded('order-rekap-'.date('m').'-'.date('Y').'.xlsx');
    }
}
