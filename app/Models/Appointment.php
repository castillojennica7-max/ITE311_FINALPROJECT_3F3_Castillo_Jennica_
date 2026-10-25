<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'staff_id',
        'service_document_id',
        'service_name',
        'service_price',
        'service_duration_minutes',
        'appointment_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_at' => 'datetime',
        'service_price'  => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}