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
        ];
    }

    /**
     * Check if user has specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'superadmin') {
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
