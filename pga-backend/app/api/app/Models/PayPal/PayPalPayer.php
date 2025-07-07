<?php

namespace App\Models\PayPal;

use Illuminate\Database\Eloquent\Model;

class PayPalPayer extends Model
{
    protected $table = 'paypal_payers';

    protected $fillable = [
        'paypal_account_id',
        'email',
        'name',
        'country_code',
        'status',
    ];

    public function payments()
    {
        return $this->hasMany(PayPalPayment::class, 'payer_id');
    }
}

