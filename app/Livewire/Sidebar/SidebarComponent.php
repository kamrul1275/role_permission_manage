<?php

namespace App\Livewire\Sidebar;

use App\Models\Sidebar;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Volt\Compilers\Mount;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SidebarComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';


    public function delete($id)
    {
        Sidebar::findOrFail($id)->delete();
        // dd()
        session()->flash('message', 'Sidebar deleted successfully.');
        $this->resetPage(); // Reset pagination if current page becomes empty
    }


    #[Layout('components.layouts.app.base')]
    public function render()
    {

        $this->authorize('viewAny', Sidebar::class);

        $sidebars = Sidebar::with('parent')->latest()->paginate(5);

        // dd($sidebars);

        return view('livewire.sidebar.sidebar-component', [
            'sidebars' => $sidebars
        ]);
    }
}
