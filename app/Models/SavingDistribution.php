<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'cycle_id',
        'user_id',
        'total_saved',
        'distribution_date'
    ];

    protected $casts = [
        'distribution_date' => 'date',
        'total_saved' => 'decimal:2',
    ];

    public function cycle()
    {
        return $this->belongsTo(SavingCycle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
