<?php

namespace App\Http\Controllers\Api\V1;
use App\Models\Vendor;
use App\Http\Controllers\Api\V1\BaseApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminVendorController extends BaseApiController
{
    public function pendingVendors()
    {
        $vendors = Vendor::with('user')
        ->where('approval_status', 'pending')
        ->latest()
        ->get();

        return $this->sendResponse($vendors,
        'pending vendors fetched successfully');
    }

    public function approvedVendors(Vendor $vendor)
    {
        $vendor->update([
            'approval_status' => 'approved',
            'approved_at' => now()
        ]);
        $vendor->user()->update([
            'status'=>'active',
        ]);
        return $this->sendResponse($vendor->load('user'),
        'approved vendor successfully');

    }
}
