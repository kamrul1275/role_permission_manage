<?php

namespace App\Livewire\RolePermission;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\RoleAndPermission;

class RolePermissionComponent extends Component
{
    use WithPagination; // Add this
    protected $paginationTheme = 'bootstrap'; // For Bootstrap styling


    public $role_name;
    public $sidebar_permissions;
    public $page_wise_permissions;



    protected function getListeners()
    {
        return [
            // ... other listeners
            'delete' => 'delete',
        ];
    }

    public function delete($id)
    {
        $role = RoleAndPermission::find($id);

        if (!$role) {
            session()->flash('error', 'Role not found!');
            return;
        }

        if ($role->role_name === 'Super Admin' || $role->role_name === 'SuperAdmin') {
            session()->flash('error', 'SuperAdmin cannot be deleted!');
            return;
        }

        $this->authorize('delete', $role);

        $role->delete();
        session()->flash('success', 'Role has been deleted!');
    }






    #[Layout('components.layouts.app.base')]
    public function render()
    {
        $this->authorize('viewAny', RoleAndPermission::class);
        $roles = RoleAndPermission::latest()->paginate(5);
        // dd($roles);
        return view('livewire.role-permission.role-permission-component', compact('roles'));
    }
}
