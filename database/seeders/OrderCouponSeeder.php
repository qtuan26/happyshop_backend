<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   // <-- thêm dòng này

use Illuminate\Database\Seeder;

class OrderCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('order_coupons')->insert([
            ['order_coupon_id' => 1, 'order_id' => 1, 'coupon_id' => 1, 'discount_applied' => 13.90],
            ['order_coupon_id' => 2, 'order_id' => 3, 'coupon_id' => 2, 'discount_applied' => 20.00],
            ['order_coupon_id' => 3, 'order_id' => 4, 'coupon_id' => 1, 'discount_applied' => 40.00],
            ['order_coupon_id' => 4, 'order_id' => 6, 'coupon_id' => 1, 'discount_applied' => 60.00],
            ['order_coupon_id' => 5, 'order_id' => 2, 'coupon_id' => 1, 'discount_applied' => 15.00],
            ['order_coupon_id' => 6, 'order_id' => 4, 'coupon_id' => 1, 'discount_applied' => 25.00],
            ['order_coupon_id' => 7, 'order_id' => 5, 'coupon_id' => 1, 'discount_applied' => 10.00],
        ]);
    }
}
