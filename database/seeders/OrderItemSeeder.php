<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   // <-- thêm dòng này

use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('order_items')->insert([
            ['order_item_id' => 1, 'order_id' => 1, 'product_id' => 1, 'size' => '42', 'quantity' => 1, 'unit_price' => 139.00, 'subtotal' => 139.00],
            ['order_item_id' => 2, 'order_id' => 1, 'product_id' => 3, 'size' => '41', 'quantity' => 1, 'unit_price' => 120.00, 'subtotal' => 120.00],
            ['order_item_id' => 3, 'order_id' => 2, 'product_id' => 6, 'size' => '43', 'quantity' => 3, 'unit_price' => 190.00, 'subtotal' => 190.00],
            ['order_item_id' => 4, 'order_id' => 3, 'product_id' => 2, 'size' => '44', 'quantity' => 4, 'unit_price' => 180.00, 'subtotal' => 180.00],
            ['order_item_id' => 5, 'order_id' => 3, 'product_id' => 17, 'size' => '42', 'quantity' => 5, 'unit_price' => 145.00, 'subtotal' => 145.00],
            ['order_item_id' => 6, 'order_id' => 3, 'product_id' => 12, 'size' => '40', 'quantity' => 1, 'unit_price' => 85.00, 'subtotal' => 85.00],
            ['order_item_id' => 7, 'order_id' => 4, 'product_id' => 8, 'size' => '42', 'quantity' => 1, 'unit_price' => 160.00, 'subtotal' => 160.00],
            ['order_item_id' => 8, 'order_id' => 4, 'product_id' => 15, 'size' => '41', 'quantity' => 2, 'unit_price' => 75.00, 'subtotal' => 150.00],
            ['order_item_id' => 9, 'order_id' => 5, 'product_id' => 19, 'size' => '40', 'quantity' => 2, 'unit_price' => 65.00, 'subtotal' => 130.00],
            ['order_item_id' => 10, 'order_id' => 6, 'product_id' => 9, 'size' => '43', 'quantity' => 1, 'unit_price' => 220.00, 'subtotal' => 220.00],
            ['order_item_id' => 11, 'order_id' => 6, 'product_id' => 5, 'size' => '42', 'quantity' => 1, 'unit_price' => 250.00, 'subtotal' => 250.00],
            ['order_item_id' => 12, 'order_id' => 6, 'product_id' => 14, 'size' => '41', 'quantity' => 1, 'unit_price' => 130.00, 'subtotal' => 130.00],
            ['order_item_id' => 13, 'order_id' => 1, 'product_id' => 21, 'size' => '42', 'quantity' => 2, 'unit_price' => 70.00, 'subtotal' => 140.00],
            ['order_item_id' => 14, 'order_id' => 2, 'product_id' => 22, 'size' => '40', 'quantity' => 2, 'unit_price' => 60.00, 'subtotal' => 120.00],
            ['order_item_id' => 15, 'order_id' => 3, 'product_id' => 24, 'size' => '43', 'quantity' => 1, 'unit_price' => 170.00, 'subtotal' => 170.00],
            ['order_item_id' => 16, 'order_id' => 4, 'product_id' => 23, 'size' => '42', 'quantity' => 1, 'unit_price' => 140.00, 'subtotal' => 140.00],
            ['order_item_id' => 17, 'order_id' => 5, 'product_id' => 13, 'size' => '41', 'quantity' => 1, 'unit_price' => 110.00, 'subtotal' => 110.00],
            ['order_item_id' => 18, 'order_id' => 6, 'product_id' => 7, 'size' => '42', 'quantity' => 1, 'unit_price' => 90.00, 'subtotal' => 90.00],
            ['order_item_id' => 19, 'order_id' => 1, 'product_id' => 10, 'size' => '40', 'quantity' => 1, 'unit_price' => 95.00, 'subtotal' => 95.00],
            ['order_item_id' => 20, 'order_id' => 3, 'product_id' => 4, 'size' => '41', 'quantity' => 1, 'unit_price' => 150.00, 'subtotal' => 150.00],
            ['order_item_id' => 21, 'order_id' => 3, 'product_id' => 20, 'size' => '42', 'quantity' => 1, 'unit_price' => 85.00, 'subtotal' => 85.00],
        ]);
    }
}
