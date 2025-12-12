<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterestRates extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['rate', 'effective_date', 'description'];

    protected $casts = [
        'effective_date' => 'date',
        'deleted_at' => 'datetime',
        'rate' => 'decimal:2',
    ];

    public function scopeLatest($query)
    {
        return $query->orderBy('effective_date', 'desc');
    }
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function distributions()
    {
        return $this->hasMany(InterestDistribution::class);
    }
}