<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Service extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id', 'slug',
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

    protected static function booted(): void
    {
        static::creating(function (Service $service) {
            if (empty($service->slug)) {
                $service->slug = static::generateUniqueSlug((int) $service->provider_id, (string) $service->name);
            }
        });
    }

    protected static function generateUniqueSlug(int $providerId, string $name): string
    {
        $base = Str::slug($providerId . '-' . $name);
        if ($base === '') {
            $base = 'service';
        }
        $slug = $base;
        $i = 2;
        while (DB::table('services')->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
