<?php

namespace App\Providers;

use App\Models\Sidebar;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the Sidebar model to be used in views
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    Paginator::useBootstrap();
}
}
