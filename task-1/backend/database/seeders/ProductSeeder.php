<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Kacamata Super',
            'price' => 1200000,
            'discount_percentage' => 50,
            'stock' => 100,
        ]);

        Product::create([
            'name' => 'Kaos Membership',
            'price' => 375000,
            'discount_percentage' => 30,
            'stock' => 100,
        ]);

        Product::create([
            'name' => 'LAPTOP ASUS ROG GAMING',
            'price' => 70500000,
            'discount_percentage' => 20,
            'stock' => 10,
        ]);
    }
}