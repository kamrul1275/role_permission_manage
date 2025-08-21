<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageAndPermission extends Model
{
    protected function loadSidebarData()
{
    $items = \App\Models\Sidebar::all();
    $grouped = [];

    foreach ($items as $item) {
        $grouped[$item->parent_id][] = $item->toArray();
    }

    return $grouped;
}

}
