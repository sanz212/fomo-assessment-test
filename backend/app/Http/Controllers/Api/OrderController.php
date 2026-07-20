<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\ShowOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {
    }


    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->validated()
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Order created successfully',
            'data' => new OrderResource(
                $order->load('items.product')
            ),
        ], 201);
        
    }

    public function show(ShowOrderRequest $request, int $orderId): JsonResponse
    {
        $order = Order::with('items.product')
            ->whereKey($orderId)
            ->where('email', $request->validated('email'))
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'message' => 'Order retrieved successfully',
            'data' => new OrderResource($order),
        ], 200);
    }
}