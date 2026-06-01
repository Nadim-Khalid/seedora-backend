<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseApiController
{
    public function index()
    {
        $categories = Category::where('status',true)
        ->latest()
        ->get();

        return $this->sendResponse(
            $categories,
            'Categories fetched successfully'
        );
    }
}
