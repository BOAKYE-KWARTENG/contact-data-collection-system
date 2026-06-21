<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
use App\Models\User;
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
            // Pull from existing users instead of creating new ones
            'user_id' => User::role('user')->inRandomOrder()->first()?->id,
            'name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'age_range' => $this->faker->randomElement(['0-17', '18-24', '25-34', '35-44', '45-54', '55-64', '65+']),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'mobile_number' => '0' . $this->faker->numerify('2########'),
            'telco_id' => \App\Models\Telco::inRandomOrder()->first()?->id ?? \App\Models\Telco::factory(),
            'status' => $this->faker->randomElement(['lead', 'prospect', 'customer', 'inactive']), // these are the possible statuses
            'email' => $this->faker->unique()->safeEmail(),
            

        ];
    }
}
