<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
    'vendor_id',
    'category_id',
    'name',
    'slug',
    'sku',
    'short_description',
    'description',
    'price',
    'sale_price',
    'stock',
    'status',
];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);

    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
