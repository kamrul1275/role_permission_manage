<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Models\Sidebar;
use Illuminate\Support\Facades\Auth;

class SidebarHelperController extends Controller
{
    public function get_sidebar()
    {
        $user = Auth::user();
        
        if (!$user) {
            return collect(); // Return empty collection if no user
        }

        // Get user's sidebar permissions from both direct permissions and role permissions
        $userSidebarPermissions = $this->getUserSidebarPermissions($user);
        
        if (empty($userSidebarPermissions)) {
            return collect(); // Return empty collection if no permissions
        }

        // Determine if permissions are IDs (numeric) or names (strings)
        $hasNumericPermissions = collect($userSidebarPermissions)->contains(function ($permission) {
            return is_numeric($permission);
        });

        $hasStringPermissions = collect($userSidebarPermissions)->contains(function ($permission) {
            return !is_numeric($permission);
        });

        return Sidebar::whereNull('sidebar_id')
            ->orderBy('position', 'asc')
            ->with(['children' => function ($q) use ($userSidebarPermissions, $hasNumericPermissions, $hasStringPermissions) {
                $q->orderBy('position', 'asc')
                  ->where(function ($query) use ($userSidebarPermissions, $hasNumericPermissions, $hasStringPermissions) {
                      if ($hasNumericPermissions && $hasStringPermissions) {
                          // Mixed permissions - check both
                          $numericPerms = array_filter($userSidebarPermissions, 'is_numeric');
                          $stringPerms = array_filter($userSidebarPermissions, function($p) { return !is_numeric($p); });
                          
                          $query->whereIn('id', $numericPerms)
                                ->orWhereIn('element_name', $stringPerms);
                      } elseif ($hasNumericPermissions) {
                          // Only numeric permissions - check IDs
                          $query->whereIn('id', $userSidebarPermissions);
                      } else {
                          // Only string permissions - check names
                          $query->whereIn('element_name', $userSidebarPermissions);
                      }
                  });
            }])
            ->where(function ($query) use ($userSidebarPermissions, $hasNumericPermissions, $hasStringPermissions) {
                if ($hasNumericPermissions && $hasStringPermissions) {
                    // Mixed permissions - check both
                    $numericPerms = array_filter($userSidebarPermissions, 'is_numeric');
                    $stringPerms = array_filter($userSidebarPermissions, function($p) { return !is_numeric($p); });
                    
                    $query->whereIn('id', $numericPerms)
                          ->orWhereIn('element_name', $stringPerms)
                          // Also include parent menus that have permitted children
                          ->orWhereHas('children', function ($childQuery) use ($numericPerms, $stringPerms) {
                              $childQuery->whereIn('id', $numericPerms)
                                        ->orWhereIn('element_name', $stringPerms);
                          });
                } elseif ($hasNumericPermissions) {
                    // Only numeric permissions - check IDs
                    $query->whereIn('id', $userSidebarPermissions)
                          ->orWhereHas('children', function ($childQuery) use ($userSidebarPermissions) {
                              $childQuery->whereIn('id', $userSidebarPermissions);
                          });
                } else {
                    // Only string permissions - check names
                    $query->whereIn('element_name', $userSidebarPermissions)
                          ->orWhereHas('children', function ($childQuery) use ($userSidebarPermissions) {
                              $childQuery->whereIn('element_name', $userSidebarPermissions);
                          });
                }
            })
            ->get()
            ->filter(function ($item) {
                // Remove parent items that have no visible children
                if ($item->children->isNotEmpty()) {
                    return true; // Keep if it has children
                }
                return $item->children->isEmpty(); // Keep if it's a regular menu item
            });
    }

    /**
     * Get user's sidebar permissions from both user permissions and role permissions
     */
    private function getUserSidebarPermissions($user)
    {
        $permissions = [];

        // Get direct user permissions
        $userPermissions = $user->userPermissions()
            ->whereNotNull('sidebar_permissions')
            ->get()
            ->flatMap(function ($permission) {
                return $permission->sidebar_permissions ?? [];
            })
            ->toArray();

        // Get role-based permissions
        $rolePermissions = [];
        if ($user->rolePermissions && $user->rolePermissions->sidebar_permissions) {
            $rolePermissions = $user->rolePermissions->sidebar_permissions;
        }

        // Ensure both are arrays
        $userPermissions = is_array($userPermissions) ? $userPermissions : [];
        $rolePermissions = is_array($rolePermissions) ? $rolePermissions : [];

        // Merge both permission arrays
        $permissions = array_merge($userPermissions, $rolePermissions);
        
        // Remove duplicates and return
        return array_unique($permissions);
    }

    /**
     * Alternative method if you want to check permissions by sidebar element names
     */
    public function get_sidebar_by_names()
    {
        $user = Auth::user();
        
        if (!$user) {
            return collect();
        }

        $userSidebarPermissions = $this->getUserSidebarPermissions($user);
        
        return Sidebar::whereNull('sidebar_id')
            ->orderBy('position', 'asc')
            ->with(['children' => function ($q) use ($userSidebarPermissions) {
                $q->orderBy('position', 'asc')
                  ->whereIn('element_name', $userSidebarPermissions);
            }])
            ->where(function ($query) use ($userSidebarPermissions) {
                $query->whereIn('element_name', $userSidebarPermissions)
                      ->orWhereHas('children', function ($childQuery) use ($userSidebarPermissions) {
                          $childQuery->whereIn('element_name', $userSidebarPermissions);
                      });
            })
            ->get()
            ->filter(function ($item) {
                return $item->children->isEmpty() || $item->children->isNotEmpty();
            });
    }
}