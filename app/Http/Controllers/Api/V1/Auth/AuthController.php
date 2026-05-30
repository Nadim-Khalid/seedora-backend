<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Controllers\Api\V1\BaseApiController;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseApiController
{
    public function register(RegisterRequest $request)
    {
        try{
            $user =User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'customer',
                'status' => 'active'
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse([
                'user' => $user,
                'token' => $token,
            ], 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError(
                'Registration failed',
                [$e->getMessage()], 500
            );
        }

    }

    public function login(LoginRequest $request)
    {
try{
    $user = User::where('email' , $request->email)->first();
    if(!$user || !Hash::check($request->password, $user->password))
        return $this->sendError(
    'Invalid Credentials',[],
        );
        if($user->status !=='active')
        {
            return $this->sendError(
                'User is not active',
                [],
            );
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->sendResponse([
            'user' => $user,
            'token' => $token,
        ], 'Login successful');

}
catch(\Exception $e)
{
    return $this->sendError(
        'login failed',
        [$e->getMessage()],
        500
    );
}
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->sendResponse([
            [],
            'LogOut Successfull'
        ]);
    }

    public function me()
    {
        return $this->sendResponse([
            auth()->user(),
            'User retrieved successfully'
        ]);
    }
}
