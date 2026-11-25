<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';

    public const ROLE_MANAGER = 'manager';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MAIN_ADMIN = 'main_admin';

    public const ROLES = [
        self::ROLE_USER,
        self::ROLE_MANAGER,
        self::ROLE_ADMIN,
        self::ROLE_MAIN_ADMIN,
    ];

    public const STAFF_ROLES = [
        self::ROLE_MANAGER,
        self::ROLE_ADMIN,
        self::ROLE_MAIN_ADMIN,
    ];

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, is_array($roles) ? $roles : [$roles], true);
    }

    public function isMainAdmin(): bool
    {
        return $this->role === self::ROLE_MAIN_ADMIN;
    }

    public function canAccessAdmin(): bool
    {
        return $this->hasRole(self::STAFF_ROLES);
    }

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
}
