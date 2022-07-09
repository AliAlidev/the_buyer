<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Seeder;

class MerhcantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchant = Merchant::UpdateOrCreate(["email" => "ali@gmail.com"], [
            "name" => "Ali",
            "email" => "ali@gmail.com",
            "phone" => "+963936566977",
            "tel_phone" => "0431234567",
            "country" => "syria",
            "city" => "tartous",
            "address" => "alshekh saad",
            "type" => "Pharmacist"
        ]);

        User::UpdateOrCreate(["email" => 'ali1@gmail.com'], [
            "name" => 'ali1',
            "email" => 'ali1@gmail.com',
            "merchant_id" => $merchant->id
        ]);

        User::UpdateOrCreate(["email" => 'ali2@gmail.com'], [
            "name" => 'ali2',
            "email" => 'ali2@gmail.com',
            "merchant_id" => $merchant->id
        ]);

        $merchant = Merchant::UpdateOrCreate(["email" => "ahmad@gmail.com"], [
            "name" => "ahmad",
            "email" => "ahmad@gmail.com",
            "phone" => "+9639321234567",
            "tel_phone" => "0431234567",
            "country" => "syria",
            "city" => "tartous",
            "address" => "share3 al3areed",
            "type" => "Market"
        ]);

        User::UpdateOrCreate(["email" => 'ahmad1@gmail.com'], [
            "name" => 'ahmad1',
            "email" => 'ahmad1@gmail.com',
            "merchant_id" => $merchant->id
        ]);

        User::UpdateOrCreate(["email" => 'ahmad2@gmail.com'], [
            "name" => 'ahmad2',
            "email" => 'ahmad2@gmail.com',
            "merchant_id" => $merchant->id
        ]);
    }
}
