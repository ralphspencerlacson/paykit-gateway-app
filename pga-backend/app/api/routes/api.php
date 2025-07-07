<?php

use App\Http\Controllers\PayPal\PayPalController;
use App\Http\Controllers\Webhook\PayPal\PayPalWebhookController;
use Illuminate\Support\Facades\Route;

// ----------------------------------------------------------------------------------------------------------------
// PayPal
// ----------------------------------------------------------------------------------------------------------------
Route::get('/paypal/pay/{packageId}', [PayPalController::class, 'pay'])->name('paypal.pay');
Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');


// ----------------------------------------------------------------------------------------------------------------
// Webhooks
// ----------------------------------------------------------------------------------------------------------------
Route::post('/webhook/paypal', [PayPalWebhookController::class, 'handle']);