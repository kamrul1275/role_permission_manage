<?php

namespace App\Livewire\UserPermission;

use App\Models\PageAndPermission;
use App\Models\Sidebar;
use App\Models\User;
use App\Models\UserPermission;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CreateUserPermissionComponent extends Component
{
    public $users;
    public $user_id;
    public $selectedUser = null;
    
    public $sidebar_permissions = [];
    public $selectedPagePermissions = [];
    // Use consistent property names - remove the duplicates
    public $sidebarPermissions = [];
    public $pageWisePermissions = [];

    public $sidebarData = [];
    public $pageWisePermissionData = [];

    public function mount()
    {

                // Load users
        $this->users = User::all();

        // dd($this->users);
        // Get page-wise permissions first
        $this->pageWisePermissionData = PageAndPermission::all()
            ->groupBy('page_name')
            ->map(function ($group) {
                return $group->map(function ($item) {
                    $item['operations_array'] = json_decode($item['operations'], true) ?: [];
                    return $item;
                })->toArray();
            })->toArray();

        // Get all page names that have operations (non-empty operations_array)
        $pagesWithOperations = collect($this->pageWisePermissionData)
            ->filter(function ($operations) {
                // Check if any operation in this page group has non-empty operations_array
                return collect($operations)->some(function ($operation) {
                    return !empty($operation['operations_array']);
                });
            })
            ->keys()
            ->toArray();

        // Get only sidebar items where sidebar_id is NOT NULL (child items)
        // Filter to only show sidebars that have corresponding page operations
        $this->sidebarData = Sidebar::whereNotNull('sidebar_id')
            ->get()
            ->filter(function ($sidebar) use ($pagesWithOperations) {
                // Check if the sidebar element_name matches any page name with operations
                return in_array($sidebar->element_name, $pagesWithOperations);
            })
            ->groupBy('sidebar_id')
            ->map(fn($group) => $group->toArray())
            ->toArray();

        // Initialize permissions arrays
        $this->sidebarPermissions = [];
        $this->pageWisePermissions = [];
    }


    public function updatedUserId()
    {
        $this->selectedUser = User::find($this->user_id);
        
        // Reset permissions when user changes
        $this->sidebar_permissions = [];
        $this->selectedPagePermissions = [];
    }



    public function isSidebarSelected($elementName)
    {
        return in_array($elementName, $this->sidebarPermissions);
    }

    public function isPagePermissionSelected($permissionValue)
    {
        return in_array($permissionValue, $this->pageWisePermissions);
    }

    // Add method to toggle sidebar permission with auto-selection
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

    // Add method to toggle individual page permission
    public function togglePagePermission($permissionValue)
    {
        if ($this->isPagePermissionSelected($permissionValue)) {
            // Remove from page permissions
            $this->pageWisePermissions = array_values(array_filter($this->pageWisePermissions, function($item) use ($permissionValue) {
                return $item !== $permissionValue;
            }));
        } else {
            // Add to page permissions
            $this->pageWisePermissions[] = $permissionValue;
        }
        
        // Remove duplicates
        $this->pageWisePermissions = array_unique($this->pageWisePermissions);
        
        // Check if we should update sidebar permission based on page permissions
        $parts = explode(':', $permissionValue);
        if (count($parts) == 2) {
            $pageName = $parts[0];
            
            // Check if any page permissions exist for this page
            $hasPagePermissions = collect($this->pageWisePermissions)->some(function($permission) use ($pageName) {
                return str_starts_with($permission, $pageName . ':');
            });
            
            // Update sidebar permission accordingly
            if ($hasPagePermissions && !$this->isSidebarSelected($pageName)) {
                $this->sidebarPermissions[] = $pageName;
                $this->sidebarPermissions = array_unique($this->sidebarPermissions);
            } elseif (!$hasPagePermissions && $this->isSidebarSelected($pageName)) {
                $this->sidebarPermissions = array_values(array_filter($this->sidebarPermissions, function($item) use ($pageName) {
                    return $item !== $pageName;
                }));
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

    // Auto-select page operations when sidebar is selected
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

    // Get counts for display
    public function getSidebarPermissionsCountProperty()
    {
        return count($this->sidebarPermissions);
    }

    public function getPageWisePermissionsCountProperty()
    {
        return count($this->pageWisePermissions);
    }


    public function storeUserPermission()
{
    $this->validate([
        'user_id' => 'required|exists:users,id',
        'sidebarPermissions' => 'nullable|array',
        'pageWisePermissions' => 'nullable|array',
    ]);


    // dd('ffdfdsf');
    $permissions = UserPermission::firstOrNew(['user_id' => $this->user_id]);

    $permissions->sidebar_permissions = json_encode($this->sidebarPermissions ?? []);
    $permissions->page_wise_permissions = json_encode($this->pageWisePermissions ?? []);
    $permissions->save();

    session()->flash('success', $permissions->wasRecentlyCreated 
        ? 'User permissions assigned successfully.' 
        : 'User permissions updated successfully.');

    return $this->redirect(route('user_permission'), navigate: true);
}

    // public function storeUserPermission()
    // {
    //     $this->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'sidebarPermissions' => 'nullable|array',
    //         'pageWisePermissions' => 'nullable|array',
    //     ]);

    //     // Check if user already has permissions assigned
    //     $existingPermission = UserPermission::where('user_id', $this->user_id)->first();
        
    //     if ($existingPermission) {
    //         // Update existing permissions
    //         $existingPermission->sidebar_permissions = json_encode($this->sidebarPermissions ?? []);
    //         $existingPermission->page_wise_permissions = json_encode($this->pageWisePermissions ?? []);
    //         $existingPermission->save();
            
    //         session()->flash('success', 'User permissions updated successfully.');
    //     } else {
    //         // Create new permission record
    //         $data = new UserPermission();
    //         $data->user_id = $this->user_id;
    //         $data->sidebar_permissions = json_encode($this->sidebarPermissions ?? []);
    //         $data->page_wise_permissions = json_encode($this->pageWisePermissions ?? []);

    //         dd($data);
    //         $data->save();

    //         session()->flash('success', 'User permissions assigned successfully.');
    //     }

    //     return $this->redirect(route('user_permission'), navigate: true);
    // }

    #[Layout('components.layouts.app.base')]
    public function render()
    {
        $userPermission = UserPermission::first();
        $this->authorize('create', $userPermission ?? UserPermission::class);
        return view('livewire.user-permission.create-user-permission-component');
    }
}