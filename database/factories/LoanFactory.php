<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $amount = fake()->randomFloat(2, 100, 5000),
            'interest_rate' => 10.00,
            'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'paid']),
            'request_date' => fake()->date(),
            'due_date' => fake()->dateTimeBetween('now', '+6 months'),
            'current_balance' => $amount, // Initialize with amount
        ];
    }
}
