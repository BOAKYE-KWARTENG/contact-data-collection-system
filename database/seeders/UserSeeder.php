<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        )->assignRole('admin');

        // Regular user
        User::firstOrCreate(
            ['email' => 'isaacburns@gmail.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('isaacburns'),
            ]
        )->assignRole('user');
    }
}
