<?php 
use App\Http\Controllers\Api\V1\AdminVendorController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\TestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\Concerns\TestCaches;
use App\Http\Controllers\Api\V1\Vendor\VendorController;

Route::prefix('v1')->group(function ()
{

    //Admin routes
    Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:admin'])
    ->group(function()
    {
        Route::get('/vendors' , [AdminVendorController::class, 'pendingVendors']);
        Route::patch('/vendors/{vendor}/approve', [AdminVendorController::class, 'approvedVendors']);
    });
    
    Route::get('/test', function ()
    {
        return response()->json([
            'success'=>true,
            'message'=>'Api is working fine'
        ], 200);
    });
    Route::get('test', [TestController::class, 'index']);
    Route::prefix('auth')->group(function()
    {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware('auth:sanctum')->group(function()
        {
            Route::post('/logout' , [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::prefix('vendor')->group(function()
    {
        Route::post('/register' , [VendorController::class,'register']);
    });

Route::middleware([
    'auth:sanctum',
    'role:admin'
])->group(function () {

    Route::get('/admin/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'welcome admin'
        ]);
    });

});

});