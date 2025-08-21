<?php

namespace App\Livewire;

use App\Models\Sidebar;
use Livewire\Component;
use App\Helpers\SidebarHelper;
use Livewire\Attributes\Layout;

class DashboardComponent extends Component
{
    #[Layout('components.layouts.app.base')]
    public function render()
    {
        // new (Sidebar::class); // Ensure Sidebar model is loaded
        //  dd((new SidebarHelper())->getSidebar());
        return view('livewire.dashboard-component');
    }
}
