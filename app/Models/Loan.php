<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'amount',
        'interest_rate',
        'status',
        'request_date',
        'due_date',
        'current_balance',
        'accumulated_interest',
        'last_interest_calculation_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'accumulated_interest' => 'decimal:2',
        'request_date' => 'date',
        'due_date' => 'date',
        'last_interest_calculation_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function calculateInterest()
    {
        // Juros de 10% a cada dois meses sobre o saldo devedor
        // Se o saldo devedor for 0, não há juros
        if ($this->current_balance <= 0) {
            return 0;
        }

        return $this->current_balance * 0.10;
    }
}
