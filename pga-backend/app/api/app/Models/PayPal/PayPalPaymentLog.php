<?php

namespace App\Models\PayPal;

use Illuminate\Database\Eloquent\Model;

class PayPalPaymentLog extends Model
{
    protected $table = 'paypal_payment_logs';

    protected $fillable = [
        'paypal_payment_id',
        'type',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(PayPalPayment::class, 'paypal_payment_id');
    }
}

