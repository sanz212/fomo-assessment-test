<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {
    }


    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {

            $order = $this->orderService->createOrder(
                $request->validated()
            );


            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order,
            ], 201);


        } catch (RuntimeException $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 409);


        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}