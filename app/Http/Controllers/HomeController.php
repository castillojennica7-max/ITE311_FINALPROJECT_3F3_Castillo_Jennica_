<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Salon;
use App\Models\SalonPromotion;
use App\Models\SalonService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $base = rtrim(config('services.strapi.url'), '/');

        // Galing sa Strapi CMS: nilalaman ng home page, announcements, at subscription plans
        $home          = $this->fetch("$base/api/home-page");
        $announcements = $this->fetch("$base/api/announcements", [
            'sort'       => 'publishedAt:desc',
            'pagination' => ['pageSize' => 3],
        ]);
        $plans = $this->fetch("$base/api/plans", ['sort' => 'price:asc']);
        $featuredServices = $this->fetch("$base/api/featured-services", ['sort' => 'sortOrder:asc', 'populate' => 'image']);

        $announcementList = collect($announcements['data'] ?? [])->map(fn ($a) => [
            'title' => $a['title'] ?? '',
            'text'  => $this->blocksToText($a['content'] ?? []),
            'date'  => isset($a['publishedAt']) ? Carbon::parse($a['publishedAt'])->format('M d, Y') : null,
        ])->all();

        // Galing sa database ng SalonSync: mga salon at promo ng mga ito
        $salons = Salon::active()->latest()->limit(6)->get();

        $promotions = SalonPromotion::current()
            ->whereHas('salon', fn ($q) => $q->active())
            ->with('salon')
            ->latest()
            ->limit(6)
            ->get();

        $stats = [
            'salons'   => Salon::active()->count(),
            'services' => SalonService::where('is_active', true)->whereHas('salon', fn ($q) => $q->active())->count(),
            'bookings' => Appointment::count(),
        ];

        return view('home', [
            'home'          => $home['data'] ?? null,
            'announcements' => $announcementList,
            'plans'         => $plans['data'] ?? [],
            'featuredServices' => $featuredServices['data'] ?? [],
            'salons'        => $salons,
            'promotions'    => $promotions,
            'stats'         => $stats,
            'cmsOnline'     => $home !== null,
        ]);
    }

    private function fetch(string $url, array $query = []): ?array
{
    try {
        $token = config('services.strapi.token');

        $request = Http::timeout(10)->acceptJson();

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get($url, $query);

        if ($response->successful()) {
            return $response->json();
        }

        Log::warning('Strapi API returned an error', [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

    } catch (\Throwable $e) {
        Log::warning('Strapi request failed: ' . $e->getMessage());
    }

    return null;
}


    private function blocksToText(array $blocks): string
    {
        $lines = [];

        foreach ($blocks as $block) {
            $text = '';

            if (isset($block['text'])) {
                $text = $block['text'];
            } elseif (isset($block['children']) && is_array($block['children'])) {
                $text = $this->blocksToText($block['children']);
            }

            if ($text !== '') {
                $lines[] = $text;
            }
        }

        return implode("\n", $lines);
    }

}
