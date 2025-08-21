<?php

namespace App\Policies;

use App\Models\PageAndPermission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PageWisePermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('Page wise Permissions.view') ||
            $user->hasPermission('Create Page Wise Permission.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PageAndPermission $pageAndPermission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('Page wise Permissions.create') ||
            $user->hasPermission('Create Page Wise Permission.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PageAndPermission $pageAndPermission): bool
    {
        return $user->hasPermission('Page wise Permissions.edit') ||
            $user->hasPermission('Create Page Wise Permission.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PageAndPermission $pageAndPermission): bool
    {
        return $user->hasPermission('Page wise Permissions.delete') ||
            $user->hasPermission('Create Page Wise Permission.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PageAndPermission $pageAndPermission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PageAndPermission $pageAndPermission): bool
    {
        return false;
    }
}
