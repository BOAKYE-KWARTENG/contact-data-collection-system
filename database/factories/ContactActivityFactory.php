<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\Contact;
use App\Models\ContactActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactActivityFactory extends Factory
{
    protected $model = ContactActivity::class;

    public function definition(): array
    {
        return [
            'contact_id'    => Contact::factory(),   // creates a contact if none exists
            'created_by'    => User::factory(),       // creates a user if none exists
            'activity_type' => fake()->randomElement(ActivityType::cases())->value,
            'description'   => fake()->sentence(10),
        ];
    }

    // ── Named States — for specific activity types ─────────

    public function called(): static
    {
        return $this->state(['activity_type' => 'called_customer']);
    }

    public function quoted(): static
    {
        return $this->state(['activity_type' => 'sent_quotation']);
    }

    public function followedUp(): static
    {
        return $this->state(['activity_type' => 'follow_up_email']);
    }

    public function met(): static
    {
        return $this->state(['activity_type' => 'meeting_completed']);
    }
}