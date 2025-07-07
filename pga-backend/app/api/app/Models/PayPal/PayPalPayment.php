<?php

namespace App\Models\PayPal;

use Illuminate\Database\Eloquent\Model;

class PayPalPayment extends Model
{
    protected $table = 'paypal_payments';

    protected $fillable = [
        'order_id',
        'capture_id',
        'status',
        'is_sandbox',
        'payer_id',
        'amount_id',
    ];

    public function payer()
    {
        return $this->belongsTo(PayPalPayer::class);
    }

    public function amount()
    {
        return $this->belongsTo(PayPalAmount::class);
    }

    public function logs()
    {
        return $this->hasMany(PayPalPaymentLog::class, 'paypal_payment_id');
    }
}

