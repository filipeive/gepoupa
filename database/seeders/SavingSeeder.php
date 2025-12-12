<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Saving;
use App\Models\User;

class SavingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Saving::factory(rand(1, 5))->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
