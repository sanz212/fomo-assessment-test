<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $total = 0;
            $products = [];

            foreach ($data['items'] as $item) {

                /**
                 * Lock product row
                 * Prevent race condition during flash sale
                 */
                $product = Product::lockForUpdate()
                    ->find($item['product_id']);

                if (!$product) {
                    throw new RuntimeException(
                        'Product not found'
                    );
                }


                if ($product->stock < $item['quantity']) {
                    throw new RuntimeException(
                        "Insufficient stock for {$product->name}"
                    );
                }


                $products[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                ];


                $total += $product->price * $item['quantity'];
            }


            /**
             * Create order
             */
            $order = Order::create([
                'email' => $data['email'],
                'total' => $total,
                'status' => 'completed',
            ]);


            /**
             * Create order items
             * And reduce inventory
             */
            foreach ($products as $item) {

                $product = $item['product'];
                $quantity = $item['quantity'];


                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->discount_price ?? $product->price,
                ]);


                $product->decrement(
                    'stock',
                    $quantity
                );
            }


            return $order->load('items.product');
        });
    }
}