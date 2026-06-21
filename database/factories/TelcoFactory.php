<?php

namespace Database\Factories;

use App\Models\Telco;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TelcoFactory extends Factory
{
    protected $model = Telco::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['MTN', 'Telecel', 'AirtelTigo']);

        return [
            'name' => $name,
            'code' => Str::slug($name),
        ];
    }
}