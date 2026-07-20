<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'email'        => $this->email,
            'order_items'  => OrderItemResource::collection(
                $this->whenLoaded('items')
            ),
            'status'       => $this->status,
            'total'        => $this->total,
            'created_at'   => $this->created_at,
        ];
    }
}