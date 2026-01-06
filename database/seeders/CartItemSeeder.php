<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   // <-- thêm dòng này

use Illuminate\Database\Seeder;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
         DB::table('cart_items')->insert([
            ['cart_item_id' => 1, 'cart_id' => 1, 'product_id' => 7, 'size' => '42', 'quantity' => 1, 'added_at' => '2024-09-01 10:15:00'],
            ['cart_item_id' => 2, 'cart_id' => 1, 'product_id' => 19, 'size' => '41', 'quantity' => 2, 'added_at' => '2024-09-01 10:30:00'],
            ['cart_item_id' => 3, 'cart_id' => 2, 'product_id' => 11, 'size' => '43', 'quantity' => 1, 'added_at' => '2024-09-05 11:20:00'],
            ['cart_item_id' => 4, 'cart_id' => 2, 'product_id' => 16, 'size' => '42', 'quantity' => 1, 'added_at' => '2024-09-05 11:45:00'],
            ['cart_item_id' => 5, 'cart_id' => 3, 'product_id' => 18, 'size' => '40', 'quantity' => 1, 'added_at' => '2024-09-10 14:25:00'],
            ['cart_item_id' => 6, 'cart_id' => 3, 'product_id' => 25, 'size' => '42', 'quantity' => 2, 'added_at' => '2024-09-10 14:50:00'],
            ['cart_item_id' => 7, 'cart_id' => 4, 'product_id' => 20, 'size' => '41', 'quantity' => 1, 'added_at' => '2024-09-12 09:35:00'],
            ['cart_item_id' => 8, 'cart_id' => 4, 'product_id' => 22, 'size' => '40', 'quantity' => 1, 'added_at' => '2024-09-12 10:00:00'],
            ['cart_item_id' => 9, 'cart_id' => 5, 'product_id' => 1, 'size' => '42', 'quantity' => 1, 'added_at' => '2024-09-15 16:45:00'],
            ['cart_item_id' => 10, 'cart_id' => 5, 'product_id' => 3, 'size' => '43', 'quantity' => 1, 'added_at' => '2024-09-15 17:10:00'],
            ['cart_item_id' => 11, 'cart_id' => 6, 'product_id' => 6, 'size' => '42', 'quantity' => 1, 'added_at' => '2024-09-18 13:30:00'],
            ['cart_item_id' => 12, 'cart_id' => 6, 'product_id' => 10, 'size' => '41', 'quantity' => 2, 'added_at' => '2024-09-18 13:55:00'],
            ['cart_item_id' => 13, 'cart_id' => 7, 'product_id' => 14, 'size' => '43', 'quantity' => 1, 'added_at' => '2024-09-20 15:35:00'],
            ['cart_item_id' => 14, 'cart_id' => 7, 'product_id' => 15, 'size' => '42', 'quantity' => 1, 'added_at' => '2024-09-20 16:00:00'],
            ['cart_item_id' => 15, 'cart_id' => 1, 'product_id' => 12, 'size' => '40', 'quantity' => 1, 'added_at' => '2024-09-02 11:20:00'],
            ['cart_item_id' => 16, 'cart_id' => 2, 'product_id' => 4, 'size' => '42', 'quantity' => 1, 'added_at' => '2024-09-06 10:30:00'],
            ['cart_item_id' => 17, 'cart_id' => 3, 'product_id' => 8, 'size' => '41', 'quantity' => 1, 'added_at' => '2024-09-11 15:15:00'],
            ['cart_item_id' => 18, 'cart_id' => 4, 'product_id' => 23, 'size' => '43', 'quantity' => 2, 'added_at' => '2024-09-13 14:25:00'],
            ['cart_item_id' => 19, 'cart_id' => 5, 'product_id' => 13, 'size' => '40', 'quantity' => 1, 'added_at' => '2024-09-16 09:45:00'],
            ['cart_item_id' => 20, 'cart_id' => 6, 'product_id' => 24, 'size' => '42', 'quantity' => 1, 'added_at' => '2024-09-19 16:10:00'],
        ]);
    }
}
