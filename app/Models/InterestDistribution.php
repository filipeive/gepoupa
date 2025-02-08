<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterestDistribution extends Model
{
    protected $fillable = [
        'cycle_id',
        'user_id',
        'amount',
        'distribution_date',
        'description'
    ];

    protected $dates = [
        'distribution_date'
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