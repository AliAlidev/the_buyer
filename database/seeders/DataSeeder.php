<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Data;
use App\Models\EffMaterial;
use App\Models\Shape;
use App\Models\TreatementGroup;
use Illuminate\Database\Seeder;
use Faker\Generator;
use Illuminate\Container\Container;

class DataSeeder extends Seeder
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
        $shapes = Shape::pluck('shape_id')->toArray();
        $companies = Company::pluck('comp_id')->toArray();
        $tretGroups = TreatementGroup::pluck('tg_id')->toArray();
        $effMaterials_array = EffMaterial::pluck('eff_mat_id')->toArray();
        // generate random data values
        for ($i = 0; $i < 9000; $i++) {
            $has_parts = $this->faker->randomElement([0, 1]);
            $partsCount = $has_parts == 1 ? $this->faker->randomDigitNotNull : 0;
            $name = $this->faker->name();
            $newData = Data::firstOrCreate(['name' => $name], [
                'code' => $this->faker->ean13,
                'name' => $name,
                'merchant_type' => $this->faker->randomElement([1, 2]),
                'has_parts' => $has_parts,
                'num_of_parts' => $partsCount,
                'shape_id' => $this->faker->randomElement($shapes),
                'comp_id' => $this->faker->randomElement($companies),
                'treatement_group' => $this->faker->randomElement($tretGroups)
            ]);

            // get random eff materials
            $effMaterials = [];
            for ($e = 0; $e < $this->faker->randomElement([1, 2, 3, 4]); $e++) {
                $effMaterials[] = $this->faker->randomElement($effMaterials_array);
            }

            $newData->effMaterial()->attach($effMaterials);
        }
    }
}
