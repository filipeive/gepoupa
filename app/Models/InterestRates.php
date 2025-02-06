<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class interestRates extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['rate', 'effective_date', 'description'];
    protected $dates = ['effective_date', 'deleted_at'];

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