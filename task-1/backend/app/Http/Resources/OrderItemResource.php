<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id'    => $this->product_id,
            'product_name'  => $this->product->name,
            'quantity'      => $this->quantity,
            'unit_price'    => $this->price,
            'subtotal'      => $this->price * $this->quantity,
        ];
    }
}