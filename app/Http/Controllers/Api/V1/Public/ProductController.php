<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    public function index()
    {
        $products = Product::with([
            'category',
            'vendor',
            'image',
        ])
        ->where('status',true)
        ->latest()
        ->paginate(12);

        return $this->sendResponse(
            $products,
            'Products Fetched Successfully'
        );
    }
    public function show($slug)
    {
        $product = Product::with([
            'category',
            'vendor',
            "image"
        ])
        ->where('slug',$slug)
        ->first();
        if(!$product)
        {
            return $this->sendError(
            'Product not found'
            );
        }
        return $this->sendResponse(
            $product,
            'Product Fetched Successfully'
        );
    }
}
