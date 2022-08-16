<?php

namespace Database\Seeders;

use App\Models\Shape;
use Illuminate\Database\Seeder;

class ShapesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Shape::firstOrCreate(
            [
                'shape_id' => '1',
                'ar_shape_name' => 'علبة',
                'en_shape_name' => 'can',
                'merchant_type' => '2'
            ],
            [
                'shape_id' => '1',
                'ar_shape_name' => 'علبة',
                'en_shape_name' => 'can',
                'merchant_type' => '2'
            ]
        );
        Shape::firstOrCreate(
            [
                'shape_id' => '1',
                'ar_shape_name' => 'قطعة',
                'en_shape_name' => 'piece',
                'merchant_type' => '2'
            ],
            [
                'shape_id' => '1',
                'ar_shape_name' => 'قطعة',
                'en_shape_name' => 'piece',
                'merchant_type' => '2'
            ]
        );
        Shape::firstOrCreate(
            [
                'shape_id' => '1',
                'ar_shape_name' => 'كيس',
                'en_shape_name' => 'bag',
                'merchant_type' => '2'
            ],
            [
                'shape_id' => '1',
                'ar_shape_name' => 'كيس',
                'en_shape_name' => 'bag',
                'merchant_type' => '2'
            ]
        );

    }
}
