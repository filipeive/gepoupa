<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InterestDistribution;
use App\Models\User;
use App\Models\SavingCycle;

class InterestDistributionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $cycle = SavingCycle::first();

        if ($cycle) {
            foreach ($users as $user) {
                if (rand(0, 1)) {
                    InterestDistribution::factory()->create([
                        'user_id' => $user->id,
                        'cycle_id' => $cycle->id,
                    ]);
                }
            }
        }
    }
}
