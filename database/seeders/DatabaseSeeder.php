<?php

namespace Database\Seeders;

use App\Http\Controllers\SendEmailsTriat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use SendEmailsTriat;
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // $this->call([UsersSeeder::class]);
        // $this->call([ShapesSeeder::class]);
        // $this->call([CompaniesSeeder::class]);
        $this->call([TestDataSeeder::class]);
        // $this->call([InvoiceSeeder::class]);

        // send email for admin
        $this->sendEmailToAdmin();
    }
}
