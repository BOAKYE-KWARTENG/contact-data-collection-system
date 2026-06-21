<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ContactSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class, 
            TelcoSeeder::class,            // 1. users first
            ContactSeeder::class,         // 2. contacts second
            ContactActivitySeeder::class, // 3. activities last (depends on both)
            ContactNoteSeeder::class,     // 4. notes last (depends on both)
            ReminderTestSeeder::class,    // 5. New Reminders (safely hooks onto contacts last)
        ]);

    }
}
