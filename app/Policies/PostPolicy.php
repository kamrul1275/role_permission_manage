<?php


namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
public function viewAny(User $user): bool
{
    return $user->hasPermission('Post List:view') ||
           $user->hasPermission('Create Post:view');
}

public function view(User $user): bool
{
    return $user->hasPermission('Post List:view') ||
           $user->hasPermission('Create Post:view');
}

public function create(User $user)
{
    return $user->hasPermission('Post List:create') ||
           $user->hasPermission('Create Post:create');
}

public function update(User $user, Post $post)
{
    return $user->hasPermission('Post List:edit') ||
           $user->hasPermission('Create Post:edit');
}

public function delete(User $user, Post $post)
{
    return $user->hasPermission('Post List:delete') ||
           $user->hasPermission('Create Post:delete');
}

}
