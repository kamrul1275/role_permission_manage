<?php

namespace App\Livewire\UserRole;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UserPermission;
use Livewire\Attributes\Layout;
use App\Models\RoleAndPermission;

class UserRoleComponent extends Component
{
    use WithPagination;

    public $roles;
    public $selectedUser = null;
    public $selectedRole = null;
    public $userPermissions;
    public $searchName = '';
    public $searchEmail = '';
    public $searchRole = '';

    public function mount()
    {
        $this->authorize('viewAny', User::class);
        $this->roles = RoleAndPermission::all();
    }

    public function updatingSearchName()
    {
        $this->resetPage();
    }

    public function updatingSearchEmail()
    {
        $this->resetPage();
    }

    public function updatingSearchRole()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->searchName = '';
        $this->searchEmail = '';
        $this->searchRole = '';
        $this->resetPage();
    }

    public function delete($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            session()->flash('error', 'UserRole not found!');
            return;
        }

        if ($user->rolePermissions->contains('role_name', 'SuperAdmin')) {
            session()->flash('error', 'SuperAdmin cannot be deleted!');
            return;
        }

        $this->authorize('delete', $user);

        $user->delete();

        // Tell frontend to re-run feather
        $this->dispatch('refresh-feather');

        session()->flash('success', 'User has been deleted!');
    }

    #[Layout('components.layouts.app.base')]
    // public function render_old()
    // {
    //     $users = User::with(['rolePermissions', 'userPermissions'])
    //         ->where(function($query) {
    //             // Search by name
    //             if (!empty($this->searchName)) {
    //                 $query->where('name', 'like', '%' . $this->searchName . '%');
    //             }

    //             // Search by email
    //             if (!empty($this->searchEmail)) {
    //                 $query->where('email', 'like', '%' . $this->searchEmail . '%');
    //             }

    //             // Search by role
    //             if (!empty($this->searchRole)) {
    //                 $query->whereHas('rolePermissions', function ($q) {
    //                     $q->where('role_name', 'like', '%' . $this->searchRole . '%');
    //                 });
    //             }
    //         })
    //         ->latest()
    //         ->paginate(10);

    //     return view('livewire.user-role.user-role-component', [
    //         'users' => $users,
    //     ]);
    // }









    public function render()
    {
        $users = User::with(['rolePermissions', 'userPermissions'])
            ->where(function ($query) {
                // Search by name (case-insensitive)
                if (!empty($this->searchName)) {
                    $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->searchName) . '%']);
                }

                // Search by email (case-insensitive)
                if (!empty($this->searchEmail)) {
                    $query->whereRaw('LOWER(email) LIKE ?', ['%' . strtolower($this->searchEmail) . '%']);
                }

                // Search by role (case-insensitive)
                if (!empty($this->searchRole)) {
                    $query->whereHas('rolePermissions', function ($q) {
                        // Use LOWER() for case-insensitive search
                        $q->whereRaw('LOWER(role_name) LIKE ?', ['%' . strtolower($this->searchRole) . '%']);
                    });
                }
            })
            ->latest()
            ->paginate(10);

        return view('livewire.user-role.user-role-component', [
            'users' => $users,
        ]);
    }
}
