<?php 
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\AdminVendorController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Cart\CartController;
use App\Http\Controllers\Api\V1\TestController;
use App\Http\Controllers\Api\V1\Vendor\ProductController;
use App\Models\Order;
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
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });

    //Vendor routes
    Route::prefix('vendor')
    ->middleware(['auth:sanctum', 'role:vendor'])
    ->group(function()
    {
        Route::get('/products', [VendorController::class, 'index']);
        Route::post('/products', [VendorController::class, 'store']);
        Route::get('/products/{product}', [VendorController::class, 'show']);
        Route::put('/products/{product}', [VendorController::class, 'update']);
        Route::delete('/products/{product}', [VendorController::class, 'destroy']);
        Route::post('/products/{product}/images', [ProductController::class, 'uploadImage']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class,'show']);
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

Route::prefix('public')->group(function()
{
    Route::get('/categories',[CategoryController::class, 'index']);
    Route::get('/products',[ProductController::class, 'index']);
    Route::get('/products/{slug}',[ProductController::class,'show']);


    Route::middleware('auth:sanctum')->group(function()
    {
        Route::post('/cart/add', [CartController::class,'addToCart']);
        Route::put('/cart/item/{id}',[CartController::class,'updateCartItem']);
        Route::delete('/cart/item/{id}', [CartController::class, 'removeCartItem']);
        Route::delete('/cart/clear',[CartController::class, 'clearCart']);
        Route::post('/checkout', [CartController::class, 'checkout']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}',[OrderController::class, 'show']);
    });


});