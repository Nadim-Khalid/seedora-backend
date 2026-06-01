<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            $orders,
            'orders fetched successfully'
        );
    }

    public function show(Request $request, $id)
    {
        $order = Order::with([
              'id',
        'order_number',
        'total_amount',
        'payment_status',
        'order_status',
        'created_at',
        'items.product',
        ])
        ->where('user_id', $request->user()->id)
        ->findOrFail($id);
        if(!$order)
        {
            return $this->sendError(
                'Order nor found'
            );
        }

        return $this->sendResponse(
            $order,
            'order fetched successfully'
        );
    }
}