<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   // <-- thêm dòng này

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $products = [
            [1, 1, 1, 'Nike Air Zoom Pegasus 40', "111nike_wbuuqc", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285428/111nike_wbuuqc.webp", 'Responsive cushioning in the Pegasus provides an energized ride for everyday road running.', 139.00, 'Black/White', 'Mesh/Synthetic', 'Unisex', '2024-01-01'],
            [2, 1, 2, 'Nike LeBron XXI', "212nike_xab5hh", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285457/212nike_xab5hh.webp", 'Built for speed and power on the basketball court.', 180.00, 'Red/Black', 'Leather/Synthetic', 'Male', '2024-01-01'],
            [3, 1, 3, 'Nike Air Force 1', "313nike_phylm7", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285458/313nike_phylm7.webp", 'The radiance lives on in the Nike Air Force 1, the basketball original.', 120.00, 'White', 'Leather', 'Unisex', '2024-01-01'],
            [4, 1, 6, 'Nike Metcon 9', "416nike_r904je", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285459/416nike_r904je.webp", 'Stability and support for intense workouts.', 150.00, 'Grey/Orange', 'Mesh/Rubber', 'Unisex', '2024-01-05'],
            [5, 1, 7, 'Nike Mercurial Vapor', "517nike_uotkfw", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285460/517nike_uotkfw.webp", 'Speed and agility for the modern game.', 250.00, 'Green/Black', 'Synthetic', 'Male', '2024-01-10'],
            [6, 2, 1, 'Adidas Ultraboost 22', "621adidas_rqnsek", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285461/621adidas_rqnsek.avif", 'Premium running shoes with responsive cushioning.', 190.00, 'Core Black', 'Primeknit', 'Unisex', '2024-01-01'],
            [7, 2, 3, 'Adidas Superstar', "723adidas_w2ljb0", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285901/723adidas_w2ljb0.webp", 'Classic shell-toe design meets modern street style.', 90.00, 'White/Black', 'Leather', 'Unisex', '2024-01-01'],
            [8, 2, 2, 'Adidas Harden Vol. 7', "822adidas_qnlsz1", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285902/822adidas_qnlsz1.webp", 'Signature basketball shoes for explosive performance.', 160.00, 'Blue/Orange', 'Textile/Synthetic', 'Male', '2024-01-01'],
            [9, 2, 7, 'Adidas Predator Edge', "927adidas_l2azze", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285903/927adidas_l2azze.jpg", 'Precision and control on the pitch.', 220.00, 'White/Red', 'Synthetic', 'Male', '2024-01-08'],
            [10, 2, 5, 'Adidas Stan Smith', "1025adidas_dpzej0", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285905/1025adidas_dpzej0.jpg", 'Timeless tennis-inspired sneaker.', 95.00, 'White/Green', 'Leather', 'Unisex', '2024-01-12'],
            [11, 3, 1, 'New Balance Fresh Foam 1080v12', "1131newblance_udoupn", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285907/1131newblance_udoupn.jpg", 'Plush cushioning for long-distance running.', 165.00, 'Grey/Blue', 'Mesh', 'Unisex', '2024-01-01'],
            [12, 3, 5, 'New Balance 574', "1235newblance_mev6it", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285908/1235newblance_mev6it.jpg", 'Iconic sneaker with timeless style.', 85.00, 'Navy', 'Suede/Mesh', 'Unisex', '2024-01-01'],
            [13, 3, 6, 'New Balance 997', "1336newblance_kuesra", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285910/1336newblance_kuesra.jpg", 'Heritage running shoe with modern comfort.', 110.00, 'Grey/Red', 'Suede/Mesh', 'Unisex', '2024-01-15'],
            [14, 4, 1, 'Puma Velocity Nitro 2', "1441puma_o18nuz", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285913/1441puma_o18nuz.webp", 'Lightweight running shoes with nitrogen-infused foam.', 130.00, 'Black/Yellow', 'Mesh', 'Unisex', '2024-01-01'],
            [15, 4, 5, 'Puma Suede Classic', "1545puma_lzixnx", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767286045/1545puma_lzixnx.jpg", 'Street-ready sneaker with iconic suede finish.', 75.00, 'Red', 'Suede', 'Unisex', '2024-01-01'],
            [16, 4, 7, 'Puma Future Z', "1647puma_i76fd8", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767286048/1647puma_i76fd8.jpg", 'Dynamic fit for explosive movements.', 200.00, 'Yellow/Blue', 'Synthetic', 'Male', '2024-01-18'],
            [17, 5, 4, 'Clarks Desert Boot', "1754clarks_alricm", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767286049/1754clarks_alricm.jpg", 'Classic desert boot with comfortable fit.', 145.00, 'Beeswax', 'Leather', 'Male', '2024-01-01'],
            [18, 5, 3, 'Clarks Wallabee', "18_xz8rwm", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285890/18_xz8rwm.jpg", 'Comfortable casual shoes with moccasin construction.', 150.00, 'Maple Suede', 'Suede', 'Male', '2024-01-01'],
            [19, 6, 5, 'Converse Chuck Taylor All Star', "19_uckhpf", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285891/19_uckhpf.jpg", 'The original basketball shoe turned cultural icon.', 65.00, 'Black', 'Canvas', 'Unisex', '2024-01-01'],
            [20, 6, 5, 'Converse Chuck 70', "20_kma2w8", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285895/20_kma2w8.webp", 'Premium version with enhanced comfort.', 85.00, 'White/Navy', 'Canvas', 'Unisex', '2024-01-20'],
            [21, 7, 5, 'Vans Old Skool', "21_v9rg4q", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285894/21_v9rg4q.webp", 'Classic skate shoe with iconic side stripe.', 70.00, 'Black/White', 'Canvas/Suede', 'Unisex', '2024-01-01'],
            [22, 7, 5, 'Vans Authentic', "22_w14her", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285894/22_w14her.jpg", 'Original classic lace-up skate shoe.', 60.00, 'Navy', 'Canvas', 'Unisex', '2024-01-22'],
            [23, 8, 6, 'Reebok Nano X3', "23_avhsly", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285897/23_avhsly.jpg", 'CrossFit training shoe for intense workouts.', 140.00, 'Black/Red', 'Mesh/Synthetic', 'Unisex', '2024-01-25'],
            [24, 9, 1, 'Asics Gel-Kayano 29', "24_loqvep", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285897/24_loqvep.jpg", 'Maximum support for overpronators.', 170.00, 'Blue/Silver', 'Mesh', 'Unisex', '2024-01-28'],
            [25, 10, 6, 'Under Armour TriBase Reign 5', "25_fgntiv", "https://res.cloudinary.com/dpgaptofq/image/upload/v1767285899/25_fgntiv.jpg", 'Stable base for heavy lifting.', 135.00, 'Grey/Red', 'Mesh/Rubber', 'Male', '2024-02-01'],
        ];

        foreach ($products as $p) {
            DB::table('products')->insert([
                'product_id'        => $p[0],
                'brand_id'          => $p[1],
                'category_id'       => $p[2],
                'product_name'      => $p[3],
                'public_url_image'  => $p[4],
                'url_image'         => $p[5],
                'description'       => $p[6],
                'base_price'        => $p[7],
                'color'             => $p[8],
                'material'          => $p[9],
                'gender'            => $p[10],
                'date_added'        => $p[11],
                'is_active'         => true,
            ]);
        }
        
    }
}
