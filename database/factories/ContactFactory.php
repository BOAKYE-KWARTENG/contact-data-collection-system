<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'age_range' => $this->faker->randomElement(['0-17', '18-24', '25-34', '35-44', '45-54', '55-64', '65+']),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'mobile_number' => '0' . $this->faker->numerify('2########'),
            'telco' => $this->faker->randomElement(['MTN', 'Telecel', 'AirtelTigo', 'Glo']),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
