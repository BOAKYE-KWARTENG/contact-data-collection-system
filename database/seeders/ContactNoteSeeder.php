<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactNote;
use Illuminate\Database\Seeder;
use App\Models\User;


class ContactNoteSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = Contact::all();

        if ($contacts->isEmpty()) {
            $this->command->warn('No contacts found. Run ContactSeeder first.');
            return;
        }

        $contacts->each(function ($contact) {

            $count = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $count; $i++) {
                ContactNote::create([                        // ✅ create directly
                    'contact_id' => $contact->id,
                    'user_id'    => $contact->user_id,      // ✅ contact's owner
                    'note'       => '<p>' . fake()->paragraph(3) . '</p>',
                ]);
            }
        });

        $this->command->info('Contact notes seeded successfully.');
    }
}