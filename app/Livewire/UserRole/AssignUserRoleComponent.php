<?php

namespace App\Livewire\UserRole;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\RoleAndPermission;

class AssignUserRoleComponent extends Component
{
    public $users;
    public $roles;
    public $selectedUser = null;
    public $selectedRole = null;
    public $selectedRoles = [];

    public function mount()
    {
        $this->users = User::all();
        $this->roles = RoleAndPermission::all();
    }

    // public function assignRole()
    // {
    //     $user = User::find($this->selectedUser);
    //     if ($user) {
    //         $user->role_id = $this->selectedRole;
    //         $user->save();
    //         session()->flash('success', 'Role assigned successfully!');
    //     } else {
    //         session()->flash('error', 'User not found!');
    //     }
    // }


    public function assignRole()
{
    $user = User::find($this->selectedUser);

    if ($user) {
        // Sync roles (replace existing with the selected ones)
        $user->rolePermissions()->sync($this->selectedRoles);

        session()->flash('success', 'Roles assigned successfully!');
    } else {
        session()->flash('error', 'User not found!');
    }
}


    #[Layout('components.layouts.app.base')]
    public function render()
    {
        $this->authorize('create', User::class);
        return view('livewire.user-role.assign-user-role-component');
    }
}
