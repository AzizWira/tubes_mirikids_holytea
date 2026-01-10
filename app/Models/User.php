<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi mass assignment.
     */
    protected $fillable = [
        'username',
        'password',
        'role',      // admin1 | admin2
        'is_active', // true/false
    ];

    /**
     * Disembunyikan saat JSON serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast tipe data.
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Optional helper
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}
