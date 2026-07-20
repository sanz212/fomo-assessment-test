<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'discount_percentage',
        'discount_price',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock' => 'integer',
    ];


    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted()
    {
        static::saving(function ($product) {

            if ($product->discount_percentage > 0) {

                $product->discount_price =
                    $product->price -
                    ($product->price * ($product->discount_percentage / 100));

            } else {

                $product->discount_price = null;

            }

        });
    }
}
