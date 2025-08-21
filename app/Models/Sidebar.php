<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sidebar extends Model
{
    protected $fillable = [
        'sidebar_id',
        'position',
        'element_name',
        'element_url',
        'sidebar_icon',
        'section' // Add this if you're using section grouping
    ];

    // Accessor to make $sidebar->icon work
    public function getIconAttribute()
    {
        return $this->sidebar_icon;
    }

    public function children()
    {
        return $this->hasMany(Sidebar::class, 'sidebar_id')->orderBy('position');
    }

    public function parent()
    {
        return $this->belongsTo(Sidebar::class, 'sidebar_id');
    }
}