<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   // <-- thêm dòng này

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('categories')->insert([
            ['category_id' => 1, 'category_name' => 'Running', 'description' => 'Shoes designed for running and jogging'],
            ['category_id' => 2, 'category_name' => 'Basketball', 'description' => 'High-performance shoes for basketball'],
            ['category_id' => 3, 'category_name' => 'Casual', 'description' => 'Everyday casual and lifestyle shoes'],
            ['category_id' => 4, 'category_name' => 'Boots', 'description' => 'Boots for winter, hiking and outdoor activities'],
            ['category_id' => 5, 'category_name' => 'Sneakers', 'description' => 'Stylish sneakers for urban fashion'],
            ['category_id' => 6, 'category_name' => 'Training', 'description' => 'Shoes for gym and cross-training'],
            ['category_id' => 7, 'category_name' => 'Soccer', 'description' => 'Football/Soccer cleats and shoes'],
        ]);
    }
}
