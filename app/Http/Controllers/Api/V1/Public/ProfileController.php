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

    $stats = Order::where(
            'user_id',
            $user->id
        )
        ->selectRaw('
            COUNT(*) as total_orders,

            SUM(
                CASE
                    WHEN order_status = "pending"
                    THEN 1
                    ELSE 0
                END
            ) as pending_orders,

            SUM(
                CASE
                    WHEN order_status = "delivered"
                    THEN 1
                    ELSE 0
                END
            ) as completed_orders,

            SUM(
                CASE
                    WHEN order_status = "cancelled"
                    THEN 1
                    ELSE 0
                END
            ) as cancelled_orders
        ')
        ->first();

return $this->sendResponse([
    'total_orders' => (int) $stats->total_orders,
    'pending_orders' => (int) $stats->pending_orders,
    'completed_orders' => (int) $stats->completed_orders,
    'cancelled_orders' => (int) $stats->cancelled_orders,
], 'Dashboard Data Fetched Successfully');
}
}

