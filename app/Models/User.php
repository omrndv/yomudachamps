<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**a
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
        'permissions',
        'is_active',
        'last_seen_at',
        'force_logout_at',
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
            'permissions' => 'array',
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
            'force_logout_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke aktivitas terakhir admin
     */
    public function latestActivity()
    {
        return $this->hasOne(AdminActivity::class)->latestOfMany();
    }

    /**
     * Periksa apakah admin sedang online (aktif dalam 5 menit terakhir)
     */
    public function isOnline(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }
        return $this->last_seen_at->gt(now()->subMinutes(5));
    }

    /**
     * Check if user has specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'superadmin' || $permission === 'dashboard') {
            return true;
        }

        $perms = $this->permissions;
        if (is_null($perms)) {
            $perms = ["dashboard", "seasons", "notes", "faqs", "activity_log"];
        } elseif (!is_array($perms)) {
            $perms = json_decode($perms, true);
        }
        
        if (empty($perms)) {
            $perms = ["dashboard", "seasons", "notes", "faqs", "activity_log"];
        }

        return in_array($permission, $perms);
    }
}
