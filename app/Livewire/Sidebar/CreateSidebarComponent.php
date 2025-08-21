<?php

namespace App\Livewire\Sidebar;

use App\Models\Sidebar;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CreateSidebarComponent extends Component
{

    use AuthorizesRequests;

    public $element_name;
    public $element_url;
    public $position;
    public $sidebar_id;
    public $sidebar_icon; // New field for sidebar icon
    public $allSidebars;

    public function mount()
    {
        $this->allSidebars = Sidebar::all();
        $this->authorize('create', \App\Models\Sidebar::class);
        // dd($this->allSidebars);
    }

    public function store()
    {
        $validated = $this->validate([
            'element_name' => 'required|string|max:255',
            'element_url' => 'nullable|string|max:255',
            'position' => 'required|integer|min:1',
            'sidebar_id' => 'nullable|exists:sidebars,id',
            'sidebar_icon' => 'nullable|string', // New field for sidebar icon
        ]);

        // Sidebar::create($validated);

        $data = new Sidebar();
        $data->element_name = $this->element_name;
        $data->element_url = $this->element_url;
        $data->position = $this->position;
        $data->sidebar_id = $this->sidebar_id;
        $data->sidebar_icon = $this->sidebar_icon; // Save the sidebar icon 

        // dd($data);
        $data->save();

        session()->flash('message', 'Sidebar created successfully.');

        return redirect()->route('sidebar');
    }



    #[Layout('components.layouts.app.base')]
    public function render()
    {
        $this->authorize('viewAny', Sidebar::class);
        return view('livewire.sidebar.create-sidebar-component');
    }
}
