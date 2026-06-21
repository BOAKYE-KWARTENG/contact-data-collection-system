<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Telco;
use Illuminate\Support\Str;

class TelcoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carriers = [
            'MTN',
            'Telecel', // Formerly Vodafone
            'AirtelTigo',
        ];

        foreach ($carriers as $carrier) {
            // Using updateOrCreate avoids duplicate record errors if run twice
            Telco::updateOrCreate(
                ['code' => Str::slug($carrier)], // Unique lookup identifier
                ['name' => $carrier]
            );
        }
    }
}
