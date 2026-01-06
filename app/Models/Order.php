<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'customer_id',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'total_amount',
        'payment_method',
        'status',
    ];

    /* ================= RELATIONSHIPS ================= */

    // Order → Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    // Order → OrderItems
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }
     public function coupons()
    {
        return $this->hasMany(OrderCoupon::class, 'order_id', 'order_id');
    }
}
