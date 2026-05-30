<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use Faker\Provider\Base;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\VendorRegisterRequest;
use App\Models\User;
use App\Models\Vendor;



class VendorController extends BaseApiController
{
    
    public function register(VendorRegisterRequest $request)
    {
        
        try{
            $data = DB::transaction(function() use($request)
            {
                $user = User::create([
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'password'=>Hash::make($request->password),
                    'role'=>'vendor',
                    'status'=>'pending',
                ]);

                $vendor = Vendor::create([
                    'user_id'=>$user->id,
                    'store_name'=>$request->store_name,
                    'store_description'=>$request->store_description,
                    'address'=>$request->address,
                    'phone'=>$request->phone,
                ]);
                return[
                    'user'=>$user,
                    'vendor'=>$vendor
                ];

            });
            return $this->sendResponse($data,'vendor registration submitted successfully');

        }
        catch(\Exception $e)
        {
            return $this->sendError('vendor registration failed',[$e->getMessage()],500);
        }
    }
}
