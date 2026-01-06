<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   // <-- thêm dòng này

use Illuminate\Database\Seeder;

class ShoppingCartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('shopping_cart')->insert([
            ['cart_id' => 1, 'customer_id' => 1, 'created_at' => '2024-09-01 10:00:00', 'updated_at' => '2024-09-01 10:30:00'],
            ['cart_id' => 2, 'customer_id' => 2, 'created_at' => '2024-09-05 11:15:00', 'updated_at' => '2024-09-05 11:45:00'],
            ['cart_id' => 3, 'customer_id' => 3, 'created_at' => '2024-09-10 14:20:00', 'updated_at' => '2024-09-10 14:50:00'],
            ['cart_id' => 4, 'customer_id' => 4, 'created_at' => '2024-09-12 09:30:00', 'updated_at' => '2024-09-12 10:00:00'],
            ['cart_id' => 5, 'customer_id' => 5, 'created_at' => '2024-09-15 16:40:00', 'updated_at' => '2024-09-15 17:10:00'],
            ['cart_id' => 6, 'customer_id' => 6, 'created_at' => '2024-09-18 13:25:00', 'updated_at' => '2024-09-18 13:55:00'],
            ['cart_id' => 7, 'customer_id' => 7, 'created_at' => '2024-09-20 15:30:00', 'updated_at' => '2024-09-20 16:00:00'],
        ]);
    }
}
