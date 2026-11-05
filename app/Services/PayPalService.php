<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    public function enabled(): bool
    {
        return filled(config('services.paypal.client_id')) && filled(config('services.paypal.secret'));
    }

    public function currency(): string
    {
        return strtoupper((string) config('services.paypal.currency', 'PHP'));
    }

    // Halagang ipapadala sa PayPal. Kung USD ang gamit, kino-convert mula sa piso.
    public function formatAmount(float $php): string
    {
        if ($this->currency() === 'PHP') {
            return number_format($php, 2, '.', '');
        }

        $rate = max((float) config('services.paypal.php_per_usd', 56), 0.01);

        return number_format($php / $rate, 2, '.', '');
    }

    /**
     * Gumagawa ng PayPal order. Nagbabalik ng ['id' => ..., 'approve_url' => ...], o null kung nabigo.
     */
    public function createOrder(string $description, float $amountPhp, string $returnUrl, string $cancelUrl): ?array
    {
        $payload = [
            'intent'         => 'CAPTURE',
            'purchase_units' => [[
                'description' => mb_substr($description, 0, 120),
                'amount'      => [
                    'currency_code' => $this->currency(),
                    'value'         => $this->formatAmount($amountPhp),
                ],
            ]],
            'payment_source' => [
                'paypal' => [
                    'experience_context' => [
                        'brand_name'          => 'SalonSync',
                        'user_action'         => 'PAY_NOW',
                        'shipping_preference' => 'NO_SHIPPING',
                        'return_url'          => $returnUrl,
                        'cancel_url'          => $cancelUrl,
                    ],
                ],
            ],
        ];

        try {
            $response = $this->request()->post($this->base() . '/v2/checkout/orders', $payload);

            if (! $response->successful()) {
                Log::warning('PayPal create order failed: HTTP ' . $response->status() . ' ' . $response->body());
                return null;
            }

            $order = $response->json();
            $link  = collect($order['links'] ?? [])
                ->first(fn ($l) => in_array($l['rel'] ?? '', ['payer-action', 'approve'], true));

            if (empty($order['id']) || ! $link) {
                Log::warning('PayPal create order returned no approval link: ' . $response->body());
                return null;
            }

            return ['id' => $order['id'], 'approve_url' => $link['href']];
        } catch (\Throwable $e) {
            Log::warning('PayPal create order error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Kinukuha ang bayad pagkatapos aprubahan ng customer. Nagbabalik ng order, o null kung nabigo.
     */
    public function captureOrder(string $orderId): ?array
    {
        if (! preg_match('/^[A-Za-z0-9]{10,40}$/', $orderId)) {
            return null;
        }

        try {
            $response = $this->request()
                ->withBody('{}', 'application/json')
                ->post($this->base() . "/v2/checkout/orders/{$orderId}/capture");

            if ($response->successful()) {
                return $response->json();
            }

            // Kung na-capture na dati (halimbawa nag-refresh ng page), kunin na lang ang order
            if (str_contains($response->body(), 'ORDER_ALREADY_CAPTURED')) {
                return $this->getOrder($orderId);
            }

            Log::warning('PayPal capture failed: HTTP ' . $response->status() . ' ' . $response->body());
        } catch (\Throwable $e) {
            Log::warning('PayPal capture error: ' . $e->getMessage());
        }

        return null;
    }

    public function getOrder(string $orderId): ?array
    {
        try {
            $response = $this->request()->get($this->base() . "/v2/checkout/orders/{$orderId}");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning('PayPal get order error: ' . $e->getMessage());
        }

        return null;
    }

    private function base(): string
    {
        return config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    private function token(): ?string
    {
        try {
            $response = Http::withBasicAuth(
                (string) config('services.paypal.client_id'),
                (string) config('services.paypal.secret')
            )
                ->asForm()
                ->timeout(15)
                ->post($this->base() . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::warning('PayPal token failed: HTTP ' . $response->status() . ' ' . $response->body());
        } catch (\Throwable $e) {
            Log::warning('PayPal token error: ' . $e->getMessage());
        }

        return null;
    }

    private function request(): PendingRequest
    {
        return Http::withToken((string) $this->token())
            ->acceptJson()
            ->timeout(20);
    }
}