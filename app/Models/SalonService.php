<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalonService extends Model
{
    public const CATEGORIES = [
        'hair'   => 'Hair',
        'nails'  => 'Nails',
        'spa'    => 'Spa & Massage',
        'barber' => 'Barber',
        'makeup' => 'Makeup & Lashes',
        'other'  => 'Other',
    ];

    protected $fillable = [
        'salon_id',
        'name',
        'category',
        'description',
        'price',
        'duration_minutes',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    // Kung walang in-upload na larawan, gagamit ng default na larawan ayon sa kategorya
    public function imageUrl(): string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }

        $category = array_key_exists($this->category, self::CATEGORIES) ? $this->category : 'other';

        return asset("images/services/{$category}.svg");
    }
}
