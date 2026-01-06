<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;   // <-- thêm dòng này

use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('customers')->insert([
            ['customer_id' => 1, 'user_id' => 3, 'full_name' => 'John Doe', 'phone' => '0901234567', 'address' => '123 Nguyen Hue', 'city' => 'Ho Chi Minh City', 'state' => 'HCMC', 'zip_code' => '700000', 'registration_date' => '2024-01-10'],
            ['customer_id' => 2, 'user_id' => 4, 'full_name' => 'Sarah Smith', 'phone' => '0912345678', 'address' => '456 Le Loi', 'city' => 'Ho Chi Minh City', 'state' => 'HCMC', 'zip_code' => '700000', 'registration_date' => '2024-01-15'],
            ['customer_id' => 3, 'user_id' => 5, 'full_name' => 'Mike Johnson', 'phone' => '0923456789', 'address' => '789 Tran Hung Dao', 'city' => 'Hanoi', 'state' => 'HN', 'zip_code' => '100000', 'registration_date' => '2024-02-01'],
            ['customer_id' => 4, 'user_id' => 6, 'full_name' => 'Emily Brown', 'phone' => '0934567890', 'address' => '321 Hai Ba Trung', 'city' => 'Da Nang', 'state' => 'DN', 'zip_code' => '550000', 'registration_date' => '2024-02-10'],
            ['customer_id' => 5, 'user_id' => 7, 'full_name' => 'David Wilson', 'phone' => '0945678901', 'address' => '555 Ly Thuong Kiet', 'city' => 'Ho Chi Minh City', 'state' => 'HCMC', 'zip_code' => '700000', 'registration_date' => '2024-03-01'],
            ['customer_id' => 6, 'user_id' => 8, 'full_name' => 'Lisa Anderson', 'phone' => '0956789012', 'address' => '888 Dong Khoi', 'city' => 'Ho Chi Minh City', 'state' => 'HCMC', 'zip_code' => '700000', 'registration_date' => '2024-03-15'],
            ['customer_id' => 7, 'user_id' => 9, 'full_name' => 'James Taylor', 'phone' => '0967890123', 'address' => '999 Ba Trieu', 'city' => 'Hanoi', 'state' => 'HN', 'zip_code' => '100000', 'registration_date' => '2024-04-01'],
            ['customer_id' => 8, 'user_id' => 10, 'full_name' => 'Anna Martinez', 'phone' => '0978901234', 'address' => '111 Phan Chu Trinh', 'city' => 'Da Nang', 'state' => 'DN', 'zip_code' => '550000', 'registration_date' => '2024-04-10'],
        ]);
    }
}
