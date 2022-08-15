<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::firstOrCreate(
            [
                'comp_id' => '1',
                'ar_comp_name' => 'كبور',
                'en_comp_name' => 'kabbor',
                'merchant_type' => '2'
            ],
            [
                'comp_id' => '1',
                'ar_comp_name' => 'كبور',
                'en_comp_name' => 'kabbor',
                'merchant_type' => '2'
            ]
        );
        Company::firstOrCreate(
            [
                'comp_id' => '1',
                'ar_comp_name' => 'ديمة',
                'en_comp_name' => 'dema',
                'merchant_type' => '2'
            ],
            [
                'comp_id' => '1',
                'ar_comp_name' => 'ديمة',
                'en_comp_name' => 'dema',
                'merchant_type' => '2'
            ]
        );
        Company::firstOrCreate(
            [
                'comp_id' => '1',
                'ar_comp_name' => 'ويندكس',
                'en_comp_name' => 'windex',
                'merchant_type' => '2'
            ],
            [
                'comp_id' => '1',
                'ar_comp_name' => 'ويندكس',
                'en_comp_name' => 'windex',
                'merchant_type' => '2'
            ]
        );

        Company::firstOrCreate(
            [
                'comp_id' => '1',
                'ar_comp_name' => 'ابن الهيقم',
                'en_comp_name' => 'ibn al hetham',
                'merchant_type' => '1'
            ],
            [
                'comp_id' => '1',
                'ar_comp_name' => 'ابن الهيقم',
                'en_comp_name' => 'ibn al hetham',
                'merchant_type' => '1'
            ]
        );
        Company::firstOrCreate(
            [
                'comp_id' => '1',
                'ar_comp_name' => 'ابن زهر',
                'en_comp_name' => 'ibn al zaher',
                'merchant_type' => '1'
            ],
            [
                'comp_id' => '1',
                'ar_comp_name' => 'ابن زهر',
                'en_comp_name' => 'ibn al zaher',
                'merchant_type' => '1'
            ]
        );
        Company::firstOrCreate(
            [
                'comp_id' => '1',
                'ar_comp_name' => 'تاميكو',
                'en_comp_name' => 'tamico',
                'merchant_type' => '1'
            ],
            [
                'comp_id' => '1',
                'ar_comp_name' => 'تاميكو',
                'en_comp_name' => 'tamico',
                'merchant_type' => '1'
            ]
        );
    }
}
