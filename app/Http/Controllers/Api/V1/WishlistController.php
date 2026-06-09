<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\addWishlistRequest;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends BaseApiController
{
    public function index()
{
    $wishlist = Wishlist::with([
        'product.images',
        'product.category'
    ])
    ->where(
        'user_id',
        auth()->id()
    )
    ->latest()
    ->get();

    return $this->sendResponse(
        $wishlist,
        'Wishlist Fetched Successfully'
    );
}
   public function store(addWishlistRequest $request)
   {
    $wishlist = Wishlist::firstOrCreate([
        'user_id' => auth()->id(),
        'product_id' => $request->product_id
    ]);
    return $this->sendResponse(
        $wishlist,
        'Product added to wishlist successfully'
    );
   }

   public function destroy($id)
{
    $wishlist = Wishlist::where(
        'user_id',
        auth()->id()
    )
    ->find($id);

    if (!$wishlist) {
        return $this->sendError(
            'Wishlist Item Not Found'
        );
    }

    $wishlist->delete();

    return $this->sendResponse(
        [],
        'Wishlist Item Removed Successfully'
    );
}

}
