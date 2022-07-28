<?php

namespace Database\Seeders;

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
        User::updateOrCreate(
            ['email' => 'admin@buyer.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@buyer.com',
                'role' => '1',
                'password' => Hash::make('12345678')
            ]
        );

        User::updateOrCreate(
            ['email' => 'reem@buyer.com'],
            [
                'name' => 'Reem',
                'email' => 'reem@buyer.com',
                'role' => '2',
                'password' => Hash::make('12345678')
            ]
        );
    }
}
