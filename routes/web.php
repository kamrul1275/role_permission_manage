<?php

use App\Livewire\Auth\Login;
use App\Livewire\CreatePermissionComponent;
use App\Livewire\CreateRoleComponent;
use App\Livewire\DashboardComponent;
use App\Livewire\EmployeeComponent;
use App\Livewire\PageWisePermission\CreatePageWisePermissionComponent;
use App\Livewire\PageWisePermission\EditPageWisePermission;
use App\Livewire\PageWisePermission\EditPageWisePermissionComponent;
use App\Livewire\PageWisePermission\PageWisePermissionComponent;
use App\Livewire\PermissionComponent;
use App\Livewire\Post\CreatePostComponent;
use App\Livewire\Post\EditPostComponent;
use App\Livewire\Post\PostComponent;
use App\Livewire\RoleComponent;
use App\Livewire\RolePermission\CreateRolePermissionComponent;
use App\Livewire\RolePermission\EditRolePermissionComponent;
use App\Livewire\RolePermission\RolePermissionComponent;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Sidebar\CreateSidebarComponent;
use App\Livewire\Sidebar\EditSidebarComponent;
use App\Livewire\Sidebar\SidebarComponent;
use App\Livewire\UserPermission\CreateUserPermissionComponent;
use App\Livewire\UserPermission\EditUserPermissionComponent;
use App\Livewire\UserPermission\UserPermissionComponent;
use App\Livewire\UserRole\AssignUserRoleComponent;
use App\Livewire\UserRole\CreateUserComponent;
use App\Livewire\UserRole\EditUserRoleComponent;
use App\Livewire\UserRole\UserRoleComponent;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('livewire.auth.login');
// });


Route::get('/',Login::class)->name('home');
// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});


Route::middleware(['auth'])->group(function () {


Route::get('/dashboard', DashboardComponent::class)->name('dashboard');

// Role & Permission Routes
Route::get('/role_permission', RolePermissionComponent::class)->name('role_permission');
Route::get('/create_role', CreateRolePermissionComponent::class)->name('create_role');
// Route::get('/delete_role/{id}', RolePermissionComponent::class)->name('delete_role');
// Route::post('/store_role_permission', CreateRolePermissionComponent::class)->name('store_role_permission');
Route::get('/edit_role_permission/{id}', EditRolePermissionComponent::class)->name('edit_role_permission');

// Page & Permission Routes
Route::get('/page_wise_permission', PageWisePermissionComponent::class)->name('page_wise_permission');
Route::get('/create_page_wise_permission', CreatePageWisePermissionComponent::class)->name('create_page_wise_permission');
// Route::post('/store_page_wise_permission', CreatePageWisePermissionComponent::class)->name('store_page_wise_permission');
Route::get('/edit_page_permission/{id}', EditPageWisePermission::class)->name('edit_page_permission');

Route::get('/user_permission', UserPermissionComponent::class)->name('user_permission');
Route::get('/create_user_permission', CreateUserPermissionComponent::class)->name('create_user_permission');
Route::get('/edit_user_permission/{id}', EditUserPermissionComponent::class)->name('edit_user_permission');

//Sidebar Routes
Route::get('/sidebar',SidebarComponent::class)->name('sidebar');
Route::get('/create_sidebar',CreateSidebarComponent::class)->name('create_sidebar');
Route::get('/edit_sidebar/{id}',EditSidebarComponent::class)->name('edit_sidebar');


// post

Route::get('/posts',PostComponent::class)->name('posts');
Route::get('/create_post', CreatePostComponent::class)->name('create_post');
Route::get('/edit_post/{postId}', EditPostComponent::class)->name('edit_post');
// Route::get('/delete_post/{id}', PostComponent::class)->name('delete_post');

// assign role in user


Route::get('/assign_user_role',AssignUserRoleComponent::class)->name('assign_user_role');
Route::get('/user_list',UserRoleComponent::class)->name('user_list');
// Route::get('/edit_user_role/{id}',EditUserRoleComponent::class)->name('edit_user_role');
Route::get('/edit_user_role/{id}', EditUserRoleComponent::class)->name('edit_user_role');
Route::get('/create_user',CreateUserComponent::class)->name('create_user');


});



require __DIR__ . '/auth.php';
