<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreReviewRequest;
use App\Http\Requests\Api\V1\UpdateReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends BaseApiController
{
public function store(StoreReviewRequest $request)
{
    $hasPurchased = Order::where(
            'user_id',
            auth()->id()
        )
        ->where(
            'order_status',
            'delivered'
        )
        ->whereHas(
            'items',
            function ($query) use ($request) {

                $query->where(
                    'product_id',
                    $request->product_id
                );

            }
        )
        ->exists();

    if (!$hasPurchased) {

        return $this->sendError(
            'Only verified purchasers can review this product'
        );

    }

    $exists = Review::where(
        'user_id',
        auth()->id()
    )
    ->where(
        'product_id',
        $request->product_id
    )
    ->exists();

    if ($exists) {

        return $this->sendError(
            'You have already reviewed this product'
        );

    }

    $review = Review::create([
        'user_id' => auth()->id(),
        'product_id' => $request->product_id,
        'rating' => $request->rating,
        'review' => $request->review,
    ]);

    return $this->sendResponse(
        $review,
        'Review Added Successfully'
    );
}
public function productReviews($productId)
{
    $product = Product::find($productId);

    if (!$product) {
        return $this->sendError(
            'Product Not Found'
        );
    }

    $reviews = Review::with([
            'user:id,name'
        ])
        ->where(
            'product_id',
            $productId
        )
        ->latest()
        ->paginate(10);

    $averageRating = Review::where(
            'product_id',
            $productId
        )
        ->avg('rating');

    $totalReviews = Review::where(
            'product_id',
            $productId
        )
        ->count();

    return $this->sendResponse([
        'average_rating' => round(
            $averageRating ?? 0,
            1
        ),

        'total_reviews' => $totalReviews,

        'reviews' => $reviews,
    ], 'Reviews Fetched Successfully');
}

public function update(
    UpdateReviewRequest $request,
    $id
)
{
    $review = Review::where(
        'user_id',
        auth()->id()
    )->find($id);

    if (!$review) {

        return $this->sendError(
            'Review Not Found'
        );

    }

    $review->update([
        'rating' => $request->rating,
        'review' => $request->review,
    ]);

    return $this->sendResponse(
        $review,
        'Review Updated Successfully'
    );
}
public function destroy($id)
{
    $review = Review::where(
        'user_id',
        auth()->id()
    )->find($id);

    if (!$review) {

        return $this->sendError(
            'Review Not Found'
        );

    }

    $review->delete();

    return $this->sendResponse(
        [],
        'Review Deleted Successfully'
    );
}
}
