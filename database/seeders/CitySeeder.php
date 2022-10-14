<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $province = Province::where('en_name', 'Tartous')->first();
        City::firstOrCreate(
            ['ar_name' => 'الشيخ بدر', 'en_name' => 'Sheikh Badr'],
            ['province_id' => $province->id, 'ar_name' => 'الشيخ بدر', 'en_name' => 'Sheikh Badr']
        );
        City::firstOrCreate(
            ['ar_name' => 'بانياس', 'en_name' => 'Baniyas'],
            ['province_id' => $province->id, 'ar_name' => 'بانياس', 'en_name' => 'Baniyas']
        );
        City::firstOrCreate(
            ['ar_name' => 'صافيتا', 'en_name' => 'Safita'],
            ['province_id' => $province->id, 'ar_name' => 'صافيتا', 'en_name' => 'Safita']
        );
        City::firstOrCreate(
            ['ar_name' => 'دريكيش', 'en_name' => 'Drakeish'],
            ['province_id' => $province->id, 'ar_name' => 'دريكيش', 'en_name' => 'Drakeish']
        );
        City::firstOrCreate(
            ['ar_name' => 'طرطوس', 'en_name' => 'Tartous'],
            ['province_id' => $province->id, 'ar_name' => 'طرطوس', 'en_name' => 'Tartous']
        );
    }
}
