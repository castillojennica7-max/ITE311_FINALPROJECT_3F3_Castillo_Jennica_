<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Transaction;
use App\Notifications\PaymentReceived;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // Ipapadala ang customer sa PayPal
    public function checkout(Request $request, PayPalService $paypal, Appointment $appointment)
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        if ($appointment->status === 'cancelled') {
            return back()->with('error', 'Cancelled appointments cannot be paid.');
        }

        if ($appointment->transaction()->exists()) {
            return back()->with('error', 'This appointment is already paid.');
        }

        if (! $paypal->enabled()) {
            return back()->with('error', 'Online payment is not set up yet.');
        }

        $appointment->loadMissing('salon');

        $order = $paypal->createOrder(
            description: "{$appointment->service_name} at {$appointment->salon->name}",
            amountPhp: (float) $appointment->service_price,
            returnUrl: route('payments.success', $appointment),
            cancelUrl: route('appointments.index'),
        );

        if (! $order) {
            return back()->with('error', 'Could not start the payment. Please try again.');
        }

        // Itatali ang order sa appointment na ito, para hindi magamit ang order sa ibang appointment
        $request->session()->put('paypal_order.' . $appointment->id, $order['id']);

        return redirect()->away($order['approve_url']);
    }

    // Bumabalik ang customer dito pagkatapos aprubahan ang bayad sa PayPal
    public function success(Request $request, PayPalService $paypal, Appointment $appointment)
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        if ($existing = $appointment->transaction) {
            return redirect()->route('transactions.receipt', $existing);
        }

        $orderId  = (string) $request->query('token');
        $expected = (string) $request->session()->get('paypal_order.' . $appointment->id);

        // Kailangang ang order na ito ay ang ginawa natin para sa appointment na ito
        $order = ($orderId !== '' && hash_equals($expected, $orderId))
            ? $paypal->captureOrder($orderId)
            : null;

        $unit    = $order['purchase_units'][0] ?? [];
        $capture = $unit['payments']['captures'][0] ?? [];

        // Hindi basta maniniwala sa browser: sinusuri natin ang sagot ng PayPal mismo
        $valid = $order
            && ($order['status'] ?? null) === 'COMPLETED'
            && ($capture['status'] ?? null) === 'COMPLETED'
            && ($capture['amount']['currency_code'] ?? null) === $paypal->currency()
            && abs((float) ($capture['amount']['value'] ?? 0) - (float) $paypal->formatAmount((float) $appointment->service_price)) < 0.01;

        if (! $valid) {
            return redirect()->route('appointments.index')
                ->with('error', 'We could not confirm your payment. If you were charged, please contact the salon.');
        }

        $transaction = Transaction::firstOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'salon_id'           => $appointment->salon_id,
                'user_id'            => $appointment->user_id,
                'amount'             => $appointment->service_price,
                'method'             => 'paypal',
                'reference_no'       => 'TX-' . strtoupper(Str::random(8)),
                'provider_reference' => $capture['id'] ?? $order['id'],
                'paid_at'            => now(),
            ]
        );

        $request->session()->forget('paypal_order.' . $appointment->id);

        try {
            $request->user()->notify(new PaymentReceived($transaction));
        } catch (\Throwable $e) {
            Log::warning('Failed to send payment email: ' . $e->getMessage());
        }

        return redirect()->route('transactions.receipt', $transaction)
            ->with('success', 'Payment received. Thank you!');
    }
}