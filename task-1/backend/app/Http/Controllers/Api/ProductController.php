<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->orderBy('id')
            ->get();
            
        return response()->json([
            'status'  => 'success',
            'message' => 'Products retrieved successfully',
            'data'    => ProductResource::collection($products),
        ], 200);
    }

    public function show(Product $productId): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Products retrieved successfully',
            'data'    => new ProductResource($productId),
        ], 200);
    }
}