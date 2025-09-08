<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\\Database\\Factories\\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = ['admin', 'provider', 'customer'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'slug',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isRole(string $role): bool
    {
        return strcasecmp((string) $this->role, $role) === 0;
    }

    public function scopeProviders($q)
    {
        return $q->where('role', 'provider')->where('is_active', true);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function services()
    {
        return $this->hasMany(\App\Models\Service::class, 'provider_id');
    }

    public function getRouteKeyName(): string
    {
        return filled($this->slug ?? null) ? 'slug' : 'id';
    }

    

    public function providerSchedules()
    {
        return $this->hasMany(\App\Models\ProviderSchedule::class, 'provider_id');
    }

    public function timeOffs()
    {
        return $this->hasMany(\App\Models\TimeOff::class, 'provider_id');
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if (($user->role ?? null) === 'provider') {
                if (blank($user->slug) || $user->isDirty('name')) {
                    $base = Str::slug($user->name ?: 'provider');
                    $candidate = $base;
                    $i = 1;
                    while (static::where('slug', $candidate)->where('id', '!=', $user->id ?? 0)->exists()) {
                        $candidate = $base . '-' . $i++;
                    }
                    $user->slug = $candidate;
                }
            }
        });
    }

    protected static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'provider';
        }
        $slug = $base;
        $i = 2;
        while (DB::table('users')->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
