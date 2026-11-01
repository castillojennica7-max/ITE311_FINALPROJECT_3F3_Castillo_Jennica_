<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = ['salon_id', 'name', 'role', 'phone'];

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }
}