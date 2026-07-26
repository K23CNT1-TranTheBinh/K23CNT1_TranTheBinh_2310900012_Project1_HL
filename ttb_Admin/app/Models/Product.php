<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'brand_id',
        'price',
        'sale_price',
        'stock',
        'description',
        'short_desc',
        'image',
        'images',
        'specs',
        'is_featured',
        'is_new',
        'views',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(
            Category::class,
            'category_id'
        );
    }

    public function brand()
    {
        return $this->belongsTo(
            Brand::class,
            'brand_id'
        );
    }
}