<?php

namespace Database\Seeders;

use App\Models\Data;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttachDataToMerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = Data::pluck('id')->toArray();
        $merchants = User::where('role', 1)->get();
        foreach ($merchants as $key => $merchant) {
            $user = User::where('merchant_id', $merchant->id)->where('role', 2)->first();
            $merchant->data()->attach($data, ['user_id' => $user->id]);
        }
    }
}
