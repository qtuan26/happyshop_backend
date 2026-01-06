<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   

use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('coupons')->insert([
            [
                'coupon_id' => 1,
                'coupon_code' => 'BIGSALE',
                'title' => 'Giảm 50% – Tối đa $50',
                'url_image' => 'https://res.cloudinary.com/dpgaptofq/image/upload/v1767418983/sale-2_sckmtf.jpg',
                'public_url_image' => 'sale-2_sckmtf',
                'description' =>
                    'Giảm ngay 50% tổng giá trị đơn hàng, mức giảm tối đa lên đến $50. '
                    .'Áp dụng cho đơn hàng từ $50 trở lên. '
                    .'Số lượng có hạn, nhanh tay sử dụng!',
                'discount_type' => 'percentage',
                'discount_value' => 50,
                'max_discount_amount' => 50,
                'min_purchase_amount' => 50,
                'usage_limit' => 100,
                'start_date' => '2026-01-01',
                'end_date' => '2026-01-31',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coupon_id' => 2,
                'coupon_code' => 'FREESHIP',
                'title' => 'Hỗ trợ phí vận chuyển $20',
                'url_image' => 'https://res.cloudinary.com/dpgaptofq/image/upload/v1767419446/sale-5_yo7anm.webp',
                'public_url_image' => 'sale-5_yo7anm',
                'description' =>
                    'Giảm ngay $20 cho đơn hàng có tổng giá trị từ $300 trở lên. '
                    .'Áp dụng cho tất cả sản phẩm trên hệ thống.',
                'discount_type' => 'fixed_amount',
                'discount_value' => 20,
                'max_discount_amount' => null,
                'min_purchase_amount' => 300,
                'usage_limit' => 50,
                'start_date' => '2026-01-01',
                'end_date' => '2026-01-31',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}

