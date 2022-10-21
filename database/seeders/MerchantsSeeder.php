<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Generator;
use Illuminate\Container\Container;

class MerchantsSeeder extends Seeder
{
    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = ['user1', 'user2', 'user3'];
        $merchants = [];
        for ($i = 1; $i <= 100; $i++) {
            $merchants['merchant' . $i] =  1;
        }

        foreach ($merchants as $key => $item) {
            $email = $key . '@buyer.com';
            $merchant = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $key,
                    'email' => $email,
                    'role' => '1',  // merchant
                    'merchant_type' => '2',  // market
                    'password' => Hash::make('12345678'),
                    "phone" => $this->faker->phoneNumber,
                    "tel_phone" => $this->faker->e164PhoneNumber,
                    // "country" => $this->faker->country,
                    // "city" => $this->faker->city,
                    "address" => $this->faker->address
                ]
            );
            $merchant->merchant_id = $merchant->id;
            $merchant->save();

            // merchant users
            foreach ($users as  $value) {
                $name = $this->faker->userName;
                $email = $value . $merchant->id . '@buyer.com';
                User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'email' => $email,
                        'role' => '2',  // employee
                        'merchant_type' => $merchant->merchant_type,
                        'merchant_id' => $merchant->id,
                        'password' => Hash::make('12345678')
                    ]
                );
            }
        }
    }
}
