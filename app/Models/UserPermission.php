<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $fillable = [
        'user_id',
        'sidebar_permissions',
        'page_wise_permissions',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


// In your UserPermission model
public function getSidebarPermissionsAttribute($value)
{
    if (is_array($value)) {
        return $value;
    }
    
    if (empty($value)) {
        return [];
    }
    
    $decoded = json_decode($value, true);
    return is_array($decoded) ? $decoded : [];
}

public function getPageWisePermissionsAttribute($value)
{
    if (is_array($value)) {
        return $value;
    }
    
    if (empty($value)) {
        return [];
    }
    
    $decoded = json_decode($value, true);
    return is_array($decoded) ? $decoded : [];
}











}
