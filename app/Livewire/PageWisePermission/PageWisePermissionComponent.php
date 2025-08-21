<?php

namespace App\Livewire\PageWisePermission;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PageAndPermission;
use App\Models\RoleAndPermission;

class PageWisePermissionComponent extends Component
{
  
    use WithPagination; // Add this
    protected $paginationTheme = 'bootstrap'; // For Bootstrap styling


    public function delete($id)
    {
        PageAndPermission::findOrFail($id)->delete();

    // return redirect()->route('page_wise_permission'); // Redirect to the role permission page
    return $this->redirect(route('page_wise_permission'), navigate: true);
    }


  #[Layout('components.layouts.app.base')]
    public function render()
    {

        //  $this->authorize('viewAny', PageAndPermission::class);
         $pages = PageAndPermission::latest()->paginate(5);
        return view('livewire.page-wise-permission.page-wise-permission-component',
            [
                'pages' => $pages,
            ]
        );
    }
}
