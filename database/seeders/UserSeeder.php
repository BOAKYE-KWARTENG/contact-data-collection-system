<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create Roles ───────────────────────────────────
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        // ── Create Admin ───────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
            ]
        )->assignRole('admin');

        // ── Create exactly 5 regular users ────────────────
        $users = [
            ['name' => 'Kwame Mensah',   'email' => 'kwame@example.com'],
            ['name' => 'Akosua Boateng', 'email' => 'akosua@example.com'],
            ['name' => 'Kofi Asante',    'email' => 'kofi@example.com'],
            ['name' => 'Abena Owusu',    'email' => 'abena@example.com'],
            ['name' => 'Yaw Darko',      'email' => 'yaw@example.com'],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => Hash::make('password'),
                ]
            )->assignRole('user');
        }

        $this->command->info('1 admin + 5 users created successfully.');
    }
}