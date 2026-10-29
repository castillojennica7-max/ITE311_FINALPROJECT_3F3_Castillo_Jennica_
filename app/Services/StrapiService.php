<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StrapiService
{
    /**
     * Kinukuha ang listahan ng services mula sa Strapi CMS.
     * Nagbabalik ng null kapag hindi maabot ang CMS.
     */
    public function services(): ?array
    {
        $base = rtrim(config('services.strapi.url'), '/');

        try {
            $response = Http::timeout(5)->acceptJson()->get("$base/api/services", [
                'sort' => 'name:asc',
                'pagination' => ['pageSize' => 100],
            ]);

            if ($response->successful()) {
                return $response->json('data', []);
            }
        } catch (\Throwable $e) {
            Log::warning('Strapi request failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Ipinapadala ang kopya ng user sa Strapi (walang password).
     */
    public function syncUser(User $user): bool
    {
        return $this->upsert('customer-profiles', 'laravel_user_id', $user->id, [
            'laravel_user_id' => $user->id,
            'name'            => $user->name,
            'email'           => $user->email,
            'user_role'       => $user->role,
            'registered_at'   => $user->created_at?->toIso8601String(),
        ]);
    }

    /**
     * Ipinapadala ang buod ng appointments ngayong araw sa Strapi.
     */
    public function syncDailyReport(): bool
    {
        $counts = Appointment::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $today = now()->toDateString();

        return $this->upsert('reports', 'report_date', $today, [
            'report_date'        => $today,
            'total_appointments' => (int) $counts->sum(),
            'pending_count'      => (int) $counts->get('pending', 0),
            'confirmed_count'    => (int) $counts->get('confirmed', 0),
            'completed_count'    => (int) $counts->get('completed', 0),
            'cancelled_count'    => (int) $counts->get('cancelled', 0),
            'total_customers'    => User::where('role', 'customer')->count(),
            'completed_revenue'  => (float) Appointment::where('status', 'completed')->sum('service_price'),
        ]);
    }

    /**
     * Kapag may umiiral nang entry na may parehong key, ina-update ito.
     * Kapag wala, gumagawa ng bago.
     */
    private function upsert(string $endpoint, string $keyField, string|int $keyValue, array $data): bool
    {
        $token = config('services.strapi.token');

        if (! $token) {
            return false;
        }

        $base = rtrim(config('services.strapi.url'), '/');

        try {
            $http = Http::withToken($token)->timeout(3)->acceptJson();

            $lookup = $http->get("$base/api/$endpoint", [
                'filters' => [$keyField => ['$eq' => $keyValue]],
            ]);

            if (! $lookup->successful()) {
                Log::warning("Strapi lookup failed for $endpoint: HTTP {$lookup->status()} {$lookup->body()}");
                return false;
            }

            $existing = $lookup->json('data.0');
            $payload  = ['data' => $data];

            $save = $existing
                ? $http->put("$base/api/$endpoint/{$existing['documentId']}", $payload)
                : $http->post("$base/api/$endpoint", $payload);

            if (! $save->successful()) {
                Log::warning("Strapi save failed for $endpoint: HTTP {$save->status()} {$save->body()}");
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning("Strapi sync error for $endpoint: " . $e->getMessage());
            return false;
        }
    }
}