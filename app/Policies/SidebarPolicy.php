<?php

namespace App\Policies;

use App\Models\Sidebar;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SidebarPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
           return $user->hasPermission('Sidebar List:view') ||
           $user->hasPermission('Create Sidebar:view');

    }


    public function create(User $user): bool
{
    return $user->hasPermission('Sidebar List:create') ||
           $user->hasPermission('Create Sidebar:create');
}

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sidebar $sidebar): bool
    {
           return $user->hasPermission('Sidebar List:edit') ||
           $user->hasPermission('Create Sidebar:edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sidebar $sidebar): bool
    {
           return $user->hasPermission('Sidebar List:delete') ||
           $user->hasPermission('Create Sidebar:delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sidebar $sidebar): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sidebar $sidebar): bool
    {
        return false;
    }
}
