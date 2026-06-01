<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{
public function index(Request $request)
{
    $vendor = $request->user()->vendor;

    $orders = Order::select(
            'id',
            'user_id',
            'order_number',
            'total_amount',
            'payment_status',
            'order_status',
            'created_at'
        )
        ->with([
            'user:id,name'
        ])
        ->whereHas(
            'items.product',
            function ($query) use ($vendor) {

                $query->where(
                    'vendor_id',
                    $vendor->id
                );

            }
        )
        ->latest()
        ->paginate(10);

    return $this->sendResponse(
        $orders,
        'Orders Fetched Successfully'
    );
}

public function show(Request $request, $id)
{
    $vendor = $request->user()->vendor;

    $order = Order::with([
        'user:id,name,email',
        'items.product'
    ])
    ->whereHas(
        'items.product',
        function ($query) use ($vendor) {
            $query->where(
                'vendor_id',
                $vendor->id
            );
        }
    )
    ->find($id);

    if (!$order) {
        return $this->sendError(
            'Order Not Found'
        );
    }

    $order->setRelation(
        'items',
        $order->items->filter(
            function ($item) use ($vendor) {

                return $item->product &&
                    $item->product->vendor_id === $vendor->id;

            }
        )->values()
    );

    return $this->sendResponse(
        $order,
        'Order Fetched Successfully'
    );
}
}
