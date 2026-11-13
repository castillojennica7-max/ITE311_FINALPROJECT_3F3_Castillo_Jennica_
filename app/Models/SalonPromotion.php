<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalonPromotion extends Model
{
    protected $fillable = [
        'salon_id',
        'type',
        'title',
        'description',
        'discount_percent',
        'bundle_price',
        'included_services',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'bundle_price' => 'decimal:2',
        'valid_until'  => 'date',
        'is_active'    => 'boolean',
    ];

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    // Aktibo at hindi pa nag-e-expire
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($w) => $w->whereNull('valid_until')->orWhere('valid_until', '>=', today()->toDateString()));
    }

    public function isBundle(): bool
    {
        return $this->type === 'bundle';
    }

    public function badge(): string
    {
        if ($this->isBundle() && $this->bundle_price !== null) {
            return '₱' . number_format((float) $this->bundle_price) . ' bundle';
        }

        if ($this->discount_percent) {
            return $this->discount_percent . '% OFF';
        }

        return $this->isBundle() ? 'Bundle' : 'Promo';
    }

    public function includedList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $this->included_services))));
    }
}
