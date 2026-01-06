<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products'; // optional (Laravel tự hiểu)
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'brand_id',
        'category_id',
        'product_name',
        'url_image',
        'public_url_image',
        'description',
        'base_price',
        'color',
        'material',
        'gender',
        'date_added',
        'is_active',
    ];

    // 1 Product thuộc 1 Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'product_id', 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'product_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }
}
