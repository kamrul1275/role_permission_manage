<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
// use Illuminate\Support\ServiceProvider;
use App\Models\Sidebar;
use App\Policies\PostPolicy;
use App\Models\UserPermission;
use App\Policies\UserRolePolicy;
use App\Models\PageAndPermission;
use App\Models\RoleAndPermission;
use App\Policies\CreateRoleAndPermissionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    protected $policies = [
        Post::class => \App\Policies\PostPolicy::class,
        UserPermission::class => \App\Policies\UserPermissionPolicy::class,
        PageAndPermission::class => \App\Policies\PageWisePermissionPolicy::class,
        Sidebar::class => \App\Policies\SidebarPolicy::class,
        RoleAndPermission::class => \App\Policies\RoleAndPermissionPolicy::class,
        // RoleAndPermission::class => \App\Policies\CreateRoleAndPermissionPolicy::class,
        User::class => \App\Policies\UserRolePolicy::class,
    ];



    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
