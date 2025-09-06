<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'provider_id',
        'customer_id',
        'service_id',
        'start_at',
        'end_at',
        'status',
        'notes',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getDurationMinutesAttribute(): int
    {
        $start = $this->start_at instanceof Carbon ? $this->start_at : Carbon::parse((string) $this->start_at);
        $end = $this->end_at instanceof Carbon ? $this->end_at : Carbon::parse((string) $this->end_at);
        return (int) $start->diffInMinutes($end);
    }
}
