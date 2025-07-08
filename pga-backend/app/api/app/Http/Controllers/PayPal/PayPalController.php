<?php

namespace App\Http\Controllers\PayPal;

use App\Http\Controllers\Controller;
use App\Models\Package\Package;
use App\Services\paypal\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\ReceiptMail;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    protected $paypal;

    public function __construct(PayPalService $paypal)
    {
        $this->paypal = $paypal;
    }

    /**
     * Create PayPal order and redirect user to approval URL.
     */
    public function pay($packageId)
    {
        $package = Package::with('activePrice')->findOrFail($packageId);
        if (!$package->activePrice) {
            return response()->json(['message' => 'No active price found for this package.'], 422);
        }

        // Create PayPal order
        $order = $this->paypal->createOrder($package);

        // Redirect to PayPal approval page
        return redirect()->away(
            collect($order['links'])->firstWhere('rel', 'approve')['href']
        );
    }

    /**
     * Handle PayPal return URL after approval (manual capture + email receipt).
     */
    public function success(Request $request)
    {
        $orderId = $request->query('token');

        // Manual Capture of Payment
        $result = $this->paypal->captureOrder((string) $orderId);
        $payment = $this->paypal->storeCapturedPayments($result);

        try {
            $resource = $result['purchase_units'][0]['payments']['captures'][0];
            $payer = $result['payer'];

            $details = [
                'name' => ($payer['name']['given_name'] ?? '') . ' ' . ($payer['name']['surname'] ?? ''),
                'amount' => $resource['amount']['value'] . ' ' . $resource['amount']['currency_code'],
            ];

            $pdf = Pdf::loadView('pdf.receipt', $details);
            $pdfPath = storage_path('app/public/receipt-' . $payment->id . '.pdf');
            $pdf->save($pdfPath);

            Mail::to($payer['email_address'] ?? '')->send(new ReceiptMail($details, $pdfPath));
        } catch (\Exception $e) {
            Log::error('Error sending receipt on manual success: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Payment successful!',
            'details' => $result,
            'payment' => $payment,
        ]);
    }

    /**
     * Handle PayPal cancel redirect.
     */
    public function cancel()
    {
        return response()->json([
            'message' => 'Payment was cancelled.'
        ]);
    }
}
