<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends BaseApiController
{
    public function index(Request $request)
    {
        $vendor = $request->user()->vendor;

        $totalProducts = Product::where(
            'vendor_id',
            $vendor->id
        )->count();

        $totalOrders = Order::whereHas(
            'items.product',
            fn($query) => $query->where(
                'vendor_id',
                $vendor->id
            )
        )->count();

        $pendingOrders = Order::whereHas(
            'items.product',
            fn($query) => $query->where(
                'vendor_id',
                $vendor->id
            )
        )
        ->where(
            'order_status',
            'pending'
        )
        ->count();

        $totalSales = OrderItem::whereHas(
            'product',
            fn($query) => $query->where(
                'vendor_id',
                $vendor->id
            )
        )
        ->selectRaw(
            'SUM(quantity * price) as total_sales'
        )
        ->value('total_sales') ?? 0;

        return $this->sendResponse([
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'total_sales' => $totalSales,
        ], 'Dashboard Data Fetched Successfully');
    }
}