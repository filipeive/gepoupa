<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingCycle extends Model
{
    use HasFactory;
    protected $fillable = [
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function interestDistributions()
    {
        return $this->hasMany(InterestDistribution::class);
    }
}
