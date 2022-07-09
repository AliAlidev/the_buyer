<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => Str::random(10),
            'name' => $this->faker->name(),
            'quantity' => $this->faker->numerify,
            'price' => $this->faker->numerify,
            'merchant_id' => 1
        ];
    }
}
