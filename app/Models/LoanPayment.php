<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'loan_id',
        'amount',
        'interest_amount',
        'payment_date',
        'proof_file'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'payment_date' => 'date'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
