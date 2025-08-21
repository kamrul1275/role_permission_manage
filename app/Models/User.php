<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Many-to-Many: A user can have multiple roles
     */

// public function rolePermissions()
// {
//     return $this->belongsTo(RoleAndPermission::class, 'role_id');
// }

    // public function rolePermissions()
    // {
    //     return $this->belongsToMany(RoleAndPermission::class, 'role_user', 'user_id', 'role_id');
    // }





    /**
     * One-to-One: A user may have custom permissions
     */
    public function userPermissions()
    {
        return $this->hasOne(UserPermission::class, 'user_id');
    }

    /**
     * One-to-Many: A user can have many posts
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }



    public function rolePermissions()
{
    return $this->belongsToMany(RoleAndPermission::class, 'role_user', 'user_id', 'role_id');
}

/**
 * Get merged sidebar permissions from all roles
 */
public function getAllRoleSidebarPermissions(): array
{
    return $this->rolePermissions
        ->pluck('sidebar_permissions') // this gives array per role
        ->filter()                     // remove null
        ->flatten(1)                   // merge nested arrays
        ->unique()                     // remove duplicates
        ->toArray();
}

/**
 * Get merged page-wise permissions from all roles
 */
public function getAllRolePagePermissions(): array
{
    return $this->rolePermissions
        ->pluck('page_wise_permissions')
        ->filter()
        ->flatten(1)
        ->unique()
        ->toArray();
}


    /**
     * Check if user has a specific permission
     */
    // public function hasPermission(string $permission): bool
    // {
    //     // User-specific permissions
    //     $userPermissions = $this->userPermissions()
    //         ->pluck('page_wise_permissions')
    //         ->flatten()
    //         ->toArray();

    //     if (in_array($permission, $userPermissions)) {
    //         return true;
    //     }

    //     // Collect permissions from all roles
    //     $rolePermissions = $this->roles
    //         ->pluck('page_wise_permissions')
    //         ->flatten()
    //         ->toArray();

    //     return in_array($permission, $rolePermissions);
    // }


    public function hasPermission(string $permission): bool
{
    // 1️⃣ Check user-specific custom permissions
    $userPermissions = $this->userPermissions
        ? $this->userPermissions->page_wise_permissions ?? []
        : [];

    if (in_array($permission, $userPermissions)) {
        return true;
    }

    // 2️⃣ Check role-based permissions
    $rolePermissions = $this->getAllRolePagePermissions();

    return in_array($permission, $rolePermissions);
}

}
