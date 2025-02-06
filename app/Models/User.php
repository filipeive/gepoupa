<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'role' => 'string'
    ];

    // Relacionamentos
    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function socialFunds()
    {
        return $this->hasMany(SocialFund::class);
    }

    public function interestDistributions()
    {
        return $this->hasMany(InterestDistribution::class);
    }
    public function calculateTotalSavings()
    {
        return $this->savings()->sum('amount');
    }
    public function hasActiveLoan()
    {
        return $this->loans()
            ->where('status', '!=', 'paid')
            ->exists();
    }

    public function getTotalSavingsAttribute()
    {
        return $this->savings->sum('amount');
    }

    public function getPendingSocialFundsAttribute()
    {
        return $this->socialFunds->where('status', 'pending')->count();
    }

    public function getTotalLoansAttribute()
    {
        return $this->loans->where('status', 'approved')->sum('amount');
    }

    public function getCurrentMonthSavingAttribute()
    {
        return $this->savings()
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');
    }
}
