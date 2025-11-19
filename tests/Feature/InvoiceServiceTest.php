<?php

namespace Tests\Unit;

use App\Models\RentalSession;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_combined_invoice()
    {
        $session = RentalSession::factory()->create(['total_cost' => 30000]);
        $order = Order::factory()->create(['total' => 20000]);

        $service = new InvoiceService();
        $invoice = $service->createCombinedInvoice($session, $order);

        $this->assertEquals(50000, $invoice->subtotal);
        $this->assertEquals(5000, $invoice->tax); // 10%
        $this->assertEquals(55000, $invoice->total);
        $this->assertEquals(30000, $invoice->console_charges);
        $this->assertEquals(20000, $invoice->food_charges);
    }
}
