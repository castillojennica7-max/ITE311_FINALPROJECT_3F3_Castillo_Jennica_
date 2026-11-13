<?php

namespace App\Console\Commands;

use App\Models\Salon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AssignSalonCovers extends Command
{
    protected $signature = 'salons:assign-covers {--all : Palitan din ang mga cover na in-upload ng may-ari}';

    protected $description = 'Gamitin ang mga litrato sa public/images/covers/photos bilang cover photo ng mga salon';

    public function handle(): int
    {
        $dir   = public_path('images/covers/photos');
        $files = collect(glob($dir . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [])
            ->map(fn ($f) => basename($f))
            ->sort()
            ->values();

        if ($files->isEmpty()) {
            $this->error('No photos found. Put .jpg, .png, or .webp files in public/images/covers/photos first.');

            return self::FAILURE;
        }

        // Mga file na ang pangalan ay kapareho ng pangalan ng salon, halimbawa glam-haven-salon.jpg
        $bySlug = $files->mapWithKeys(fn ($f) => [Str::slug(pathinfo($f, PATHINFO_FILENAME)) => $f]);

        $used     = [];
        $assigned = 0;
        $next     = 0;

        foreach (Salon::orderBy('id')->get() as $salon) {
            $current = $salon->cover_path;

            // Huwag galawin ang cover na in-upload ng may-ari, maliban kung --all
            if ($current && ! str_starts_with($current, 'images/') && ! $this->option('all')) {
                continue;
            }

            $slug = Str::slug($salon->name);
            $file = $bySlug->get($slug)
                ?? $bySlug->first(fn ($f, $key) => Str::contains($slug, $key) || Str::contains($key, $slug));

            if (! $file) {
                // Kung walang tugma sa pangalan, umikot sa mga natitirang litrato
                $file = $files[$next % $files->count()];
                $next++;
            }

            $salon->update(['cover_path' => 'images/covers/photos/' . $file]);
            $assigned++;
            $this->line("{$salon->name} -> {$file}");
        }

        $this->info("Done: {$assigned} salon cover photo(s) updated.");

        return self::SUCCESS;
    }
}
