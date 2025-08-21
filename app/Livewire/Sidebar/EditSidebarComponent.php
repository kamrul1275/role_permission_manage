<?php

namespace App\Livewire\Sidebar;

use App\Models\Sidebar;
use Livewire\Component;
use Livewire\Attributes\Layout;

class EditSidebarComponent extends Component
{

    public $element_name;
    public $element_url;
    public $position;
    public $sidebar_id;
    public $sidebar; // holds the current Sidebar model
    public $allSidebars;

    public function mount($id)
    {
        $this->sidebar = Sidebar::findOrFail($id);

        $this->authorize('update', $this->sidebar);

        $this->element_name = $this->sidebar->element_name;
        $this->element_url = $this->sidebar->element_url;
        $this->position = $this->sidebar->position;
        $this->sidebar_id = $this->sidebar->sidebar_id;

        $this->allSidebars = Sidebar::where('id', '!=', $id)->get();
    }

    public function update()
    {
        $validated = $this->validate([
            'element_name' => 'required|string|max:255',
            'element_url' => 'nullable|string|max:255',
            'position' => 'required|integer|min:1',
            'sidebar_id' => 'nullable|exists:sidebars,id',
        ]);

        $this->sidebar->update($validated);

        session()->flash('message', 'Sidebar updated successfully.');

        return redirect()->route('sidebar');
    }


     #[Layout('components.layouts.app.base')]
    public function render()
    {
        return view('livewire.sidebar.edit-sidebar-component');
    }
}
