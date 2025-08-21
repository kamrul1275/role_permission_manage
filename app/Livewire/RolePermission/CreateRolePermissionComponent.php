<?php

namespace App\Livewire\RolePermission;

use Log;
use App\Models\Sidebar;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\PageAndPermission;
use App\Models\RoleAndPermission;

class CreateRolePermissionComponent extends Component
{
    public $roleName = '';
    public $sidebarData = [];
    public $pageWisePermissionData = [];
    public $sidebarPermissions = [];
    public $pageWisePermissions = [];

    public function mount()
    {
        $this->sidebarData = Sidebar::whereNotNull('sidebar_id')
            ->get()
            ->groupBy('sidebar_id')
            ->map(fn($group) => $group->toArray())
            ->toArray();

        $this->pageWisePermissionData = PageAndPermission::all()
            ->groupBy('page_name')
            ->map(function ($group) {
                return $group->map(function ($item) {
                    $item['operations_array'] = json_decode($item['operations'], true) ?: [];
                    return $item;
                })->toArray();
            })->toArray();

        // Initialize arrays
        $this->sidebarPermissions = [];
        $this->pageWisePermissions = [];
        
        // Clean up any existing invalid selections
        $this->cleanupInvalidSelections();
    }

    // Clean up any invalid selections (pages without operations)
    private function cleanupInvalidSelections()
    {
        // Remove sidebar permissions for pages without operations
        $this->sidebarPermissions = collect($this->sidebarPermissions)
            ->filter(function($permission) {
                return $this->hasPageOperations($permission);
            })
            ->values()
            ->toArray();

        // Remove page permissions for pages without operations
        $this->pageWisePermissions = collect($this->pageWisePermissions)
            ->filter(function($permission) {
                $parts = explode(':', $permission);
                $pageName = $parts[0] ?? null;
                return $pageName && $this->hasPageOperations($pageName);
            })
            ->values()
            ->toArray();
    }

    // Check if the page has any operations defined
    // public function hasPageOperations($pageName): bool
    // {
    //     if (!isset($this->pageWisePermissionData[$pageName])) {
    //         return false;
    //     }
        
    //     return collect($this->pageWisePermissionData[$pageName])
    //         ->pluck('operations_array')
    //         ->flatten()
    //         ->isNotEmpty();
    // }


    // In your Livewire component

public function hasPageOperations($pageName)
{
    // Check if page exists and has operations
    if (!isset($this->pageWisePermissionData[$pageName])) return false;

    foreach ($this->pageWisePermissionData[$pageName] as $permItem) {
        if (!empty($permItem['operations_array'])) return true;
    }
    return false;
}



    // Check if sidebar access exists for this element/page
    public function isSidebarAvailable($elementName): bool
    {
        foreach ($this->sidebarData as $items) {
            foreach ($items as $item) {
                if ($item['element_name'] === $elementName) {
                    return true;
                }
            }
        }
        return false;
    }

    // Check if sidebar should be selectable (has sidebar AND has operations)
    public function isSidebarSelectable($elementName): bool
    {
        return $this->isSidebarAvailable($elementName) && $this->hasPageOperations($elementName);
    }

    // Check if page operations should be shown
    public function shouldShowPageOperations($pageName): bool
    {
        return $this->isSidebarSelectable($pageName);
    }

    public function storeRolePermission()
    {
        // Enhanced validation
        $this->validate([
            'roleName' => 'required|string|max:255|unique:role_and_permissions,role_name',
            'sidebarPermissions' => 'nullable|array',
            'pageWisePermissions' => 'nullable|array',
        ], [
            'roleName.required' => 'Role name is required.',
            'roleName.unique' => 'This role name already exists.',
            'roleName.max' => 'Role name cannot exceed 255 characters.',
        ]);

        try {
            $rolePermission = new RoleAndPermission();
            $rolePermission->role_name = trim($this->roleName);
            $rolePermission->sidebar_permissions = $this->sidebarPermissions ?: [];
            $rolePermission->page_wise_permissions = $this->pageWisePermissions ?: [];
            $rolePermission->save();

            // Log successful creation
            \Log::info('Role created successfully', [
                'role_name' => $this->roleName,
                'sidebar_permissions' => $this->sidebarPermissions,
                'page_wise_permissions' => $this->pageWisePermissions,
            ]);

            // Show success message
            session()->flash('success', 'Role "' . $this->roleName . '" created successfully!');

            // Reset form and redirect
            $this->reset();
            return $this->redirect(route('role_permission'), navigate: true);
        } catch (\Exception $e) {
            // Log error
            \Log::error('Error creating role', [
                'error' => $e->getMessage(),
                'role_name' => $this->roleName,
                'sidebar_permissions' => $this->sidebarPermissions,
                'page_wise_permissions' => $this->pageWisePermissions,
            ]);

            // Show error message
            session()->flash('error', 'Failed to create role. Please try again.');
        }
    }

    // Handle sidebar permission updates
// public function updatedSidebarPermissions($value, $name)
// {
//     \Log::info('Sidebar Permissions Updated', [
//         'value' => $value,
//         'name' => $name,
//         'current_sidebar_permissions' => $this->sidebarPermissions,
//     ]);

//     $this->cleanupInvalidSelections();

//     if (!is_array($this->pageWisePermissions)) {
//         $this->pageWisePermissions = [];
//     }

//     // Remove sidebar selections for pages without operations
//     $this->sidebarPermissions = array_filter($this->sidebarPermissions, function ($sidebarElement) {
//         return $this->hasPageOperations($sidebarElement);
//     });

//     // Auto-select related page permissions when sidebar item is selected
//     if (is_array($this->sidebarPermissions)) {
//         $newPageWisePermissions = [];

//         foreach ($this->sidebarPermissions as $sidebarElement) {
//             if (isset($this->pageWisePermissionData[$sidebarElement]) && $this->hasPageOperations($sidebarElement)) {
//                 foreach ($this->pageWisePermissionData[$sidebarElement] as $permissionItem) {
//                     $operations = $permissionItem['operations_array'] ?? [];
//                     foreach ($operations as $op) {
//                         $permissionString = $sidebarElement . ':' . $op;
//                         if (!in_array($permissionString, $newPageWisePermissions)) {
//                             $newPageWisePermissions[] = $permissionString;
//                         }
//                     }
//                 }
//             }
//         }

//         $this->pageWisePermissions = array_values($newPageWisePermissions);
//     }

//     \Log::info('Updated page permissions:', $this->pageWisePermissions);
// }






// Handle sidebar permission updates
public function updatedSidebarPermissions($value, $name)
{
    \Log::info('Sidebar Permissions Updated', [
        'value' => $value,
        'name' => $name,
        'current_sidebar_permissions' => $this->sidebarPermissions,
    ]);

    $this->cleanupInvalidSelections();

    if (!is_array($this->pageWisePermissions)) {
        $this->pageWisePermissions = [];
    }

    // Remove sidebar selections for pages without operations
    $this->sidebarPermissions = array_filter($this->sidebarPermissions, function ($sidebarElement) {
        return $this->hasPageOperations($sidebarElement);
    });

    // Handle page permissions based on sidebar selection changes
    if (is_array($this->sidebarPermissions)) {
        // Get all page names that have operations
        $allPageNames = array_keys(array_filter($this->pageWisePermissionData, function($permissions, $pageName) {
            return $this->hasPageOperations($pageName);
        }, ARRAY_FILTER_USE_BOTH));

        // For each page that has operations
        foreach ($allPageNames as $pageName) {
            $isSidebarSelected = in_array($pageName, $this->sidebarPermissions);
            
            if ($isSidebarSelected) {
                // If sidebar is selected but no page operations are selected for this page,
                // then auto-select all operations for this page
                $hasAnyPageOperations = false;
                foreach ($this->pageWisePermissions as $permission) {
                    if (strpos($permission, $pageName . ':') === 0) {
                        $hasAnyPageOperations = true;
                        break;
                    }
                }
                
                // Only auto-select all operations if none are currently selected
                if (!$hasAnyPageOperations && isset($this->pageWisePermissionData[$pageName])) {
                    foreach ($this->pageWisePermissionData[$pageName] as $permissionItem) {
                        $operations = $permissionItem['operations_array'] ?? [];
                        foreach ($operations as $op) {
                            $permissionString = $pageName . ':' . $op;
                            if (!in_array($permissionString, $this->pageWisePermissions)) {
                                $this->pageWisePermissions[] = $permissionString;
                            }
                        }
                    }
                }
            } else {
                // If sidebar is deselected, remove all page operations for this page
                $this->pageWisePermissions = array_filter($this->pageWisePermissions, function($permission) use ($pageName) {
                    return strpos($permission, $pageName . ':') !== 0;
                });
            }
        }

        // Re-index the array
        $this->pageWisePermissions = array_values($this->pageWisePermissions);
    }

    \Log::info('Updated page permissions:', $this->pageWisePermissions);
}



    // Method to handle page-wise permission updates
public function updatedPageWisePermissions($value, $name)
{
    foreach ($this->pageWisePermissionData as $pageName => $permissions) {
        $operationsForSidebar = collect($permissions)
            ->pluck('operations_array')
            ->flatten()
            ->map(fn($op) => $pageName . ':' . $op)
            ->toArray();

        $hasSelectedOperations = count(array_intersect($operationsForSidebar, $this->pageWisePermissions)) > 0;

        if ($hasSelectedOperations) {
            // Ensure sidebar is selected
            if (!in_array($pageName, $this->sidebarPermissions) && $this->isSidebarSelectable($pageName)) {
                $this->sidebarPermissions[] = $pageName;
            }
        } else {
            // No operations selected → unselect sidebar
            $this->sidebarPermissions = array_diff($this->sidebarPermissions, [$pageName]);
        }
    }
}



    // Get all available operations
    public function getAllOperations()
    {
        $allOperations = [];

        foreach ($this->pageWisePermissionData as $pageName => $permissions) {
            if ($this->hasPageOperations($pageName)) {
                foreach ($permissions as $permissionItem) {
                    $operations = $permissionItem['operations_array'] ?? [];
                    foreach ($operations as $op) {
                        if (!in_array($op, $allOperations)) {
                            $allOperations[] = $op;
                        }
                    }
                }
            }
        }

        return $allOperations;
    }

    // Check if all selectable permissions are selected
    public function areAllPermissionsSelected()
    {
        $totalSelectableSidebarItems = 0;
        $totalPageOperations = 0;

        // Count total selectable sidebar items (only those with operations)
        foreach ($this->sidebarData as $parentItems) {
            foreach ($parentItems as $item) {
                if ($this->isSidebarSelectable($item['element_name'])) {
                    $totalSelectableSidebarItems++;
                }
            }
        }

        // Count total page operations for pages with operations
        foreach ($this->pageWisePermissionData as $pageName => $permissions) {
            if ($this->hasPageOperations($pageName)) {
                foreach ($permissions as $permissionItem) {
                    $operations = $permissionItem['operations_array'] ?? [];
                    $totalPageOperations += count($operations);
                }
            }
        }

        return count($this->sidebarPermissions) === $totalSelectableSidebarItems &&
            count($this->pageWisePermissions) === $totalPageOperations;
    }

    // Helper method to check if a permission is selected
    public function isPermissionSelected($permission)
    {
        return in_array($permission, $this->pageWisePermissions);
    }

    // Helper method to check if sidebar item is selected
    public function isSidebarSelected($sidebarItem)
    {
        return in_array($sidebarItem, $this->sidebarPermissions);
    }

    // Method to clear all permissions
    public function clearAllPermissions()
    {
        $this->sidebarPermissions = [];
        $this->pageWisePermissions = [];
        session()->flash('info', 'All permissions cleared.');
    }

    // Method to select all available permissions
    public function selectAllPermissions()
    {
        // Select all selectable sidebar items (only those with operations)
        $this->sidebarPermissions = [];
        foreach ($this->sidebarData as $parentItems) {
            foreach ($parentItems as $item) {
                if ($this->isSidebarSelectable($item['element_name'])) {
                    $this->sidebarPermissions[] = $item['element_name'];
                }
            }
        }

        // Select all page permissions for pages with operations
        $this->pageWisePermissions = [];
        foreach ($this->pageWisePermissionData as $pageName => $permissions) {
            if ($this->hasPageOperations($pageName)) {
                foreach ($permissions as $permissionItem) {
                    $operations = $permissionItem['operations_array'] ?? [];
                    foreach ($operations as $op) {
                        $this->pageWisePermissions[] = $pageName . ':' . $op;
                    }
                }
            }
        }

        session()->flash('info', 'All available permissions selected.');
    }

    #[Layout('components.layouts.app.base')]
    public function render()
    {
        $this->authorize('create', RoleAndPermission::class);
        return view('livewire.role-permission.create-role-permission-component');
    }
}