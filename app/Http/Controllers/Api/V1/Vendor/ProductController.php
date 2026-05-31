<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Vendor;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\Api\V1\ProductRequest;
use Str;
use App\Http\Requests\Api\V1\ProductImageRequest;
class ProductController extends BaseApiController
{
    public function store(ProductRequest $request)
    {
        $vendor = auth()->user()->vendor;
        $product = $vendor->products()->create([
            'vendor_id' => $vendor->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $request->slug,
            'sku' => $request->sku,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'status' => $request->status,
        ]);
        return $this->sendResponse($product,
        'Product created successfully');
    }
    public function index()
    {
        $vendor = auth()->user()->vendor();
        $products = Product::with([
            'category',
            'image'
        ])
        ->where('vendor_id',$vendor->id)
        ->latest()
        ->paginate(10);
        return $this->sendResponse($products,
        'Products retrieved successfully');
    }
    public function uploadImage(ProductImageRequest $request, Product $product)
    {
        $vendor = auth()->user()->vendor();
        if($product->vendor_id !==$vendor->id)
        {
            return $this->sendError('Unauthorized',[],403);
        }
        DB::beginTransaction();
        try{
            if($request->is_primary)
            {
                ProductImage::where('product_id',
                $product->id)->update(['is_primary'=>false]);
            }
            $path = $request->file('image')
            ->store('products',
            'public');
            $image = ProductImage::create([
                'product_id'=>$product->id,
                'image'=>$path,
                'is_primary'=>$request->is_primary?? false,
            ]);
            DB::commit();
            return $this->sendResponse($image,
            'Product image uploaded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error uploading image',
            [$e->getMessage()],500);
        }
    }
    public function show(Product $product)
    {
        $vendor = auth()->user()->vendor();
        if($product->vendor_id !==$vendor->id)
        {
            return $this->sendError('Unauthorized',[],403);
        }

        return $this->sendResponse
        ($product->load(['category','images']),
        'Products Fetched Successfully'
    );
    }
    public function update(ProductRequest $request, Product $product)
    {
        $vendor = auth()->user()->vendor();
        if($product->vendor_id !==$vendor->id)
        {
            return $this->sendError('Unauthorized',[],403);
        }
        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sku' => $request->sku,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'status' => $request->status,
        ]);
        return $this->sendResponse($product,
        'Product Update Successfully');
    }

    public function destroy(Product $product)
    {
        $vendor = auth()->user()->vendor();
        if($product->vendor_id !== $vendor->id)
        {
            return $this->sendError('Unauthorized', [],403);
        }
        $product->delete();
        return $this->sendResponse([],
        'Product Deleted Successfully');
    }
}
