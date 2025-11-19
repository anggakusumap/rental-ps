<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FoodItem;
use App\Models\FoodCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($this->user);
    }

    public function test_can_create_order()
    {
        $category = FoodCategory::factory()->create();
        $foodItem = FoodItem::factory()->create([
            'food_category_id' => $category->id,
            'price' => 10000,
            'stock' => 100,
        ]);

        $response = $this->post(route('orders.store'), [
            'customer_name' => 'Jane Doe',
            'items' => [
                [
                    'food_item_id' => $foodItem->id,
                    'quantity' => 2,
                ]
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Jane Doe',
            'subtotal' => 20000,
            'status' => 'pending',
        ]);

        $foodItem->refresh();
        $this->assertEquals(98, $foodItem->stock);
    }

    public function test_order_fails_with_insufficient_stock()
    {
        $category = FoodCategory::factory()->create();
        $foodItem = FoodItem::factory()->create([
            'food_category_id' => $category->id,
            'stock' => 1,
        ]);

        $response = $this->post(route('orders.store'), [
            'items' => [
                [
                    'food_item_id' => $foodItem->id,
                    'quantity' => 5,
                ]
            ]
        ]);

        $response->assertSessionHas('error');
    }
}
