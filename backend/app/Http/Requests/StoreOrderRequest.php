<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'items.required' => 'Order must contain at least one item.',

            'items.min' => 'Order must contain at least one item.',

            'items.*.product_id.exists' => 'Selected product does not exist.',

            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}