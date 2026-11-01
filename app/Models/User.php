<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Route;

#[Fillable(['name', 'username', 'email', 'google_id', 'avatar', 'password', 'role', 'domain_akses', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Hierarki: super_user (1 orang, kendali penuh) > admin (per domain) > user (read-only) */
    public const ROLES = [
        'super_user' => 'Super User',
        'admin' => 'Admin',
        'user' => 'User (read-only)',
    ];

    /** slug domain_akses => label */
    public const DOMAINS = [
        'batubara' => 'Batubara',
        'mineral_logam' => 'Mineral Logam',
        'mineral_bukan_logam' => 'Mineral Bukan Logam',
        'panas_bumi' => 'Panas Bumi',
        'gambut' => 'Gambut',
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
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function domainLabel(): string
    {
        return $this->domain_akses ? (self::DOMAINS[$this->domain_akses] ?? $this->domain_akses) : '-';
    }

    public function getRedirectUrlAfterLogin(): string
    {
        if ($this->role === 'super_user') {
            return route('superuser.dashboard');
        }

        if ($this->role === 'admin') {
            $grafikRoute = match ($this->domain_akses) {
                'batubara' => 'admin.batubara.grafik.index',
                'mineral_logam' => 'admin.mineral-logam.grafik.index',
                'mineral_bukan_logam' => 'admin.mineral-bukan-logam.grafik.index',
                'panas_bumi' => 'admin.panas-bumi.grafik.index',
                'gambut' => 'admin.gambut.grafik.index',
                default => null,
            };

            if ($grafikRoute && Route::has($grafikRoute)) {
                return route($grafikRoute);
            }
        }

        return route('user.dashboard');
    }
}
