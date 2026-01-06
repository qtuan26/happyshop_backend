<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Lấy danh sách coupon active (trang chủ)
     */
    public function index()
    {
        $coupons = Coupon::where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->select(
                'coupon_id',
                'coupon_code',
                'title',
                'url_image',
                'description'
            )
            ->get();

        return response()->json([
            'data' => $coupons
        ]);
    }

    /**
     * Lấy chi tiết coupon khi click
     */
    public function show($id)
    {
        $coupon = Coupon::where('coupon_id', $id)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon không tồn tại hoặc đã hết hạn'
            ], 404);
        }

        return response()->json([
            'data' => [
                'coupon_id' => $coupon->coupon_id,
                'coupon_code' => $coupon->coupon_code,
                'title' => 'Nhập ' . $coupon->coupon_code . ' để được giảm giá',
                'description' => $coupon->description,
                'url_image' => $coupon->url_image,
                'discount' => [
                    'type' => $coupon->discount_type,
                    'value' => (float) $coupon->discount_value,
                    'max' => $coupon->max_discount_amount,
                ],
                'condition' => [
                    'min_order_value' => $coupon->min_purchase_amount,
                ],
                'validity' => [
                    'start_date' => $coupon->start_date,
                    'end_date' => $coupon->end_date,
                ],
            ]
        ]);
    }
}
