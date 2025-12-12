<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\User;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            if (rand(0, 1)) {
                Loan::factory()->create([
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
