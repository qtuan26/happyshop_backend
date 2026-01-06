<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   // <-- thêm dòng này
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('brands')->insert([
            ['brand_id' => 1, 'brand_name' => 'Nike', 'country' => 'USA', 'website' => 'https://www.nike.com'],
            ['brand_id' => 2, 'brand_name' => 'Adidas', 'country' => 'Germany', 'website' => 'https://www.adidas.com'],
            ['brand_id' => 3, 'brand_name' => 'New Balance', 'country' => 'USA', 'website' => 'https://www.newbalance.com'],
            ['brand_id' => 4, 'brand_name' => 'Puma', 'country' => 'Germany', 'website' => 'https://www.puma.com'],
            ['brand_id' => 5, 'brand_name' => 'Clarks', 'country' => 'UK', 'website' => 'https://www.clarks.com'],
            ['brand_id' => 6, 'brand_name' => 'Converse', 'country' => 'USA', 'website' => 'https://www.converse.com'],
            ['brand_id' => 7, 'brand_name' => 'Vans', 'country' => 'USA', 'website' => 'https://www.vans.com'],
            ['brand_id' => 8, 'brand_name' => 'Reebok', 'country' => 'USA', 'website' => 'https://www.reebok.com'],
            ['brand_id' => 9, 'brand_name' => 'Asics', 'country' => 'Japan', 'website' => 'https://www.asics.com'],
            ['brand_id' => 10, 'brand_name' => 'Under Armour', 'country' => 'USA', 'website' => 'https://www.underarmour.com'],
        ]);
    }
}
