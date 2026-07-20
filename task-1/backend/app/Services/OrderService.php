<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $products = $this->lockProducts($data['items']);

            $order = Order::create([
                'email'  => $data['email'],
                'total'  => $this->calculateTotal($products),
                'status' => 'completed',
            ]);

            $this->createOrderItems($order, $products);

            return $order->load('items');
        });
    }

    /**
     * Lock product rows and validate stock.
     */
    private function lockProducts(array $items): array
    {
        $products = [];

        foreach ($items as $item) {

            $product = Product::lockForUpdate()
                ->find($item['product_id']);

            if ($product->stock < $item['quantity']) {
                throw new InsufficientStockException(
                    "Insufficient stock for {$product->name}"
                );
            }

            $products[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
            ];
        }

        return $products;
    }

    /**
     * Calculate order total.
     */
    private function calculateTotal(array $products): float
    {
        $total = 0;

        foreach ($products as $item) {
            $total += $this->getSellingPrice($item['product']) * $item['quantity'];
        }

        return $total;
    }

    /**
     * Create order items and reduce stock.
     */
    private function createOrderItems(Order $order, array $products): void
    {
        foreach ($products as $item) {

            $product = $item['product'];
            $quantity = $item['quantity'];

            $order->items()->create([
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'price'      => $this->getSellingPrice($product),
            ]);

            $product->decrement('stock', $quantity);
        }
    }

    /**
     * Get current selling price.
     */
    private function getSellingPrice(Product $product): float
    {
        return $product->discount_price ?? $product->price;
    }
}