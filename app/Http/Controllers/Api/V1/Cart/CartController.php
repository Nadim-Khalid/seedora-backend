<?php

namespace App\Http\Controllers\Api\V1\Cart;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckoutRequest;
use App\Http\Requests\Api\V1\UpdateCartItemRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\V1\AddToCartRequest;


class CartController extends BaseApiController
{
    public function addToCart(AddToCartRequest $request)
    {
        $user = auth()->user();
        $cart = Cart::firstOrCreate([
            'user_id'=>$user->id 
        ]);

        $product = Product::findOrFail(
            $request->product_id
        );

        $cartItem = CartItem::where(
            'cart_id',
            $cart->id
        )
        ->where(
            'product_id',
            $product->id
        )
        ->first();
        if($cartItem)
        {
            $cartItem->increment(
            'quantity',
            $request->quantity
        );
        }
        else{

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->price,
        ]);
    }
     return $this->sendResponse(
        [],
        'Product added to cart successfully'
    );
  
    }
      public function viewCart()
    {
        $user = auth()->user();
        $cart = Cart::with([
             'items.product:id,name,slug,price',
    'items.product.images:id,product_id,image,is_primary'
        ])
        ->where('user_id',$user->id)
->first();

if(!$cart)
{
    return $this->sendError(
        [],
        'Cart Is Empty'
    );
}
$total = 0;
$total = $cart->items->map(function ($item)
{
    return $item->price * $item->quantity;
})->sum();

return $this->sendResponse([
    'cart'=>$cart,
    'total'=>$total
],'Cart Fetched Successfully');

    }

    public function updateCartItem(UpdateCartItemRequest $request,$id)
    {
        $user= auth()->user();
        $cart = Cart::where("user_id",$user->id)
        ->first();
        if(!$cart)
        {
            return $this->sendError("Cart Not Found");
        }
        $cartItem = CartItem::where('cart_id',$cart->id)
        ->where('id',$id)
        ->first();

        if(!$cartItem)
        {
            return $this->sendError('Cart Item Not Found');
        }
        if ($request->quantity > $cartItem->product->stock)
{
    return $this->sendError(
        'Insufficient stock'
    );
}
        $cartItem->update([
            'quantity'=>$request->quantity
        ]);
        return $this->sendResponse($cartItem,
        'Cart Item Updated Successfully');
    }
   public function removeCartItem($id)
{
    $user = auth()->user();

    $cart = Cart::where(
        'user_id',
        $user->id
    )->first();

    if (!$cart) {

        return $this->sendError(
            'Cart not found'
        );
    }

    $cartItem = CartItem::where(
        'cart_id',
        $cart->id
    )
    ->where(
        'id',
        $id
    )
    ->first();

    if (!$cartItem) {

        return $this->sendError(
            'Cart item not found'
        );
    }

    $cartItem->delete();

    return $this->sendResponse(
        [],
        'Cart item removed successfully'
    );
}

public function clearCart()
{
    $user = auth()->user();

    $cart = Cart::where(
        'user_id',
        $user->id
    )->first();

    if (!$cart) {

        return $this->sendError(
            'Cart not found'
        );
    }

    $cart->items()->delete();

    return $this->sendResponse(
        [],
        'Cart cleared successfully'
    );
}

public function checkout(CheckoutRequest $request)
{
    try{
        $user= auth()->user();
        DB::transaction(function()use(
            $user,
            $request,
           & $order,
        )
        {
            $cart= Cart::with('items.product')
            ->where('user_id',$user->id)
            ->first();
            if(!$cart || $cart->items->isEmpty())
                {
                    throw new \Exception('cart is empty');
                }
                $totalAmount = 0;
                foreach($cart->items as $item)
                {
                    $product=Product::lockForUpdate()
                    ->find($item->product_id);
                    if($product->stock < $item->quantity)
                    {
                        throw new \Exception($product->name.'is out of stock');
                    }
                    $totalAmount += $item->price * $item->quantity; 
                }
                $order = Order::create([
                    'user_id'=>$user->id,
                    'order_number'=>'ORD'.time(),
                    'total_amount'=>$totalAmount,
                    'payment_method'=>$request->payment_method,
                    'payment_status'=>$request->payment_method==='cod'?'pending':'pending',
                    'order_status'=>'pending',
                    'customer_name'=>$request->customer_name,
                    'customer_phone'=>$request->customer_phone,
                    'shipping_address'=>$request->shipping_address,
                ]);
                foreach($cart->items as $item)
                {
                    OrderItem::create([
                        'order_id'=>$order->id,
                        'product_id'=>$item->product_id,
                        'quantity'=>$item->quantity,
                        'price'=>$item->price,
                    ]);
                    Product::where(
                        'id',
                        $item->product_id
                    )->decrement('stock',
                    $item->quantity);
                }
                $cart->items()->delete();

        });
        return $this->sendResponse(
            $order,
            'Order Placed Successfully'
        );
    }
    catch(\Exception $e)
    {
        return $this->sendError($e->getMessage(),
        [],
        500);
    }
}
}
