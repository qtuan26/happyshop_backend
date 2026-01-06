<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;
    protected $table = 'shopping_cart';
    protected $primaryKey = 'cart_id';

    protected $fillable = ['customer_id'];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
