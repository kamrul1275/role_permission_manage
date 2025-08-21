<?php

namespace App\Helpers;

use App\Models\Sidebar;

class SidebarHelper
{

    // public function getSidebar()
    // {

    //     return Sidebar::whereNull('sidebar_id')
    //         ->with('children')
    //         ->orderBy('position')
    //         ->get();
    // }


public function getSidebar()
{
    return Sidebar::whereNull('sidebar_id')
        ->with(['children', 'children.children']) 
        ->orderBy('position')
        ->get();
}


// public function getSidebar()
// {
//     return Sidebar::with([
//         'children.children.children' // load 3 levels deep
//     ])
//     ->whereNull('sidebar_id')   // only top-level
//     ->orderBy('position')
//     ->get();
// }

}