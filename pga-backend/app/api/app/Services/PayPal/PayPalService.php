<?php

namespace App\Services\PayPal;

use App\Models\Package\Package;
use App\Models\PayPal\PayPalAmount;
use App\Models\PayPal\PayPalPayer;
use App\Models\PayPal\PayPalPayment;
use Illuminate\Support\Facades\Http;

class PayPalService
{
    protected $clientId;
    protected $secret;
    protected $baseUrl;

    protected $oauth_endpoint = '/v1/oauth2/token';
    protected $checkout_endpoint = '/v2/checkout/orders';
    protected $payment_endpoint = '/v2/payments/captures';

    protected $ngrok_url;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');
        $this->baseUrl = config('services.paypal.mode') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $this->ngrok_url = env('NGROK_FORWARDING');
    }
    

    public function getAccessToken()
    {
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post("{$this->baseUrl}{$this->oauth_endpoint}", [
                'grant_type' => 'client_credentials'
            ]);

        return $response->json('access_token');
    }

    public function createOrder(Package $package)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}{$this->checkout_endpoint}", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => $package->activePrice->currency,
                        'value' => number_format($package->activePrice->amount, 2, '.', '')
                    ]
                ]],
                'application_context' => [
                    'return_url' => "{$this->ngrok_url}/paypal/success",
                    'cancel_url' => "{$this->ngrok_url}/paypal/cancel"
                ]
            ]);

        return $response->json();
    }

    public function captureOrder($orderId)
    {
        $accessToken = $this->getAccessToken();
        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->withBody('', 'application/json')
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

        return $response->json();
    }

    public function storeCapturedPayments(array $response) : PayPalPayment
    {
        $capture = $response['purchase_units'][0]['payments']['captures'][0] ?? null;
        $payer = $response['payer'] ?? null;

        // Save or retrieve payer
        $payerModel = PayPalPayer::firstOrCreate(
            ['paypal_account_id' => $payer['payer_id']],
            [
                'email' => $payer['email_address'] ?? null,
                'name' => trim(($payer['name']['given_name'] ?? '') . ' ' . ($payer['name']['surname'] ?? '')),
                'country_code' => $payer['address']['country_code'] ?? null,
                'status' => $response['payment_source']['paypal']['account_status'] ?? null,
            ]
        );

        // Save amount
        $amountModel = PayPalAmount::create([
            'currency' => $capture['amount']['currency_code'],
            'gross_amount' => $capture['amount']['value'],
            'paypal_fee' => $capture['seller_receivable_breakdown']['paypal_fee']['value'] ?? null,
            'net_amount' => $capture['seller_receivable_breakdown']['net_amount']['value'] ?? null,
            'receivable_amount' => $capture['seller_receivable_breakdown']['receivable_amount']['value'] ?? null,
            'exchange_rate' => $capture['seller_receivable_breakdown']['exchange_rate']['value'] ?? null,
            'source_currency' => $capture['seller_receivable_breakdown']['exchange_rate']['source_currency'] ?? null,
        ]);

        // Save payment
        $paymentModel = PayPalPayment::create([
            'order_id' => $response['id'],
            'capture_id' => $capture['id'] ?? null,
            'status' => $capture['status'] ?? 'COMPLETED',
            'is_sandbox' => config('services.paypal.mode') === 'sandbox',
            'payer_id' => $payerModel->id,
            'amount_id' => $amountModel->id,
        ]);

        // Save full log
        $paymentModel->logs()->create([
            'type' => 'CAPTURE_ORDER',
            'payload' => $response,
        ]);

        return $paymentModel;
    }
}