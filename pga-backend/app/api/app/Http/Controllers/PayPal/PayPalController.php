<?php

namespace App\Http\Controllers\PayPal;

use App\Http\Controllers\Controller;
use App\Models\Package\Package;
use App\Services\paypal\PayPalService;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    protected $paypal;

    public function __construct(PayPalService $paypal)
    {
        $this->paypal = $paypal;
    }

    public function pay($packageId)
    {

        $package = Package::with('activePrice')->findOrFail($packageId);
        if (!$package->activePrice) {
            return response()->json(['message' => 'No active price found for this package.'], 422);
        }

        $order = $this->paypal->createOrder($package);

        return redirect()->away(
            collect($order['links'])->firstWhere('rel', 'approve')['href']
        );
    }

    public function success(Request $request)
    {
        $orderId = $request->query('token');
        $result = $this->paypal->captureOrder((string) $orderId);

        $payment = $this->paypal->storeCapturedPayments($result);

        return response()->json([
            'message' => 'Payment successful!',
            'details' => $result,
            'payment' => $payment,
        ]);
    }

    public function cancel()
    {
        return response()->json([
            'message' => 'Payment was cancelled.'
        ]);
    }
}
