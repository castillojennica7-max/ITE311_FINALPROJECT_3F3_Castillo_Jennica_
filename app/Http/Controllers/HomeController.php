<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $base = rtrim(config('services.strapi.url'), '/');

        $home = $this->fetch("$base/api/home-page");
        $announcements = $this->fetch("$base/api/announcements", [
            'sort' => 'publishedAt:desc',
            'pagination' => ['pageSize' => 3],
        ]);
        $services = $this->fetch("$base/api/services", ['sort' => 'name:asc']);

        $announcementList = collect($announcements['data'] ?? [])->map(fn ($a) => [
            'title' => $a['title'] ?? '',
            'text'  => $this->blocksToText($a['content'] ?? []),
        ])->all();

        return view('home', [
            'home'          => $home['data'] ?? null,
            'announcements' => $announcementList,
            'services'      => $services['data'] ?? [],
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