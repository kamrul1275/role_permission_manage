<?php

namespace App\Livewire\UserRole;

use App\Models\User;
use App\Models\RoleAndPermission;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;

class CreateUserComponent extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $selectedRoles = [];
    public $roles;

    public function mount()
    {
        $this->roles = RoleAndPermission::all();
    }

    public function toggleRole($roleId)
    {
        if (in_array($roleId, $this->selectedRoles)) {
            $this->selectedRoles = array_diff($this->selectedRoles, [$roleId]);
        } else {
            $this->selectedRoles[] = $roleId;
        }
    }

    public function removeRole($roleId)
    {
        $this->selectedRoles = array_diff($this->selectedRoles, [$roleId]);
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|same:password_confirmation',
            'selectedRoles' => 'required|array',
            'selectedRoles.*' => 'exists:role_and_permissions,id',
        ]);

        $user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Attach roles
        $user->rolePermissions()->sync($this->selectedRoles);

        session()->flash('success', 'User created successfully!');
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'selectedRoles']);

        return $this->redirect(route('user_list'), navigate: true);
    }

    #[Layout('components.layouts.app.base')]
    public function render()
    {
        return view('livewire.user-role.create-user-component');
    }
}
