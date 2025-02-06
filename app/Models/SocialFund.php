<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialFund extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'amount',
        'payment_date',
        'status',
        'penalty_amount',
        'proof_file'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'payment_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function calculatePenalty()
    {
        // Implementar lógica de cálculo de multa (10% por semana de atraso)
        $penalty = $this->amount * 0.1;
        return $penalty;
    }
}
