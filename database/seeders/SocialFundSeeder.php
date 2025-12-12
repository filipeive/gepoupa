<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SocialFund;
use App\Models\User;

class SocialFundSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            SocialFund::factory(rand(1, 3))->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
