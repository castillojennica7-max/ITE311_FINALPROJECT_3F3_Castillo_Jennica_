<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salon extends Model
{
    protected $fillable = ['owner_id', 'name', 'phone', 'address'];

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

    // Aktibo ang salon kung may aktibong subscription
    public function isActive(): bool
    {
        return $this->subscriptions()->active()->exists();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('subscriptions', fn ($s) => $s->active());
    }
}