<?php

namespace App\Models\PayPal;

use Illuminate\Database\Eloquent\Model;

class PayPalAmount extends Model
{
    protected $table = 'paypal_amounts';

    protected $fillable = [
        'currency',
        'gross_amount',
        'paypal_fee',
        'net_amount',
        'receivable_amount',
        'exchange_rate',
        'source_currency',
    ];

    public function payments()
    {
        return $this->hasMany(PayPalPayment::class, 'amount_id');
    }
}

