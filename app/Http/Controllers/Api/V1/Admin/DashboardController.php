<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;

class DashboardController extends BaseApiController
{
    public function index()
    {
        $data = [
            'total_users' => User::count(),

            'total_vendors' => Vendor::count(),

            'total_products' => Product::count(),

            'total_orders' => Order::count(),

            'total_revenue' => Order::where(
                'payment_status',
                'paid'
            )->sum('total_amount'),
        ];

        return $this->sendResponse(
            $data,
            'Dashboard Data Fetched Successfully'
        );
    }
}