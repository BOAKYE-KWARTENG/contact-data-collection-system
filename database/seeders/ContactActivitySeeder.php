<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\ContactActivity;
use App\Models\User;


class ContactActivitySeeder extends Seeder
{
    public function run(): void
    {
        $contacts = Contact::all();

        if ($contacts->isEmpty()) {
            $this->command->warn('No contacts found. Run ContactSeeder first.');
            return;
        }

        $contacts->each(function ($contact) {

            $count = fake()->numberBetween(2, 5);

            ContactActivity::factory()
                ->count($count)
                ->create([
                    'contact_id' => $contact->id,
                    'created_by' => $contact->user_id, // ✅ always the contact's owner
                ]);
        });

        $this->command->info('Contact activities seeded successfully.');
    }
}