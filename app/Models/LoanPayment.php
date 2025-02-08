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
        'proof_file',
        'distributed_at',  // Novo campo
        'cycle_id'        // Novo campo
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'payment_date' => 'date',
        'distributed_at' => 'datetime'  // Novo cast
    ];

    // Relacionamento existente
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    // Novo relacionamento
    public function cycle()
    {
        return $this->belongsTo(SavingCycle::class);
    }

    // Escopos úteis para filtrar pagamentos
    public function scopePending($query)
    {
        return $query->whereNull('distributed_at');
    }

    public function scopeDistributed($query)
    {
        return $query->whereNotNull('distributed_at');
    }
}