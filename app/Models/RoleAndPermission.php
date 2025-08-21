<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleAndPermission extends Model
{
    protected $fillable = [
        'role_name',
        'sidebar_permissions',
        'page_wise_permissions',
    ];

    protected $casts = [
        'sidebar_permissions' => 'array',
        'page_wise_permissions' => 'array',
    ];



    public function users()
{
    return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
}

}
