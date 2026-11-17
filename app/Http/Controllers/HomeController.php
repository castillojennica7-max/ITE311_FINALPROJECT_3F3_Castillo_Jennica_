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
            $response = Http::timeout(5)->acceptJson()->get($url, $query);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning('Strapi request failed: ' . $e->getMessage());
        }

        return null;
    }

    // Ang rich text ng Strapi ay dumarating bilang JSON blocks; ginagawa nating plain text.
    private function blocksToText($nodes): string
    {
        $out = '';

        foreach ((array) $nodes as $node) {
            if (($node['type'] ?? '') === 'text') {
                $out .= $node['text'] ?? '';
                continue;
            }

            $out .= $this->blocksToText($node['children'] ?? []);

            if (in_array($node['type'] ?? '', ['paragraph', 'heading', 'list-item', 'quote', 'code'])) {
                $out .= "\n";
            }
        }

        return trim($out);
    }
}
