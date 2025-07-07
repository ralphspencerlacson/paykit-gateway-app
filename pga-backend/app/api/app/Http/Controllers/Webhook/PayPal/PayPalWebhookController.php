<?php

namespace App\Http\Controllers\Webhook\PayPal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        Log::info('PayPal Webhook', ['payload' => $payload]);

        return response()->json(['status' => 'received']);
    }
}
