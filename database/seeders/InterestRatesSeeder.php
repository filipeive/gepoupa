<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InterestRates;

class InterestRatesSeeder extends Seeder
{
    public function run(): void
    {
        InterestRates::create([
            'rate' => 5.00,
            'effective_date' => now()->subMonths(6),
            'description' => 'Initial Interest Rate',
        ]);

        InterestRates::create([
            'rate' => 10.00,
            'effective_date' => now()->subMonths(1),
            'description' => 'Updated Interest Rate',
        ]);
    }
}
