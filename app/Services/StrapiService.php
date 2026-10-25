<?php

namespace App\Services;

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
}