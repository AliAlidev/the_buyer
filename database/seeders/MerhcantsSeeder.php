<?php

namespace Database\Seeders;

use App\Models\Amount;
use App\Models\Data;
use App\Models\Merchant;
use App\Models\Price;
use App\Models\User;
use App\Models\UserData;
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

        $user = User::firstOrCreate(["email" => 'ali1@gmail.com'], [
            "name" => 'ali1',
            "email" => 'ali1@gmail.com',
            "merchant_id" => $merchant->id
        ]);
        $data = Data::firstOrCreate(['name' => 'item1'], [
            'name' => 'item1'
        ]);
        UserData::create([
            'user_id' => $user->id,
            'merchant_id' => $user->merchant_id,
            'data_id' => $data->id,
        ]);
        Amount::create([
            'data_id' => $data->id,
            'amount' => 5,
            'user_id' => $user->id,
            'merchant_id' => $user->merchant_id
        ]);
        Price::create([
            'data_id' => $data->id,
            'price' => 800,
            'user_id' => $user->id,
            'merchant_id' => $user->merchant_id
        ]);

        $data = Data::firstOrCreate(['name' => 'item2'], [
            'name' => 'item2'
        ]);
        UserData::create([
            'merchant_id' => $user->merchant_id,
            'user_id' => $user->id,
            'data_id' => $data->id,
        ]);
        Amount::create([
            'data_id' => $data->id,
            'amount' => 10,
            'user_id' => $user->id,
            'merchant_id' => $user->merchant_id
        ]);
        Price::create([
            'data_id' => $data->id,
            'price' => 500,
            'user_id' => $user->id,
            'merchant_id' => $user->merchant_id
        ]);


        $user =User::firstOrCreate(["email" => 'ali2@gmail.com'], [
            "name" => 'ali2',
            "email" => 'ali2@gmail.com',
            "merchant_id" => $merchant->id
        ]);
        $data = Data::firstOrCreate(['name' => 'item3'], [
            'name' => 'item3'
        ]);
        UserData::create([
            'merchant_id' => $user->merchant_id,
            'user_id' => $user->id,
            'data_id' => $data->id,
        ]);
        Amount::create([
            'data_id' => $data->id,
            'amount' => 20,
            'user_id' => $user->id,
            'merchant_id' => $user->merchant_id
        ]);
        Price::create([
            'data_id' => $data->id,
            'price' => 1000,
            'user_id' => $user->id,
            'merchant_id' => $user->merchant_id
        ]);


        $merchant = Merchant::firstOrCreate(["email" => "ahmad@gmail.com"], [
            "name" => "ahmad",
            "email" => "ahmad@gmail.com",
            "phone" => "+9639321234567",
            "tel_phone" => "0431234567",
            "country" => "syria",
            "city" => "tartous",
            "address" => "share3 al3areed",
            "type" => "Market"
        ]);

        User::firstOrCreate(["email" => 'ahmad1@gmail.com'], [
            "name" => 'ahmad1',
            "email" => 'ahmad1@gmail.com',
            "merchant_id" => $merchant->id
        ]);

        User::firstOrCreate(["email" => 'ahmad2@gmail.com'], [
            "name" => 'ahmad2',
            "email" => 'ahmad2@gmail.com',
            "merchant_id" => $merchant->id
        ]);
    }
}
