<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   // <-- thêm dòng này

use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('orders')->insert([
            [
                'order_id' => 1,
                'customer_id' => 1,
                'order_date' => '2024-03-15 10:30:00',
                'subtotal' => 259.00,
                'shipping_fee' => 20.00,
                'discount_amount' => 13.90,
                'total_amount' => 265.10,
                'payment_method' => 'Credit Card',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 2,
                'customer_id' => 2,
                'order_date' => '2024-04-20 14:15:00',
                'subtotal' => 190.00,
                'shipping_fee' => 20.00,
                'discount_amount' => 0.00,
                'total_amount' => 210.00,
                'payment_method' => 'PayPal',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 3,
                'customer_id' => 3,
                'order_date' => '2024-04-20 14:15:00',
                'subtotal' => 190.00,
                'shipping_fee' => 20.00,
                'discount_amount' => 0.00,
                'total_amount' => 210.00,
                'payment_method' => 'PayPal',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 4,
                'customer_id' => 4,
                'order_date' => '2024-04-20 14:15:00',
                'subtotal' => 190.00,
                'shipping_fee' => 20.00,
                'discount_amount' => 0.00,
                'total_amount' => 210.00,
                'payment_method' => 'PayPal',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 5,
                'customer_id' => 5,
                'order_date' => '2024-04-20 14:15:00',
                'subtotal' => 190.00,
                'shipping_fee' => 20.00,
                'discount_amount' => 0.00,
                'total_amount' => 210.00,
                'payment_method' => 'PayPal',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 6,
                'customer_id' => 6,
                'order_date' => '2024-04-20 14:15:00',
                'subtotal' => 190.00,
                'shipping_fee' => 20.00,
                'discount_amount' => 0.00,
                'total_amount' => 210.00,
                'payment_method' => 'PayPal',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
