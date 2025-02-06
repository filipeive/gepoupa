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
        'due_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'request_date' => 'date',
        'due_date' => 'date'
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
        // Implementar lógica de cálculo de juros (10% a cada dois meses)
        $interest = $this->amount * $this->interest_rate / 2;
        return $interest;
    }
}
