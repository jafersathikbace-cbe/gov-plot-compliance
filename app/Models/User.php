<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'mongodb';

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_chat_id',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function scopeRole($query, string|array $roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return $query->whereIn(
            'role',
            collect($roles)
                ->flatMap(fn ($role) => preg_split('/[|,]/', (string) $role))
                ->filter()
                ->values()
                ->all()
        );
    }

    public function hasRole(string $role): bool
    {
        return (string) $this->role === $role;
    }

    public function hasAnyRole(array|string $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        $allowed = collect($roles)
            ->flatMap(fn ($role) => preg_split('/[|,]/', (string) $role))
            ->filter()
            ->values();

        return $allowed->contains((string) $this->role);
    }

    public function getRoleNames(): Collection
    {
        return $this->role ? collect([(string) $this->role]) : collect();
    }

    public function syncRoles(array|string $roles): static
    {
        $role = collect(is_array($roles) ? $roles : [$roles])
            ->flatMap(fn ($item) => preg_split('/[|,]/', (string) $item))
            ->filter()
            ->first();

        $this->role = $role;
        $this->save();

        return $this;
    }
}