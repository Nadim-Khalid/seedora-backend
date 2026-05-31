<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Str;

class CategoryController extends BaseApiController
{

    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return $this->sendResponse($categories,
        'categories fetch successfully');
    }
    public function store(CategoryRequest $request)
    {
$category = Category::create([
    'name'=>$request->name,
    'slug'=>\Str::slug($request->name),
    'description'=>$request->description,
    'status'=>$request->status ?? true,
]);
return $this->sendResponse(
    $category,
    'category created successfully'
);
    }
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update([
            'name' =>$request->name,
            'slug'=>Str::slug($request->name),
            'description'=>$request->description,
            'status'=>$request->status ?? true,
        ]);
        return $this->sendResponse(
            $category,
            'category updated successfully'
        );
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return $this->sendResponse([],
        'category deleted successfully'
        );

    }

    public function show(Category $category)
    {
        return $this->sendResponse(
            $category,
            'category details fetched successfully'
        );
    }
}
