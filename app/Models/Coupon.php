<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $primaryKey = 'coupon_id';

    protected $fillable = [
        'coupon_code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_purchase_amount',
        'usage_limit',
        'start_date',
        'end_date',
        'is_active',
        'url_image',
        'public_url_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function orders()
    {
        return $this->hasMany(OrderCoupon::class, 'coupon_id', 'coupon_id');
    }
}
