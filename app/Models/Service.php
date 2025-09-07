<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id',
        'name',
        'duration_minutes',
        'price',
        'description',
        'is_active',
    ];

    /**
     * Get the provider (User) that owns the service.
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
