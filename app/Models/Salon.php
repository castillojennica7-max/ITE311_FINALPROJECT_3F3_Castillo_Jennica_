<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Salon extends Model
{
    public const DAYS = [
        'mon' => 'Monday',
        'tue' => 'Tuesday',
        'wed' => 'Wednesday',
        'thu' => 'Thursday',
        'fri' => 'Friday',
        'sat' => 'Saturday',
        'sun' => 'Sunday',
    ];

    protected $fillable = [
        'owner_id',
        'name',
        'phone',
        'address',
        'tagline',
        'about',
        'logo_path',
        'cover_path',
        'opening_hours',
    ];

    protected $casts = [
        'opening_hours' => 'array',
    ];

    // ---------- Relationships ----------

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(SalonService::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(SalonPromotion::class);
    }

    // ---------- Subscription ----------

    // Aktibo ang salon kung may aktibong subscription
    public function isActive(): bool
    {
        return $this->subscriptions()->active()->exists();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('subscriptions', fn ($s) => $s->active());
    }

    // ---------- Opening hours ----------

    public static function defaultHours(): array
    {
        $hours = [];

        foreach (array_keys(self::DAYS) as $key) {
            $hours[$key] = ['open' => '09:00', 'close' => '18:00', 'closed' => $key === 'sun'];
        }

        return $hours;
    }

    // Kumpletong oras para sa pitong araw
    public function hours(): array
    {
        $saved = $this->opening_hours;

        if (! is_array($saved) || $saved === []) {
            return self::defaultHours();
        }

        $out = [];

        foreach (array_keys(self::DAYS) as $key) {
            $d = $saved[$key] ?? [];

            $out[$key] = [
                'open'   => $d['open'] ?? '09:00',
                'close'  => $d['close'] ?? '18:00',
                'closed' => (bool) ($d['closed'] ?? false),
            ];
        }

        return $out;
    }

    public function hoursFor(Carbon $date): array
    {
        return $this->hours()[strtolower($date->format('D'))];
    }

    // Bukas ba ang salon sa oras na ito (at kasya ang serbisyo bago magsara)?
    public function isOpenAt(Carbon $when, int $durationMinutes = 0): bool
    {
        $day = $this->hoursFor($when);

        if ($day['closed']) {
            return false;
        }

        $open  = $when->copy()->setTimeFromTimeString($day['open']);
        $close = $when->copy()->setTimeFromTimeString($day['close']);

        return $when->greaterThanOrEqualTo($open)
            && $when->copy()->addMinutes($durationMinutes)->lessThanOrEqualTo($close);
    }

    public static function formatTime(string $time): string
    {
        return Carbon::parse($time)->format('g:i A');
    }

    public function todayLabel(): string
    {
        $d = $this->hoursFor(now());

        return $d['closed']
            ? 'Closed today'
            : 'Today ' . self::formatTime($d['open']) . ' - ' . self::formatTime($d['close']);
    }

    public function dayHoursLabel(Carbon $when): string
    {
        $d    = $this->hoursFor($when);
        $name = $when->format('l');

        return $d['closed']
            ? "The salon is closed on {$name}s."
            : "On {$name}s the salon is open from " . self::formatTime($d['open']) . ' to ' . self::formatTime($d['close']) . '.';
    }

    // ---------- Images and helpers ----------

    private function assetFor(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        // Ang mga larawang galing sa public/images ay hindi dumadaan sa storage
        return str_starts_with($path, 'images/') ? asset($path) : asset('storage/' . $path);
    }

    public function coverUrl(): ?string
    {
        return $this->assetFor($this->cover_path);
    }

    public function logoUrl(): ?string
    {
        return $this->assetFor($this->logo_path);
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->name));

        return strtoupper(mb_substr($words[0] ?? '', 0, 1) . mb_substr($words[1] ?? '', 0, 1));
    }

    public function startingPrice(): ?float
    {
        $min = $this->services()->where('is_active', true)->min('price');

        return $min !== null ? (float) $min : null;
    }
}
