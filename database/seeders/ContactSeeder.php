<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\User;

;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        // Get only the 5 regular users (not admin)
        $users = User::role('user')->get();

        if ($users->isEmpty()) {
            $this->command->warn('No regular users found. Run UserSeeder first.');
            return;
        }

        // Distribute contacts randomly across the 5 users
        Contact::factory()
            ->count(2222)
            ->make()
            ->each(function ($contact) use ($users) {
                $contact->user_id = $users->random()->id;
                $contact->save();
            });

        $this->command->info('2222 contacts randomly distributed across 5 users.');
    }
}