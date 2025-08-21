<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserRolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
            return $user->hasPermission('User Role:view') ||
           $user->hasPermission('Assign User Role:view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
                   return $user->hasPermission('User Role:view') ||
           $user->hasPermission('Assign User Role:view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
                   return $user->hasPermission('User Role:create') ||
           $user->hasPermission('Assign User Role:create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
                   return $user->hasPermission('User Role:edit') ||
           $user->hasPermission('Assign User Role:edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
                   return $user->hasPermission('User Role:delete') ||
           $user->hasPermission('Assign User Role:delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
