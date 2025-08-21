<?php

namespace App\Livewire\UserPermission;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UserPermission;
use Livewire\Attributes\Layout;

class UserPermissionComponent extends Component
{

    use WithPagination; // Add this
    protected $paginationTheme = 'bootstrap'; // For Bootstrap styling
    public function delete($id)
    {
        UserPermission::findOrFail($id)->delete();
        return $this->redirect(route('user_permission'), navigate: true);
    }



    #[Layout('components.layouts.app.base')]

    public function render()
{
     $this->authorize('viewAny', UserPermission::class);

    $userPermissions = UserPermission::with('user.rolePermissions')->latest()->paginate(5);

//  dd($userPermissions);

    return view('livewire.user-permission.user-permission-component', [
        'userPermissions' => $userPermissions,
    ]);
}

}
