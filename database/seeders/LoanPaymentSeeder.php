<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoanPayment;
use App\Models\Loan;

class LoanPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $loans = Loan::all();

        foreach ($loans as $loan) {
            if (rand(0, 1)) {
                LoanPayment::factory(rand(1, 3))->create([
                    'loan_id' => $loan->id,
                ]);
            }
        }
    }
}
