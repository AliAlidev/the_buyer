<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Province::firstOrCreate([
            'ar_name' => 'دمشق',
            'en_name' => 'Damascus',
        ], [
            'ar_name' => 'دمشق',
            'en_name' => 'Damascus',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'ريف دمشق',
            'en_name' => 'Rural Damascus',
        ],[
            'ar_name' => 'ريف دمشق',
            'en_name' => 'Rural Damascus',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'القنيطرة',
            'en_name' => 'Quneitra',
        ],[
            'ar_name' => 'القنيطرة',
            'en_name' => 'Quneitra',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'درعا',
            'en_name' => 'Daraa',
        ],[
            'ar_name' => 'درعا',
            'en_name' => 'Daraa',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'السويداء',
            'en_name' => 'Sweida',
        ],[
            'ar_name' => 'السويداء',
            'en_name' => 'Sweida',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'حمص',
            'en_name' => 'Homs',
        ],[
            'ar_name' => 'حمص',
            'en_name' => 'Homs',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'طرطوس',
            'en_name' => 'Tartous',
        ],[
            'ar_name' => 'طرطوس',
            'en_name' => 'Tartous',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'اللاذقية',
            'en_name' => 'Latakia',
        ],[
            'ar_name' => 'اللاذقية',
            'en_name' => 'Latakia',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'حماة',
            'en_name' => 'Hama',
        ],[
            'ar_name' => 'حماة',
            'en_name' => 'Hama',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'إدلب',
            'en_name' => 'Idlib',
        ],[
            'ar_name' => 'إدلب',
            'en_name' => 'Idlib',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'حلب',
            'en_name' => 'Aleppo',
        ],[
            'ar_name' => 'حلب',
            'en_name' => 'Aleppo',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'الرقة',
            'en_name' => 'Al-Raqqa',
        ],[
            'ar_name' => 'الرقة',
            'en_name' => 'Al-Raqqa',
        ]);
        Province::firstOrCreate([
            'ar_name' => ' دير الزور',
            'en_name' => 'Der-Alzoor',
        ],[
            'ar_name' => ' دير الزور',
            'en_name' => 'Der-Alzoor',
        ]);
        Province::firstOrCreate([
            'ar_name' => 'الحسكة',
            'en_name' => 'Al-Hasakah',
        ],[
            'ar_name' => 'الحسكة',
            'en_name' => 'Al-Hasakah',
        ]);
    }
}
