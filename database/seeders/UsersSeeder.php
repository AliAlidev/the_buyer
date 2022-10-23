<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
            1 Admin
            2 merchant
            3 user
        */
        $province = Province::where('en_name','tartous')->first();
        $city = City::where('en_name', 'Sheikh Badr')->first();
        User::updateOrCreate(
            ['email' => 'admin@buyer.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@buyer.com',
                'role' => '0',  // admin
                'password' => Hash::make('12345678'),
                "phone" => "+963936566977",
                "tel_phone" => "0431234567",
                "province" => $province->id,
                "city" => $city->id,
                "address" => "alshekh saad"
            ]
        );

        // $merchant = User::where('email', 'admin@buyer.com')->First();
        // User::updateOrCreate(
        //     ['email' => 'ali_m@buyer.com'],
        //     [
        //         'name' => 'Ali',
        //         'email' => 'ali_m@buyer.com',
        //         'role' => '2',  // employee
        //         'merchant_type' => '2',  // market
        //         'merchant_id' => $merchant->id,
        //         'password' => Hash::make('12345678')
        //     ]
        // );

        // $merchant = User::where('email', 'admin@buyer.com')->First();
        // User::updateOrCreate(
        //     ['email' => 'ali_p@buyer.com'],
        //     [
        //         'name' => 'Ali p',
        //         'email' => 'ali_p@buyer.com',
        //         'role' => '2',  // employee
        //         'merchant_type' => '1',  // Pharmacist
        //         'merchant_id' => $merchant->id,
        //         'password' => Hash::make('12345678')
        //     ]
        // );
    }
}
