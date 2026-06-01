<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{
    public function index(Request $request)
    {
$query = Order::query();
if($request->filled('status'))
{
    $query->where('orders_status', $request->status);
}
$orders = $query
    ->with('user:id,name,email')
    ->latest()
    ->paginate(10);
return $this->sendResponse(
    $orders,
    'orders fetched successfully'
);
    }
public function updateStatus(UpdateOrderStatusRequest $request, $id)
{
    $order = Order::find($id);

    if (!$order) {
        return $this->sendError(
            'Order Not Found'
        );
    }

    $allowedTransitions = [
        'pending' => [
            'processing',
            'cancelled'
        ],

        'processing' => [
            'shipped',
            'cancelled'
        ],

        'shipped' => [
            'delivered'
        ],

        'delivered' => [],

        'cancelled' => [],
    ];

    $currentStatus = $order->order_status;

    $newStatus = $request->order_status;

    if (
        !in_array(
            $newStatus,
            $allowedTransitions[$currentStatus]
        )
    ) {
        return $this->sendError(
            'Invalid Status Transition'
        );
    }

    $order->update([
        'order_status' => $newStatus
    ]);

    return $this->sendResponse(
        $order,
        'Order Status Updated Successfully'
    );
}
}
