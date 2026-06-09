<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\Api\V1\ChangePasswordRequest;
use App\Http\Requests\Api\V1\UpdateProfileRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends BaseApiController
{
    /**
     * Get Logged In User Profile
     */
    public function profile(Request $request)
    {
        return $this->sendResponse(
            $request->user(),
            'Profile Fetched Successfully'
        );
    }

    /**
     * Update Profile
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return $this->sendResponse(
            $user->fresh(),
            'Profile Updated Successfully'
        );
    }

    /**
     * Change Password
     */
    public function changePassword(
        ChangePasswordRequest $request
    )
    {
        $user = $request->user();

        if (!Hash::check(
            $request->current_password,
            $user->password
        )) {
            return $this->sendError(
                'Current Password Is Incorrect'
            );
        }

        $user->update([
            'password' => Hash::make(
                $request->password
            )
        ]);

        return $this->sendResponse(
            [],
            'Password Changed Successfully'
        );
    }

    /**
     * Customer Dashboard
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $totalOrders = Order::where(
            'user_id',
            $user->id
        )->count();

        $pendingOrders = Order::where(
            'user_id',
            $user->id
        )
        ->where(
            'order_status',
            'pending'
        )
        ->count();

        $completedOrders = Order::where(
            'user_id',
            $user->id
        )
        ->where(
            'order_status',
            'delivered'
        )
        ->count();

        $cancelledOrders = Order::where(
            'user_id',
            $user->id
        )
        ->where(
            'order_status',
            'cancelled'
        )
        ->count();

        return $this->sendResponse([
            'total_orders'      => $totalOrders,
            'pending_orders'    => $pendingOrders,
            'completed_orders'  => $completedOrders,
            'cancelled_orders'  => $cancelledOrders,
        ], 'Dashboard Data Fetched Successfully');
    }
}