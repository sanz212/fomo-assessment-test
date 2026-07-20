<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleRaceConditionTest extends TestCase
{
    use RefreshDatabase;


    public function test_flash_sale_cannot_oversell_stock(): void
    {
        $product = Product::create([
            'name' => 'Flash Sale Laptop',
            'price' => 1000000,
            'stock' => 10,
        ]);


        $responses = [];

        for ($i = 0; $i < 20; $i++) {

            $responses[] = $this->postJson('/api/orders', [
                'email' => "buyer{$i}@test.com",
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                    ]
                ],
            ]);
        }


        $successCount = collect($responses)
            ->filter(fn ($response) => $response->status() === 201)
            ->count();


        $product->refresh();


        $this->assertEquals(10, $successCount);

        $this->assertEquals(0, $product->stock);

        $this->assertGreaterThanOrEqual(
            0,
            $product->stock
        );
    }
}