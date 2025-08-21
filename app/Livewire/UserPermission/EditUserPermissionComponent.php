<?php

namespace App\Livewire\UserPermission;

use App\Models\PageAndPermission;
use App\Models\Sidebar;
use App\Models\User;
use App\Models\UserPermission;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EditUserPermissionComponent extends Component
{
    public $permissionId;
    public $users;
    public $user_id;
    public $selectedUser = null;
    
    public $sidebarPermissions = [];
    public $pageWisePermissions = [];

    public $sidebarData = [];
    public $pageWisePermissionData = [];
    public $id;


// In your EditUserPermissionComponent's mount() method
public function mount($id)
{
    $this->permissionId = $id;
    
    // Load users
    $this->users = User::all();

    // Load existing permission data
    $permission = UserPermission::findOrFail($this->permissionId);
    $this->user_id = $permission->user_id;
    $this->selectedUser = User::find($this->user_id);
    
    // Initialize permissions from the existing record
    // The model's accessors will handle the conversion automatically
    $this->sidebarPermissions = $permission->sidebar_permissions;
    $this->pageWisePermissions = $permission->page_wise_permissions;

    // Rest of your mount method remains the same...
    $this->pageWisePermissionData = PageAndPermission::all()
        ->groupBy('page_name')
        ->map(function ($group) {
            return $group->map(function ($item) {
                $item['operations_array'] = json_decode($item['operations'], true) ?: [];
                return $item;
            })->toArray();
        })->toArray();

    // Get all page names that have operations
    $pagesWithOperations = collect($this->pageWisePermissionData)
        ->filter(function ($operations) {
            return collect($operations)->some(function ($operation) {
                return !empty($operation['operations_array']);
            });
        })
        ->keys()
        ->toArray();

    // Get only sidebar items where sidebar_id is NOT NULL (child items)
    $this->sidebarData = Sidebar::whereNotNull('sidebar_id')
        ->get()
        ->filter(function ($sidebar) use ($pagesWithOperations) {
            return in_array($sidebar->element_name, $pagesWithOperations);
        })
        ->groupBy('sidebar_id')
        ->map(fn($group) => $group->toArray())
        ->toArray();
}

//     public function mount($id)
//     {
//         $this->permissionId = $id;
        
//         // Load users
//         $this->users = User::all();

//         // Load existing permission data
//         $permission = UserPermission::findOrFail($this->permissionId);
//         $this->user_id = $permission->user_id;
//         $this->selectedUser = User::find($this->user_id);
        
//  $this->sidebarPermissions = $permission->sidebar_permissions ?: [];
// $this->pageWisePermissions = $permission->page_wise_permissions ?: [];
//         // Get page-wise permissions first
//         $this->pageWisePermissionData = PageAndPermission::all()
//             ->groupBy('page_name')
//             ->map(function ($group) {
//                 return $group->map(function ($item) {
//                     $item['operations_array'] = json_decode($item['operations'], true) ?: [];
//                     return $item;
//                 })->toArray();
//             })->toArray();

//         // Get all page names that have operations (non-empty operations_array)
//         $pagesWithOperations = collect($this->pageWisePermissionData)
//             ->filter(function ($operations) {
//                 return collect($operations)->some(function ($operation) {
//                     return !empty($operation['operations_array']);
//                 });
//             })
//             ->keys()
//             ->toArray();

//         // Get only sidebar items where sidebar_id is NOT NULL (child items)
//         $this->sidebarData = Sidebar::whereNotNull('sidebar_id')
//             ->get()
//             ->filter(function ($sidebar) use ($pagesWithOperations) {
//                 return in_array($sidebar->element_name, $pagesWithOperations);
//             })
//             ->groupBy('sidebar_id')
//             ->map(fn($group) => $group->toArray())
//             ->toArray();
//     }

    public function updatedUserId()
    {
        $this->selectedUser = User::find($this->user_id);
        
        // Reset permissions when user changes
        $this->sidebarPermissions = [];
        $this->pageWisePermissions = [];
    }

    public function isSidebarSelected($elementName)
    {
        return in_array($elementName, $this->sidebarPermissions);
    }

    public function isPagePermissionSelected($permissionValue)
    {
        return in_array($permissionValue, $this->pageWisePermissions);
    }

    public function toggleSidebarPermission($elementName)
    {
        if ($this->isSidebarSelected($elementName)) {
            // Remove from sidebar permissions
            $this->sidebarPermissions = array_values(array_filter($this->sidebarPermissions, function($item) use ($elementName) {
                return $item !== $elementName;
            }));
            
            // Remove all related page permissions
            $this->pageWisePermissions = array_values(array_filter($this->pageWisePermissions, function($permission) use ($elementName) {
                return !str_starts_with($permission, $elementName . ':');
            }));
        } else {
            // Add to sidebar permissions
            $this->sidebarPermissions[] = $elementName;
            
            // Auto-add ALL related page permissions
            if (!empty($this->pageWisePermissionData[$elementName])) {
                foreach ($this->pageWisePermissionData[$elementName] as $permGroup) {
                    foreach ($permGroup['operations_array'] ?? [] as $operation) {
                        $permissionValue = $elementName . ':' . $operation;
                        if (!in_array($permissionValue, $this->pageWisePermissions)) {
                            $this->pageWisePermissions[] = $permissionValue;
                        }
                    }
                }
            }
        }
        
        // Remove duplicates
        $this->sidebarPermissions = array_unique($this->sidebarPermissions);
        $this->pageWisePermissions = array_unique($this->pageWisePermissions);
    }

public function togglePagePermission($permissionValue)
{
    if ($this->isPagePermissionSelected($permissionValue)) {
        // Remove from page permissions
        $this->pageWisePermissions = array_values(array_filter(
            $this->pageWisePermissions, 
            fn($item) => $item !== $permissionValue
        ));
    } else {
        // Add to page permissions
        $this->pageWisePermissions[] = $permissionValue;
        $this->pageWisePermissions = array_unique($this->pageWisePermissions);
    }

    // Extract the page name from permission (format: "PageName:operation")
    $parts = explode(':', $permissionValue);
    if (count($parts) === 2) {
        $pageName = $parts[0];
        
        // Check if any permissions exist for this page
        $hasPermissionsForPage = collect($this->pageWisePermissions)
            ->contains(fn($perm) => str_starts_with($perm, $pageName.':'));
        
        // Update sidebar permission
        if ($hasPermissionsForPage && !in_array($pageName, $this->sidebarPermissions)) {
            $this->sidebarPermissions[] = $pageName;
            $this->sidebarPermissions = array_unique($this->sidebarPermissions);
        } elseif (!$hasPermissionsForPage && in_array($pageName, $this->sidebarPermissions)) {
            $this->sidebarPermissions = array_values(array_filter(
                $this->sidebarPermissions,
                fn($item) => $item !== $pageName
            ));
        }
    }
}
    public function clearAllPermissions()
    {
        $this->sidebarPermissions = [];
        $this->pageWisePermissions = [];
    }

    public function selectAllPermissions()
    {
        // Select all sidebar permissions
        $this->sidebarPermissions = [];

        foreach ($this->sidebarData as $parentId => $childItems) {
            if ($parentId !== null && is_array($childItems)) {
                foreach ($childItems as $child) {
                    $elementName = $child['element_name'] ?? '';
                    if ($elementName) {
                        $this->sidebarPermissions[] = $elementName;
                    }
                }
            }
        }

        // This will automatically trigger the auto-selection of page operations
        $this->autoSelectPageOperations();
        
        // Remove duplicates
        $this->sidebarPermissions = array_unique($this->sidebarPermissions);
        $this->pageWisePermissions = array_unique($this->pageWisePermissions);
    }

    public function autoSelectPageOperations()
    {
        $newPagePermissions = [];
        
        // Add all operations for selected sidebar items
        foreach ($this->sidebarPermissions as $sidebarElement) {
            if (!empty($this->pageWisePermissionData[$sidebarElement])) {
                foreach ($this->pageWisePermissionData[$sidebarElement] as $permGroup) {
                    foreach ($permGroup['operations_array'] ?? [] as $operation) {
                        $permissionValue = $sidebarElement . ':' . $operation;
                        if (!in_array($permissionValue, $newPagePermissions)) {
                            $newPagePermissions[] = $permissionValue;
                        }
                    }
                }
            }
        }
        
        $this->pageWisePermissions = $newPagePermissions;
    }

    public function getSidebarPermissionsCountProperty()
    {
        return count($this->sidebarPermissions);
    }

    public function getPageWisePermissionsCountProperty()
    {
        return count($this->pageWisePermissions);
    }

public function updateUserPermission()
{
    $this->validate([
        'user_id' => 'required|exists:users,id',
        'sidebarPermissions' => 'nullable|array',
        'pageWisePermissions' => 'nullable|array',
    ]);

    $permission = UserPermission::findOrFail($this->permissionId);
    $permission->user_id = $this->user_id;
    
    // The model will handle the conversion to JSON automatically
    $permission->sidebar_permissions = $this->sidebarPermissions;
    $permission->page_wise_permissions = $this->pageWisePermissions;
    
    $permission->save();

    session()->flash('success', 'User permissions updated successfully.');

    return $this->redirect(route('user_permission'), navigate: true);
}
    #[Layout('components.layouts.app.base')]
    public function render()
    {
        $this->authorize('update', UserPermission::findOrFail($this->permissionId));
        return view('livewire.user-permission.edit-user-permission-component');
    }
}