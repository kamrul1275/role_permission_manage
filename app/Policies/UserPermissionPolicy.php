<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Auth\Access\Response;

class UserPermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    // public function viewAny(User $user): bool
    // {

    //         logger()->info('Checking viewAny for user: ' . $user->name);
    //     logger()->info('User permissions: ', $user->userPermissions()->pluck('page_wise_permissions')->toArray());
    //     return $user->hasPermission('user_permission.view') ||
    //            $user->hasPermission('create_user_permission.view');
    // }



    public function viewAny(User $user): bool
    {

         logger()->info('Checking viewAny for user: ' . $user->name);
         logger()->info('User permissions: ', $user->userPermissions()->pluck('page_wise_permissions')->toArray());
        return $user->hasPermission('User Permission:view') ||
            $user->hasPermission('Create User Permission:view');
    }



    // public function viewAny(User $user): bool
    // {
    //     logger()->info('Checking viewAny for user: ' . $user->name);
    //     logger()->info('User permissions: ', $user->userPermissions()->pluck('page_wise_permissions')->toArray());

    //     return $user->hasPermission('User Permission') ||
    //            $user->hasPermission('create User Permission');
    // }


    /**
     * Determine whether the user can view the model.
     */
    // public function view(User $user, UserPermission $userPermission): bool
    // {
    //     return $user->hasPermission('user permission.view') ||
    //         $user->hasPermission('create user permission.view');
    // }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('User Permission:create') ||
            $user->hasPermission('Create User Permission:create');
    }




    public function update(User $user, UserPermission $userPermission): bool
{
    return $user->hasPermission('User Permission:edit') ||
           $user->hasPermission('Create User Permission:edit');
}

public function delete(User $user, UserPermission $userPermission): bool
{
    return $user->hasPermission('User Permission:delete') ||
           $user->hasPermission('Create User Permission:delete');
}

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserPermission $userPermission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserPermission $userPermission): bool
    {
        return false;
    }
}
