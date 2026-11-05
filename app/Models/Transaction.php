<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'salon_id',
        'appointment_id',
        'user_id',
        'recorded_by',
        'amount',
        'method',
        'reference_no',
        'provider_reference',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'stripe' => 'Card (Stripe)',
            'paypal' => 'PayPal',
            'gcash'  => 'GCash',
            'card'   => 'Card',
            'cash'   => 'Cash',
            default  => ucfirst($this->method),
        };
    }
}