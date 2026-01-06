<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            InventorySeeder::class,
            CouponSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            OrderCouponSeeder::class,
            ProductReviewSeeder::class,
            ShoppingCartSeeder::class,
            CartItemSeeder::class,
        ]);
    }
}
