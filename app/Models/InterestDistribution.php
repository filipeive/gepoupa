<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestDistribution extends Model
{
    use HasFactory;
    protected $fillable = [
        'cycle_id',
        'user_id',
        'amount',
        'distribution_date',
        'description',

    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'distribution_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cycle()
    {
        return $this->belongsTo(SavingCycle::class);
    }
}
