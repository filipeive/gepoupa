<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialFund>
 */
class SocialFundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 10, 100),
            'payment_date' => fake()->date(),
            'status' => fake()->randomElement(['pending', 'paid']),
            'penalty_amount' => 0,
            'proof_file' => null,
        ];
    }
}
