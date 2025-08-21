<?php

namespace App\Livewire\UserRole;

use App\Models\User;
use App\Models\Sidebar;
use Livewire\Component;
use App\Models\UserPermission;
use Livewire\Attributes\Layout;
use App\Models\PageAndPermission;
use App\Models\RoleAndPermission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class EditUserRoleComponent extends Component
{
    // ========================================
    // PROPERTIES - Data Storage
    // ========================================

    // Basic user info
    public $userPermissionId;           // The user ID we're editing
    public $users;                      // All users for dropdown
    public $user_id;                    // Selected user ID
    public $selectedUser = null;        // User object being edited

    // UPDATED: Changed to handle multiple roles
    public $selectedRoles = [];         // Array of selected role IDs

    // Permission arrays - These hold the actual permissions
    public $sidebarPermissions = [];    // User's additional sidebar permissions (editable)
    public $pageWisePermissions = [];   // User's additional page permissions (editable)

    // Data arrays - These hold the available options
    public $sidebarData = [];           // Available sidebar elements
    public $pageWisePermissionData = []; // Available page operations
    public $roles = [];                 // Available roles

    // Role-based permissions - These come from the role (read-only, shown as disabled)
    public $roleSidebarPermissions = []; // Sidebar permissions from role
    public $rolePageWisePermissions = []; // Page permissions from role

    // ========================================
    // INITIALIZATION - Setup Component
    // ========================================

    public function mount($id)
    {
        $this->userPermissionId = $id;

        // STEP 1: Load basic data
        $this->loadBasicData();

        // STEP 2: Load selected user and their current settings
        $this->loadUserData();

        // STEP 3: Load available permissions structure
        $this->loadPermissionStructure();
    }

    /**
     * Load users and roles from database
     */
    private function loadBasicData()
    {
        $this->users = User::with(['rolePermissions', 'userPermissions'])->get();
        $this->roles = RoleAndPermission::all();
    }

    /**
     * Load the selected user and their current permissions
     */
    private function loadUserData()
    {
        // Find the user we're editing
        $user = User::with('rolePermissions')->findOrFail($this->userPermissionId);
        $this->user_id = $user->id;
        $this->selectedUser = $user;

        // Load user's current roles from pivot table
        $this->selectedRoles = $user->rolePermissions->pluck('id')->toArray();

        // Load user's ADDITIONAL permissions (on top of role permissions)
        $userPermission = UserPermission::where('user_id', $user->id)->first();
        if ($userPermission) {
            $this->sidebarPermissions = $userPermission->sidebar_permissions ?: [];
            $this->pageWisePermissions = $userPermission->page_wise_permissions ?: [];
        } else {
            $this->sidebarPermissions = [];
            $this->pageWisePermissions = [];
        }

        // Load role-based permissions from all selected roles
        $this->loadRoleBasedPermissions();
    }

    /**
     * Load permissions from all selected roles
     */


    /**
     * Load the structure of available permissions
     */
    private function loadPermissionStructure()
    {
        // Load page-wise permission data (what operations are available for each page)
        $this->pageWisePermissionData = PageAndPermission::all()
            ->groupBy('page_name')
            ->map(function ($group) {
                return $group->map(function ($item) {
                    $item['operations_array'] = json_decode($item['operations'], true) ?: [];
                    return $item;
                })->toArray();
            })->toArray();

        // Get only pages that have operations defined
        $pagesWithOperations = collect($this->pageWisePermissionData)
            ->filter(function ($operations) {
                return collect($operations)->some(function ($operation) {
                    return !empty($operation['operations_array']);
                });
            })
            ->keys()
            ->toArray();

        // Load sidebar structure (only pages that have operations)
        $this->sidebarData = Sidebar::whereNotNull('sidebar_id')
            ->get()
            ->filter(function ($sidebar) use ($pagesWithOperations) {
                return in_array($sidebar->element_name, $pagesWithOperations);
            })
            ->groupBy('sidebar_id')
            ->map(fn($group) => $group->toArray())
            ->toArray();
    }

    // ========================================
    // EVENT HANDLERS - React to Changes
    // ========================================

    /**
     * UPDATED METHOD: This runs when selectedRoles array changes
     * This handles multiple role selection
     */







public function updatedSelectedRoles($value)
{
    // Skip if value is empty or same as current
    if (empty($value) && empty($this->selectedRoles)) {
        return;
    }

    // Dispatch loading state
    $this->dispatch('dropdown-loading');

    try {
        // Ensure we have an array
        if (!is_array($value)) {
            $value = [];
        }

        // Update the property
        $this->selectedRoles = array_map('intval', $value);

        // Clear everything if no roles selected
        if (empty($this->selectedRoles)) {
            $this->clearAllRolePermissions();
        } else {
            // Load permissions from all selected roles
            $this->loadRoleBasedPermissions();

            // RESET USER-SPECIFIC PERMISSIONS to match combined role permissions
            $this->sidebarPermissions = $this->roleSidebarPermissions;
            $this->pageWisePermissions = $this->rolePageWisePermissions;
        }

        // Dispatch events for frontend
        $this->dispatch('roles-updated', $this->selectedRoles);

        // Log for debugging
        Log::info('Roles changed to: ' . implode(', ', $this->selectedRoles), [
            'role_sidebar_permissions' => $this->roleSidebarPermissions,
            'role_page_permissions' => $this->rolePageWisePermissions
        ]);

    } catch (\Exception $e) {
        Log::error('Error updating selected roles: ' . $e->getMessage(), [
            'selected_roles' => $this->selectedRoles,
            'error' => $e->getTraceAsString()
        ]);

        session()->flash('error', 'Error updating role permissions. Please try again.');
    } finally {
        // Always dispatch loaded state
        $this->dispatch('dropdown-loaded');
    }
}

/**
 * Load permissions from all selected roles - OPTIMIZED
 */
private function loadRoleBasedPermissions()
{
    // Reset arrays
    $this->roleSidebarPermissions = [];
    $this->rolePageWisePermissions = [];

    if (empty($this->selectedRoles)) {
        return;
    }

    // Get all selected roles in one query
    $roles = RoleAndPermission::whereIn('id', $this->selectedRoles)->get();

    $allRoleSidebarPermissions = [];
    $allRolePagePermissions = [];

    foreach ($roles as $role) {
        // Handle both JSON and array formats
        $roleSidebarPerms = $role->sidebar_permissions;
        if (is_string($roleSidebarPerms)) {
            $roleSidebarPerms = json_decode($roleSidebarPerms, true) ?: [];
        } elseif (!is_array($roleSidebarPerms)) {
            $roleSidebarPerms = [];
        }

        $rolePagePerms = $role->page_wise_permissions;
        if (is_string($rolePagePerms)) {
            $rolePagePerms = json_decode($rolePagePerms, true) ?: [];
        } elseif (!is_array($rolePagePerms)) {
            $rolePagePerms = [];
        }

        // Merge permissions from all roles
        $allRoleSidebarPermissions = array_merge($allRoleSidebarPermissions, $roleSidebarPerms);
        $allRolePagePermissions = array_merge($allRolePagePermissions, $rolePagePerms);
    }

    // Remove duplicates and reindex
    $this->roleSidebarPermissions = array_values(array_unique($allRoleSidebarPermissions));
    $this->rolePageWisePermissions = array_values(array_unique($allRolePagePermissions));
}

/**
 * Add method to force refresh permissions table
 */
public function refreshPermissions()
{
    $this->dispatch('dropdown-loading');
    
    try {
        $this->loadRoleBasedPermissions();
        $this->dispatch('permissions-refreshed');
    } catch (\Exception $e) {
        Log::error('Error refreshing permissions: ' . $e->getMessage());
    } finally {
        $this->dispatch('dropdown-loaded');
    }
}










    





    /**
     * Clear all role-based permissions
     */
    private function clearAllRolePermissions()
    {
        $this->roleSidebarPermissions = [];
        $this->rolePageWisePermissions = [];
        $this->sidebarPermissions = [];
        $this->pageWisePermissions = [];
    }

    /**
     * Handle user selection change (currently not used since user is pre-selected)
     */
    public function updatedUserId()
    {
        $this->selectedUser = User::find($this->user_id);
        $this->sidebarPermissions = [];
        $this->pageWisePermissions = [];
    }

    // ========================================
    // PERMISSION CHECKING METHODS
    // ========================================

    /**
     * Check if a sidebar element is selected
     * Returns true if it's either in user permissions OR role permissions
     */
    public function isSidebarSelected($elementName): bool
    {
        return in_array($elementName, $this->sidebarPermissions) ||
            in_array($elementName, $this->roleSidebarPermissions);
    }

    /**
     * Check if a page permission is selected
     * Returns true if it's either in user permissions OR role permissions
     */
    public function isPagePermissionSelected($permissionValue): bool
    {
        return in_array($permissionValue, $this->pageWisePermissions) ||
            in_array($permissionValue, $this->rolePageWisePermissions);
    }

    public function toggleSidebarPermission($elementName)
    {
        // PROTECTION: Don't allow toggling role-based permissions
        if (in_array($elementName, $this->roleSidebarPermissions)) {
            return; // Do nothing if it's a role-based permission
        }

        $isCurrentlySelected = in_array($elementName, $this->sidebarPermissions);

        if ($isCurrentlySelected) {
            // REMOVE: Uncheck sidebar and remove all related page permissions
            $this->removeSidebarPermission($elementName);
            $this->removeAllPagePermissionsForElement($elementName);
        } else {
            // ADD: Check sidebar and auto-add all page permissions for this element
            $this->addSidebarPermission($elementName);
            $this->autoAddPagePermissionsForElement($elementName);
        }

        $this->cleanupPermissionArrays();
    }

    /**
     * Toggle page permission when checkbox is clicked
     * IMPORTANT: Cannot toggle role-based permissions (they're disabled in UI)
     */
    public function togglePagePermission($permissionValue)
    {
        // PROTECTION: Don't allow toggling role-based permissions
        if (in_array($permissionValue, $this->rolePageWisePermissions)) {
            return; // Do nothing if it's a role-based permission
        }

        // Extract page name from permission format "PageName:operation"
        [$pageName, $operation] = explode(':', $permissionValue, 2);

        $isCurrentlySelected = in_array($permissionValue, $this->pageWisePermissions);

        if ($isCurrentlySelected) {
            // REMOVE: Uncheck this page permission
            $this->removePagePermission($permissionValue);
        } else {
            // ADD: Check this page permission
            $this->addPagePermission($permissionValue);
        }

        // AUTO-MANAGE SIDEBAR: If page has any permissions, sidebar should be checked
        $this->autoManageSidebarBasedOnPagePermissions($pageName);

        $this->cleanupPermissionArrays();
    }

    // ========================================
    // HELPER METHODS FOR PERMISSION MANAGEMENT
    // ========================================

    private function removeSidebarPermission($elementName)
    {
        $this->sidebarPermissions = array_filter(
            $this->sidebarPermissions,
            fn($item) => $item !== $elementName
        );
    }

    private function addSidebarPermission($elementName)
    {
        if (!in_array($elementName, $this->sidebarPermissions)) {
            $this->sidebarPermissions[] = $elementName;
        }
    }

    private function removePagePermission($permissionValue)
    {
        $this->pageWisePermissions = array_filter(
            $this->pageWisePermissions,
            fn($item) => $item !== $permissionValue
        );
    }

    private function addPagePermission($permissionValue)
    {
        if (!in_array($permissionValue, $this->pageWisePermissions)) {
            $this->pageWisePermissions[] = $permissionValue;
        }
    }

    private function removeAllPagePermissionsForElement($elementName)
    {
        $this->pageWisePermissions = array_filter(
            $this->pageWisePermissions,
            fn($permission) => !str_starts_with($permission, $elementName . ':')
        );
    }

    private function autoAddPagePermissionsForElement($elementName)
    {
        // Only add if no page permissions exist for this element
        $hasExistingPagePermissions = collect($this->pageWisePermissions)
            ->contains(fn($perm) => str_starts_with($perm, $elementName . ':'));

        if (!$hasExistingPagePermissions && !empty($this->pageWisePermissionData[$elementName])) {
            foreach ($this->pageWisePermissionData[$elementName] as $permGroup) {
                foreach ($permGroup['operations_array'] ?? [] as $operation) {
                    $this->addPagePermission($elementName . ':' . $operation);
                }
            }
        }
    }

    private function autoManageSidebarBasedOnPagePermissions($pageName)
    {
        // Check if this page has any user-specific OR role-based permissions
        $hasUserPagePermissions = collect($this->pageWisePermissions)
            ->contains(fn($perm) => str_starts_with($perm, $pageName . ':'));

        $hasRolePagePermissions = collect($this->rolePageWisePermissions)
            ->contains(fn($perm) => str_starts_with($perm, $pageName . ':'));

        if ($hasUserPagePermissions || $hasRolePagePermissions) {
            // Page has permissions, ensure sidebar is selected
            $this->addSidebarPermission($pageName);
        } else {
            // No permissions for this page, remove sidebar (but only if not role-based)
            if (!in_array($pageName, $this->roleSidebarPermissions)) {
                $this->removeSidebarPermission($pageName);
            }
        }
    }

    private function cleanupPermissionArrays()
    {
        $this->sidebarPermissions = array_unique(array_values($this->sidebarPermissions));
        $this->pageWisePermissions = array_unique(array_values($this->pageWisePermissions));
    }

    // ========================================
    // BULK ACTIONS - Clear All / Select All
    // ========================================

    /**
     * Clear all USER-SPECIFIC permissions (keep role-based permissions)
     */
    public function clearAllPermissions()
    {
        // Reset to only role-based permissions
        $this->sidebarPermissions = $this->roleSidebarPermissions;
        $this->pageWisePermissions = $this->rolePageWisePermissions;
    }

    /**
     * Select ALL available permissions (role-based + all possible additional permissions)
     */
    public function selectAllPermissions()
    {
        // Start with role-based permissions and add all available options
        $this->sidebarPermissions = $this->getAllAvailableSidebarPermissions();
        $this->pageWisePermissions = $this->getAllAvailablePagePermissions();

        $this->cleanupPermissionArrays();
    }

    private function getAllAvailableSidebarPermissions(): array
    {
        $allPermissions = $this->roleSidebarPermissions; // Start with role-based

        foreach ($this->sidebarData as $parentId => $childItems) {
            if ($parentId !== null && is_array($childItems)) {
                foreach ($childItems as $child) {
                    $elementName = $child['element_name'] ?? '';
                    if ($elementName && !in_array($elementName, $allPermissions)) {
                        $allPermissions[] = $elementName;
                    }
                }
            }
        }

        return $allPermissions;
    }

    private function getAllAvailablePagePermissions(): array
    {
        $allPermissions = $this->rolePageWisePermissions; // Start with role-based

        foreach ($this->pageWisePermissionData as $elementName => $permGroups) {
            foreach ($permGroups as $permGroup) {
                foreach ($permGroup['operations_array'] ?? [] as $operation) {
                    $permissionValue = $elementName . ':' . $operation;
                    if (!in_array($permissionValue, $allPermissions)) {
                        $allPermissions[] = $permissionValue;
                    }
                }
            }
        }

        return $allPermissions;
    }

    // ========================================
    // LEGACY METHODS (kept for backward compatibility)
    // ========================================

    public function updatedSidebarPermissions($value, $name)
    {
        // This method handles array-level changes to sidebar permissions
        // Currently used for logging and complex permission synchronization
        Log::info('Sidebar Permissions Updated', [
            'current_sidebar_permissions' => $this->sidebarPermissions,
        ]);

        // Auto-sync page permissions based on sidebar changes
        $this->autoSyncPagePermissionsWithSidebar();
    }

    public function updatedPageWisePermissions($value, $name)
    {
        // This method handles array-level changes to page permissions
        Log::info('Page Permissions Updated', [
            'current_page_permissions' => $this->pageWisePermissions,
        ]);

        // Auto-sync sidebar permissions based on page changes
        $this->autoSyncSidebarWithPagePermissions();
    }

    private function autoSyncPagePermissionsWithSidebar()
    {
        if (!is_array($this->pageWisePermissions)) {
            $this->pageWisePermissions = [];
        }

        $allPageNames = array_keys($this->pageWisePermissionData);

        foreach ($allPageNames as $pageName) {
            $isSidebarSelected = in_array($pageName, $this->sidebarPermissions);

            if ($isSidebarSelected) {
                $hasAnyPageOperations = collect($this->pageWisePermissions)
                    ->contains(fn($perm) => str_starts_with($perm, $pageName . ':'));

                if (!$hasAnyPageOperations && isset($this->pageWisePermissionData[$pageName])) {
                    foreach ($this->pageWisePermissionData[$pageName] as $permissionItem) {
                        foreach ($permissionItem['operations_array'] ?? [] as $op) {
                            $this->addPagePermission($pageName . ':' . $op);
                        }
                    }
                }
            } else {
                $this->removeAllPagePermissionsForElement($pageName);
            }
        }

        $this->pageWisePermissions = array_values($this->pageWisePermissions);
    }

    private function autoSyncSidebarWithPagePermissions()
    {
        if (!is_array($this->sidebarPermissions)) {
            $this->sidebarPermissions = [];
        }

        foreach ($this->pageWisePermissionData as $pageName => $permissions) {
            $hasSelectedOperations = collect($this->pageWisePermissions)
                ->contains(fn($perm) => str_starts_with($perm, $pageName . ':'));

            if ($hasSelectedOperations) {
                $this->addSidebarPermission($pageName);
            } else {
                if (!in_array($pageName, $this->roleSidebarPermissions)) {
                    $this->removeSidebarPermission($pageName);
                }
            }
        }

        $this->cleanupPermissionArrays();
    }

    // ========================================
    // COMPUTED PROPERTIES - For UI Display
    // ========================================

    public function getSidebarPermissionsCountProperty(): int
    {
        return count(array_unique(array_merge($this->sidebarPermissions, $this->roleSidebarPermissions)));
    }

    public function getPageWisePermissionsCountProperty(): int
    {
        return count(array_unique(array_merge($this->pageWisePermissions, $this->rolePageWisePermissions)));
    }

    // ========================================
    // SAVE METHOD - Persist Changes
    // ========================================

    public function savePermissions()
    {
        // Validate input
        $this->validate([
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'exists:role_and_permissions,id',
            'user_id' => 'required|exists:users,id',
            'sidebarPermissions' => 'nullable|array',
            'pageWisePermissions' => 'nullable|array',
        ]);

        $user = User::findOrFail($this->user_id);

        // Sync user's roles using the pivot table
        $user->rolePermissions()->sync($this->selectedRoles);

        // IMPORTANT: Only save USER-SPECIFIC permissions (exclude role-based permissions)
        $userSpecificSidebarPermissions = array_diff($this->sidebarPermissions, $this->roleSidebarPermissions);
        $userSpecificPagePermissions = array_diff($this->pageWisePermissions, $this->rolePageWisePermissions);

        // Save only the additional permissions (not role-based ones)
        UserPermission::updateOrCreate(
            ['user_id' => $this->user_id],
            [
                'sidebar_permissions' => !empty($userSpecificSidebarPermissions) ?
                    json_encode(array_values($userSpecificSidebarPermissions)) : null,
                'page_wise_permissions' => !empty($userSpecificPagePermissions) ?
                    json_encode(array_values($userSpecificPagePermissions)) : null,
            ]
        );

        session()->flash('success', 'User roles and permissions updated successfully!');
        return $this->redirect(route('user_list'), navigate: true);
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    #[Layout('components.layouts.app.base')]
    public function render()
    {
        Gate::authorize('create', User::class);
        return view('livewire.user-role.edit-user-role-component');
    }
}
