<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   // <-- thêm dòng này

use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('product_reviews')->insert([
            ['review_id' => 1, 'customer_id' => 1, 'product_id' => 1, 'rating' => 5, 'review_text' => 'Excellent running shoes! Very comfortable and lightweight.', 'created_at' => '2024-03-20 11:00:00'],
            ['review_id' => 2, 'customer_id' => 1, 'product_id' => 3, 'rating' => 4, 'review_text' => 'Classic design, fits well. Great for everyday wear.', 'created_at' => '2024-03-21 15:30:00'],
            ['review_id' => 3, 'customer_id' => 2, 'product_id' => 6, 'rating' => 5, 'review_text' => 'Best running shoes I have ever owned!', 'created_at' => '2024-04-25 09:15:00'],
            ['review_id' => 4, 'customer_id' => 3, 'product_id' => 2, 'rating' => 4, 'review_text' => 'Great basketball shoes, good ankle support.', 'created_at' => '2024-05-12 16:45:00'],
            ['review_id' => 5, 'customer_id' => 3, 'product_id' => 17, 'rating' => 5, 'review_text' => 'High quality leather boots, very comfortable!', 'created_at' => '2024-05-15 10:20:00'],
            ['review_id' => 6, 'customer_id' => 4, 'product_id' => 8, 'rating' => 4, 'review_text' => 'Good value for money. Solid basketball shoe.', 'created_at' => '2024-05-30 14:30:00'],
            ['review_id' => 7, 'customer_id' => 5, 'product_id' => 19, 'rating' => 5, 'review_text' => 'Classic Converse! Love them!', 'created_at' => '2024-06-08 11:45:00'],
            ['review_id' => 8, 'customer_id' => 6, 'product_id' => 9, 'rating' => 4, 'review_text' => 'Nice design and comfortable for soccer.', 'created_at' => '2024-06-22 16:00:00'],
            ['review_id' => 9, 'customer_id' => 7, 'product_id' => 21, 'rating' => 5, 'review_text' => 'Perfect skate shoes. Very durable.', 'created_at' => '2024-07-05 13:20:00'],
            ['review_id' => 10, 'customer_id' => 8, 'product_id' => 24, 'rating' => 5, 'review_text' => 'Amazing support for running. Highly recommend!', 'created_at' => '2024-07-25 09:30:00'],
            ['review_id' => 11, 'customer_id' => 1, 'product_id' => 7, 'rating' => 4, 'review_text' => 'Good casual sneakers, comfortable fit.', 'created_at' => '2024-08-12 15:15:00'],
            ['review_id' => 12, 'customer_id' => 2, 'product_id' => 4, 'rating' => 5, 'review_text' => 'Best training shoes for the gym!', 'created_at' => '2024-08-28 10:45:00'],
            ['review_id' => 13, 'customer_id' => 4, 'product_id' => 15, 'rating' => 4, 'review_text' => 'Classic Puma style, love the suede material.', 'created_at' => '2024-06-05 14:20:00'],
            ['review_id' => 14, 'customer_id' => 6, 'product_id' => 5, 'rating' => 5, 'review_text' => 'Excellent soccer cleats! Great traction.', 'created_at' => '2024-06-25 11:30:00'],
            ['review_id' => 15, 'customer_id' => 3, 'product_id' => 12, 'rating' => 4, 'review_text' => 'Comfortable and stylish. Good quality.', 'created_at' => '2024-05-20 16:50:00'],
        ]);
    }
}
