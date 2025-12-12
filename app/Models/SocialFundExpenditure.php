<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialFundExpenditure extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'description',
        'expenditure_date',
        'proof_file'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expenditure_date' => 'date'
    ];
}
