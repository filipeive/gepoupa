<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SavingCycle;

class SavingCycleSeeder extends Seeder
{
    public function run(): void
    {
        SavingCycle::factory()->create([
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'status' => 'active',
            'month_year' => now()->format('m/Y'),
            'name' => 'Cycle ' . now()->year,
        ]);
    }
}
