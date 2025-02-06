<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'payment_date',
        'proof_file'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function socialFund()
    {
        return $this->hasOne(SocialFund::class, 'user_id', 'user_id')
            ->whereRaw('YEAR(social_funds.payment_date) = ?', [date('Y', strtotime($this->payment_date))])
            ->whereRaw('MONTH(social_funds.payment_date) = ?', [date('m', strtotime($this->payment_date))]);
    }

}