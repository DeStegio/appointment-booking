<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'name',
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
        return $q->where('role', 'provider');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function services()
    {
        return $this->hasMany(\App\Models\Service::class, 'provider_id');
    }

    public function providerSchedules()
    {
        return $this->hasMany(\App\Models\ProviderSchedule::class, 'provider_id');
    }

    public function timeOffs()
    {
        return $this->hasMany(\App\Models\TimeOff::class, 'provider_id');
    }
}
