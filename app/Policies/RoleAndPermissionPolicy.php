<?php

namespace App\Policies;

use App\Models\RoleAndPermission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoleAndPermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */


    public function viewAny(User $user): bool
{
    return $user->hasPermission('Role List:view') ||
           $user->hasPermission('Create Role:view');
}

    /**
     * Determine whether the user can view the model.
     */
    // public function view(User $user, RoleAndPermission $roleAndPermission): bool
    // {

    // }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('Role List:create') ||
           $user->hasPermission('Create Role:create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RoleAndPermission $roleAndPermission): bool
    {
               return $user->hasPermission('Role List:edit') ||
           $user->hasPermission('Create Role:edit');
    }




// public function update(User $user, RoleAndPermission $roleAndPermission = null): bool
// {
//     return $user->hasPermission('Role List:edit') ||
//            $user->hasPermission('Create Role:edit');
// }







    /**
     * Determine whether the user can delete the model.
     */
public function delete(User $user, RoleAndPermission $roleAndPermission): bool
{
    // SuperAdmin role can never be deleted
    if ($roleAndPermission->name === 'SuperAdmin') {
        return false;
    }

    return $user->hasPermission('Role List:delete') ||
           $user->hasPermission('Create Role:delete');
}




    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RoleAndPermission $roleAndPermission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RoleAndPermission $roleAndPermission): bool
    {
        return false;
    }
}
