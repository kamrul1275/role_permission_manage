<div>
  {{-- Update the permissions section with proper wire:key --}}





<div class="content container-fluid">
 
  {{-- ===================================
  PAGE HEADER
  =================================== --}}
  <div class="page-header">
    <div class="row">
      <div class="col">
        <h3 class="page-title">Edit User Role & Permissions</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('user_list') }}">User Roles</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ul>
      </div>
    </div>
  </div>
 
  <div class="row">
    <div class="col-md-12">
      <div class="card">
 
        <div class="card-body">
          <form wire:submit.prevent="savePermissions">
 
            {{-- ===================================
            SECTION 1: USER & ROLE SELECTION
            =================================== --}}
            <div class="row mb-4">
 
              {{-- User Selection (Pre-selected, Disabled) --}}
              @if ($selectedUser)
              <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                  <div>
                    <strong>Editing User:</strong> {{ $selectedUser->name }}<br>
                    <strong>Email:</strong> {{ $selectedUser->email }}<br>
                    @if(!empty($selectedRoles))
                    <strong>Assigned Roles:</strong>
                    @php
                    $roleNames = collect($roles)->whereIn('id', $selectedRoles)->pluck('role_name')->toArray();
                    @endphp
                    {{ implode(', ', $roleNames) }}
                    @else
                    <strong>Assigned Roles:</strong> No Roles Selected
                    @endif
                  </div>
                </div>
              </div>
              @endif
 
              {{-- ===================================
              SECTION 2: ROLE SELECTION WITH SELECT2 - FIXED
              =================================== --}}
              <div class="col-md-12 mb-4">
                  <label class="form-label fw-semibold">Select Roles</label>
                 
                  {{-- Loading Indicator --}}
                  <div id="role-loading" class="d-none mb-2">
                      <div class="d-flex align-items-center text-muted">
                          <div class="spinner-border spinner-border-sm me-2" role="status">
                              <span class="visually-hidden">Loading...</span>
                          </div>
                          <small>Updating permissions...</small>
                      </div>
                  </div>
                 
                  {{-- CRITICAL FIX: Use wire:ignore.self to prevent Livewire from replacing this element --}}
                  <div wire:ignore.self>
                      <select class="form-control js-role-select"
                              name="selectedRoles[]"
                              multiple="multiple"
                              id="role-select">
                          @foreach($roles as $role)
                              <option value="{{ $role->id }}"
                                  @if(in_array($role->id, $selectedRoles ?? [])) selected @endif
                                  data-role-name="{{ $role->role_name }}">
                                  {{ $role->role_name }}
                              </option>
                          @endforeach
                      </select>
                  </div>
               
                  @error('selectedRoles')
                      <div class="invalid-feedback d-block mt-2">
                        {{ $message }}
                      </div>
                  @enderror
                  
                  {{-- Debug info (remove in production) --}}
                  @if(config('app.debug'))
                      <small class="text-muted mt-1 d-block">
                          Selected Role IDs: {{ implode(', ', $selectedRoles ?? []) }}
                      </small>
                  @endif
              </div>
            </div>
 
            {{-- ===================================
            SECTION 3: PERMISSIONS MANAGEMENT - ENHANCED
            =================================== --}}
            <div class="mb-4" 
                 wire:key="permissions-table-{{ implode('-', $selectedRoles ?? []) }}-{{ count($sidebarPermissions ?? []) }}-{{ count($pageWisePermissions ?? []) }}">
                
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="text-start">
                        {{-- Add refresh button for debugging --}}
                        <button type="button" class="btn btn-outline-info btn-sm" 
                                wire:click="refreshPermissions">
                            <i class="fas fa-sync me-1"></i> Refresh Permissions
                        </button>
                        
                        {{-- Permission counts --}}
                        <small class="text-muted ms-3">
                            Sidebar: {{ count(array_unique(array_merge($sidebarPermissions ?? [], $roleSidebarPermissions ?? []))) }} |
                            Operations: {{ count(array_unique(array_merge($pageWisePermissions ?? [], $rolePageWisePermissions ?? []))) }}
                        </small>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2"
                            wire:click="clearAllPermissions">
                            <i class="fas fa-eraser me-1"></i> Clear Additional
                        </button>
                        <button type="button" class="btn btn-success btn-sm" wire:click="selectAllPermissions">
                            <i class="fas fa-check-double me-1"></i> Select All
                        </button>
                    </div>
                </div>

                {{-- Permissions Table with enhanced loading state --}}
                <div class="table-responsive border rounded" style="max-height: 600px; overflow-y: auto;">
                    
                    {{-- Loading overlay --}}
                    <div wire:loading.block wire:target="refreshPermissions,updatedSelectedRoles" 
                         class="position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle bg-white p-3 rounded shadow" 
                             style="z-index: 1000;">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-2" role="status">
                                    <span class="visually-hidden">Loading permissions...</span>
                                </div>
                                <div class="small text-muted">Updating permissions table...</div>
                            </div>
                        </div>
                    </div>
                    
                    <table class="table table-bordered table-sm align-middle mb-0" 
                           wire:loading.class="opacity-50"
                           wire:target="refreshPermissions,updatedSelectedRoles">
                        <thead class="table-light sticky-top custom-table-header">
                            <tr>
                                <th style="width: 40%;">Page/Module Name</th>
                                <th class="text-center" style="width: 20%;">Sidebar Access</th>
                                <th class="text-center" style="width: 40%;">Page Operations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($sidebarData) && is_array($sidebarData))
                                @foreach ($sidebarData as $parentId => $childItems)
                                    @if ($parentId !== null && is_array($childItems))
                                        @foreach ($childItems as $child)
                                            @php 
                                                $elementName = $child['element_name'] ?? ''; 
                                                $rowKey = 'permission-row-' . ($child['id'] ?? 'unknown') . '-' . md5(json_encode($selectedRoles ?? []));
                                            @endphp
                                            <tr wire:key="{{ $rowKey }}">
                                                <td>
                                                    <i class="fas fa-file-alt me-2 text-secondary"></i>
                                                    <strong>{{ $elementName }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $isRoleBasedSidebar = in_array($elementName, $roleSidebarPermissions ?? []);
                                                        $isSidebarSelected = $this->isSidebarSelected($elementName);
                                                        $sidebarId = 'sidebar_' . ($child['id'] ?? 'unknown') . '_' . md5(json_encode($selectedRoles ?? []));
                                                    @endphp
                                                    <div class="form-check d-inline-block">
                                                        <input type="checkbox"
                                                            class="form-check-input {{ $isRoleBasedSidebar ? 'border-primary bg-primary bg-opacity-25' : '' }}"
                                                            wire:click="toggleSidebarPermission('{{ $elementName }}')"
                                                            @checked($isSidebarSelected)
                                                            @disabled($isRoleBasedSidebar)
                                                            id="{{ $sidebarId }}">
                                                    </div>
                                                    @if($isRoleBasedSidebar)
                                                        <small class="text-primary d-block mt-1">
                                                            <i class="fas fa-lock me-1" style="font-size: 10px;"></i>Role-based
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($pageWisePermissionData[$elementName]))
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach ($pageWisePermissionData[$elementName] as $permGroupIndex => $permGroup)
                                                                @foreach ($permGroup['operations_array'] ?? [] as $operationIndex => $operation)
                                                                    @php
                                                                        $permissionString = $elementName . ':' . $operation;
                                                                        $isRoleBasedOperation = in_array($permissionString, $rolePageWisePermissions ?? []);
                                                                        $isOperationSelected = $this->isPagePermissionSelected($permissionString);
                                                                        $operationId = 'perm_' . md5($elementName . $operation . json_encode($selectedRoles ?? []) . $permGroupIndex . $operationIndex);
                                                                    @endphp
                                                                    <div class="form-check">
                                                                        <input type="checkbox"
                                                                            class="form-check-input custom-checkbox-small {{ $isRoleBasedOperation ? 'border-primary bg-primary bg-opacity-25' : '' }}"
                                                                            wire:click="togglePagePermission('{{ $permissionString }}')"
                                                                            @checked($isOperationSelected)
                                                                            @disabled($isRoleBasedOperation)
                                                                            id="{{ $operationId }}">
                                                                        <label class="form-check-label small {{ $isRoleBasedOperation ? 'text-primary fw-semibold' : '' }}"
                                                                            for="{{ $operationId }}">
                                                                            {{ ucfirst($operation) }}
                                                                            @if($isRoleBasedOperation)
                                                                                <i class="fas fa-lock ms-1" style="font-size: 8px;"></i>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">
                                                            <i class="fas fa-info-circle me-1"></i>No operations available
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No sidebar elements found
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Error messages --}}
                @error('sidebarPermissions')
                    <div class="text-danger mt-2 small">
                        <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                    </div>
                @enderror

                @error('pageWisePermissions')
                    <div class="text-danger mt-2 small">
                        <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            {{-- ===================================
            SECTION 4: ACTION BUTTONS
            =================================== --}}
            <div class="text-end mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary btn-lg me-3" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="savePermissions">
                        <i class="fas fa-save me-2"></i> Update Role & Permissions
                    </span>
                    <span wire:loading wire:target="savePermissions">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Updating...
                    </span>
                </button>
                <a href="{{ route('user_list') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===================================
ENHANCED JAVASCRIPT WITH FIXES
=================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let isUpdating = false;
    let select2Instance = null;
    let updateTimeout = null;
    
    console.log('Role permission script loaded');

    // Initialize Select2 with better error handling
    function initializeSelect2() {
        const selectElement = $('#role-select');
        
        if (!selectElement.length) {
            console.log('Select element not found');
            return;
        }

        // Only destroy if it exists and is a Select2 instance
        if (selectElement.hasClass('select2-hidden-accessible')) {
            console.log('Destroying existing Select2 instance');
            selectElement.select2('destroy');
        }

        try {
            select2Instance = selectElement.select2({
                placeholder: "Select roles from the list",
                allowClear: false,
                width: '100%',
                closeOnSelect: false,
                templateResult: formatRole,
                templateSelection: formatRoleSelection,
                escapeMarkup: function(markup) { return markup; },
                dropdownParent: selectElement.parent()
            });

            // Bind change event with better handling
            selectElement.off('change.roleSelect').on('change.roleSelect', function(e) {
                if (isUpdating) {
                    console.log('Skipping change event - updating in progress');
                    return;
                }

                const selectedValues = $(this).val() || [];
                console.log('Select2 changed:', selectedValues);
                
                // Clear existing timeout
                if (updateTimeout) {
                    clearTimeout(updateTimeout);
                }
                
                // Debounce the update
                updateTimeout = setTimeout(() => {
                    updateLivewireRoles(selectedValues);
                }, 150);
            });

            console.log('Select2 initialized successfully');

        } catch (error) {
            console.error('Error initializing Select2:', error);
        }
    }

    // Format functions
    function formatRole(role) {
        if (!role.id) return role.text;
        
        const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FECA57', '#FF9FF3', '#54A0FF', '#5F27CD'];
        const colorIndex = Math.abs(hashCode(role.text)) % colors.length;
        const roleColor = colors[colorIndex];
        
        return '<span><span class="role-color-dot" style="background-color: ' + roleColor + '; display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 8px;"></span>' + role.text + '</span>';
    }

    function formatRoleSelection(role) {
        return role.text;
    }

    function hashCode(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return hash;
    }

    // Update Livewire state
    function updateLivewireRoles(selectedRoles) {
        console.log('Updating Livewire with roles:', selectedRoles);
        
        isUpdating = true;
        showLoading();
        
        // Use Livewire's set method with error handling
        @this.set('selectedRoles', selectedRoles)
            .then(() => {
                console.log('Livewire updated successfully');
            })
            .catch((error) => {
                console.error('Livewire update failed:', error);
                hideLoading();
            });
    }

    // Loading state management
    function showLoading() {
        $('#role-loading').removeClass('d-none');
        
        const selectElement = $('#role-select');
        if (selectElement.length) {
            selectElement.prop('disabled', true);
            $('.select2-container').addClass('select2-container--disabled');
        }
    }

    function hideLoading() {
        $('#role-loading').addClass('d-none');
        
        const selectElement = $('#role-select');
        if (selectElement.length) {
            selectElement.prop('disabled', false);
            $('.select2-container').removeClass('select2-container--disabled');
        }
        
        // Reset updating flag
        isUpdating = false;
    }

    // Livewire event listeners
    window.addEventListener('dropdown-loading', function(event) {
        console.log('Loading event received');
        showLoading();
    });

    window.addEventListener('dropdown-loaded', function(event) {
        console.log('Loaded event received');
        hideLoading();
        
        // Small delay to ensure DOM updates are complete
        setTimeout(() => {
            const selectElement = $('#role-select');
            if (selectElement.length && !selectElement.hasClass('select2-hidden-accessible')) {
                console.log('Reinitializing Select2 after load');
                initializeSelect2();
            }
        }, 100);
    });

    window.addEventListener('roles-updated', function(event) {
        console.log('Roles updated event:', event.detail);
        
        // Update Select2 selection without triggering change event
        if (select2Instance && event.detail !== undefined) {
            isUpdating = true;
            
            const selectElement = $('#role-select');
            selectElement.val(event.detail);
            
            // Trigger change but prevent our handler from running
            selectElement.trigger('change.select2');
            
            setTimeout(() => {
                isUpdating = false;
            }, 200);
        }
    });

    window.addEventListener('permissions-refreshed', function(event) {
        console.log('Permissions refreshed');
        hideLoading();
    });

    // Enhanced Livewire hooks - CRITICAL FIX
    if (typeof Livewire !== 'undefined') {
        // Prevent automatic reinitialization on every update
        Livewire.hook('morph.updated', ({ component, cleanup }) => {
            console.log('Livewire morph updated');
            
            // Only reinitialize if Select2 was destroyed
            setTimeout(() => {
                const selectElement = $('#role-select');
                if (selectElement.length && !selectElement.hasClass('select2-hidden-accessible')) {
                    console.log('Select2 lost - reinitializing');
                    initializeSelect2();
                }
            }, 50);
        });
    }

    // Initialize on page load
    initializeSelect2();

    // Cleanup on navigation
    function cleanup() {
        if (updateTimeout) {
            clearTimeout(updateTimeout);
        }
        
        const selectElement = $('#role-select');
        if (selectElement.hasClass('select2-hidden-accessible')) {
            selectElement.select2('destroy');
        }
    }

    // Navigation event handlers
    window.addEventListener('beforeunload', cleanup);
    
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:navigating', cleanup);
        document.addEventListener('livewire:navigated', function() {
            setTimeout(initializeSelect2, 200);
        });
    }
});
</script>

{{-- Feather icons initialization --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        feather.replace();
    });

    // Re-run after Livewire updates
    document.addEventListener("livewire:navigated", () => {
        feather.replace();
    });
</script>

{{-- Custom CSS for better visual feedback --}}







<script>
  


document.addEventListener('DOMContentLoaded', function() {
    let isUpdating = false;
    let select2Instance = null;
    let updateTimeout = null;
    
    console.log('Script loaded');

    // Initialize Select2 with better error handling
    function initializeSelect2() {
        const selectElement = $('#role-select');
        
        if (!selectElement.length) {
            console.log('Select element not found');
            return;
        }

        // Only destroy if it exists and is a Select2 instance
        if (selectElement.hasClass('select2-hidden-accessible')) {
            console.log('Destroying existing Select2 instance');
            selectElement.select2('destroy');
        }

        try {
            select2Instance = selectElement.select2({
                placeholder: "Select roles from the list",
                allowClear: false,
                width: '100%',
                closeOnSelect: false,
                templateResult: formatRole,
                templateSelection: formatRoleSelection,
                escapeMarkup: function(markup) { return markup; },
                dropdownParent: selectElement.parent()
            });

            // Bind change event with better handling
            selectElement.off('change.roleSelect').on('change.roleSelect', function(e) {
                if (isUpdating) {
                    console.log('Skipping change event - updating in progress');
                    return;
                }

                const selectedValues = $(this).val() || [];
                console.log('Select2 changed:', selectedValues);
                
                // Clear existing timeout
                if (updateTimeout) {
                    clearTimeout(updateTimeout);
                }
                
                // Debounce the update
                updateTimeout = setTimeout(() => {
                    updateLivewireRoles(selectedValues);
                }, 150);
            });

            console.log('Select2 initialized successfully');

        } catch (error) {
            console.error('Error initializing Select2:', error);
        }
    }

    // Format functions
    function formatRole(role) {
        if (!role.id) return role.text;
        
        const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FECA57', '#FF9FF3', '#54A0FF', '#5F27CD'];
        const colorIndex = Math.abs(hashCode(role.text)) % colors.length;
        const roleColor = colors[colorIndex];
        
        return '<span><span class="role-color-dot" style="background-color: ' + roleColor + '; display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 8px;"></span>' + role.text + '</span>';
    }

    function formatRoleSelection(role) {
        return role.text;
    }

    function hashCode(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return hash;
    }

    // Update Livewire state
    function updateLivewireRoles(selectedRoles) {
        console.log('Updating Livewire with roles:', selectedRoles);
        
        isUpdating = true;
        showLoading();
        
        // Use Livewire's set method with error handling
        @this.set('selectedRoles', selectedRoles)
            .then(() => {
                console.log('Livewire updated successfully');
            })
            .catch((error) => {
                console.error('Livewire update failed:', error);
                hideLoading();
            })
            .finally(() => {
                // Don't set isUpdating to false here - let the event handler do it
            });
    }

    // Loading state management
    function showLoading() {
        $('#role-loading').removeClass('d-none');
        
        const selectElement = $('#role-select');
        if (selectElement.length) {
            selectElement.prop('disabled', true);
            $('.select2-container').addClass('select2-container--disabled');
        }
    }

    function hideLoading() {
        $('#role-loading').addClass('d-none');
        
        const selectElement = $('#role-select');
        if (selectElement.length) {
            selectElement.prop('disabled', false);
            $('.select2-container').removeClass('select2-container--disabled');
        }
        
        // Reset updating flag
        isUpdating = false;
    }

    // Livewire event listeners
    window.addEventListener('dropdown-loading', function(event) {
        console.log('Loading event received');
        showLoading();
    });

    window.addEventListener('dropdown-loaded', function(event) {
        console.log('Loaded event received');
        hideLoading();
        
        // Small delay to ensure DOM updates are complete
        setTimeout(() => {
            const selectElement = $('#role-select');
            if (selectElement.length && !selectElement.hasClass('select2-hidden-accessible')) {
                console.log('Reinitializing Select2 after load');
                initializeSelect2();
            }
        }, 100);
    });

    window.addEventListener('roles-updated', function(event) {
        console.log('Roles updated event:', event.detail);
        
        // Update Select2 selection without triggering change event
        if (select2Instance && event.detail) {
            isUpdating = true;
            
            const selectElement = $('#role-select');
            selectElement.val(event.detail);
            
            // Trigger change but prevent our handler from running
            selectElement.trigger('change.select2');
            
            setTimeout(() => {
                isUpdating = false;
            }, 200);
        }
    });

    window.addEventListener('permissions-refreshed', function(event) {
        console.log('Permissions refreshed');
        hideLoading();
    });

    // Enhanced Livewire hooks - CRITICAL FIX
    if (typeof Livewire !== 'undefined') {
        // Prevent automatic reinitialization on every update
        Livewire.hook('morph.updated', ({ component, cleanup }) => {
            console.log('Livewire morph updated');
            
            // Only reinitialize if Select2 was destroyed
            setTimeout(() => {
                const selectElement = $('#role-select');
                if (selectElement.length && !selectElement.hasClass('select2-hidden-accessible')) {
                    console.log('Select2 lost - reinitializing');
                    initializeSelect2();
                }
            }, 50);
        });

        // Prevent reinitialization on component updates
        Livewire.hook('component.init', ({ component, cleanup }) => {
            console.log('Livewire component init');
            // Don't reinitialize here - let the main init handle it
        });
    }

    // Initialize on page load
    initializeSelect2();

    // Cleanup on navigation
    function cleanup() {
        if (updateTimeout) {
            clearTimeout(updateTimeout);
        }
        
        const selectElement = $('#role-select');
        if (selectElement.hasClass('select2-hidden-accessible')) {
            selectElement.select2('destroy');
        }
    }

    // Navigation event handlers
    window.addEventListener('beforeunload', cleanup);
    
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:navigating', cleanup);
        document.addEventListener('livewire:navigated', function() {
            setTimeout(initializeSelect2, 200);
        });
    }

    // Debug: Log when Select2 is destroyed unexpectedly
    const originalDestroy = $.fn.select2;
    $.fn.select2 = function(options) {
        if (options === 'destroy' && this.attr('id') === 'role-select') {
            console.warn('Select2 being destroyed on role-select:', new Error().stack);
        }
        return originalDestroy.apply(this, arguments);
    };
});



// Add this to your JavaScript event listeners
window.addEventListener('permissions-updated', function(event) {
    console.log('Permissions updated event received');
    // Force a re-render by triggering a Livewire update
    setTimeout(() => {
        @this.call('refreshPermissions');
    }, 100);
});

</script>
</div>