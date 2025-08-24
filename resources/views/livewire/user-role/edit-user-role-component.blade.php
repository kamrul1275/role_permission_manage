<div>
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



                                @if ($selectedUser)
                                <div class="alert alert-info mb-4">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong>Editing User:</strong> {{ $selectedUser->name }}<br>
                                            <strong>Email:</strong> {{ $selectedUser->email }}<br>
                                            <strong>Assigned Roles:</strong>
                                            <span id="role-display">
                                                @if(!empty($selectedRoleNames))
                                                <span class="text-success fw-bold">{{ implode(', ', $selectedRoleNames)
                                                    }}</span>
                                                @else
                                                <span class="text-muted">No Roles Selected</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- User Info Display --}}
                                {{-- @if ($selectedUser)
                                <div class="alert alert-info mb-4" wire:key="user-info-{{ $selectedUser->id }}">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong>Editing User:</strong> {{ $selectedUser->name }}<br>
                                            <strong>Email:</strong> {{ $selectedUser->email }}<br>
                                            <strong>Assigned Roles:</strong>
                                            <span id="current-roles-display">
                                                @if(!empty($selectedRoleNames))
                                                <span class="text-success fw-bold">{{ implode(', ', $selectedRoleNames)
                                                    }}</span>
                                                @else
                                                <span class="text-muted">No Roles Selected</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif --}}

                                {{-- ===================================
                                SECTION 2: ROLE SELECTION WITH SELECT2
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

                                    {{-- Role Selection Dropdown --}}
                                    <div wire:ignore>
                                        <select class="form-control js-role-select" name="selectedRoles[]"
                                            multiple="multiple" id="role-select" data-placeholder="Select roles">
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @if(in_array($role->id, $selectedRoles ??
                                                [])) selected @endif>
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
                                </div>
                            </div>

                            {{-- ===================================
                            SECTION 3: PERMISSIONS MANAGEMENT
                            =================================== --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="text-start">
                                        <small class="text-muted">
                                            Sidebar: <span id="sidebar-count">{{ count($sidebarPermissions ?? [])
                                                }}</span> |
                                            Operations: <span id="operations-count">{{ count($pageWisePermissions ?? [])
                                                }}</span>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-outline-secondary btn-sm me-2"
                                            wire:click="clearAllPermissions">
                                            <i class="fas fa-eraser me-1"></i> Clear Additional
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm"
                                            wire:click="selectAllPermissions">
                                            <i class="fas fa-check-double me-1"></i> Select All
                                        </button>
                                    </div>
                                </div>

                                {{-- Permissions Table --}}
                                <div class="table-responsive border rounded"
                                    style="max-height: 600px; overflow-y: auto;">
                                    <div id="permissions-loading-overlay" class="d-none position-absolute w-100 h-100"
                                        style="z-index: 1000; background: rgba(255,255,255,0.9);">
                                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                                            <div class="spinner-border text-primary mb-2" role="status">
                                                <span class="visually-hidden">Loading permissions...</span>
                                            </div>
                                            <div class="small text-muted">Updating permissions table...</div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-sm align-middle mb-0"
                                        id="permissions-table">
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
                                            $elementId = $child['id'] ?? 'unknown';
                                            @endphp

                                            <tr>
                                                <td>
                                                    <i class="fas fa-file-alt me-2 text-secondary"></i>
                                                    <strong>{{ $elementName }}</strong>
                                                </td>

                                                {{-- SIDEBAR PERMISSIONS --}}
                                                <td class="text-center">
                                                    @php
                                                    $isRoleBasedSidebar = in_array($elementName, $roleSidebarPermissions
                                                    ?? []);
                                                    $isSidebarSelected = $this->isSidebarSelected($elementName);
                                                    @endphp

                                                    <div class="form-check d-inline-block">
                                                        <input type="checkbox"
                                                            class="form-check-input {{ $isRoleBasedSidebar ? 'role-based-highlight' : '' }}"
                                                            wire:click="toggleSidebarPermission('{{ $elementName }}')"
                                                            @checked($isSidebarSelected) @disabled($isRoleBasedSidebar)
                                                            id="sidebar_{{ $elementId }}">
                                                    </div>

                                                    @if($isRoleBasedSidebar)
                                                    <small class="text-primary d-block mt-1">
                                                        <i class="fas fa-lock me-1"
                                                            style="font-size: 10px;"></i>Role-based
                                                    </small>
                                                    @endif
                                                </td>

                                                {{-- PAGE OPERATIONS --}}
                                                <td>
                                                    @php
                                                    $operations = $pageWisePermissionData[$elementName] ?? [];
                                                    @endphp

                                                    @if (!empty($operations))
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach ($operations as $permGroup)
                                                        @php $ops = $permGroup['operations_array'] ?? []; @endphp
                                                        @foreach ($ops as $operation)
                                                        @php
                                                        $permissionString = $elementName . ':' . $operation;
                                                        $isRoleBasedOperation = in_array($permissionString,
                                                        $rolePageWisePermissions ?? []);
                                                        $isOperationSelected =
                                                        $this->isPagePermissionSelected($permissionString);
                                                        $operationId = "perm_" . md5($elementName . $operation);
                                                        @endphp

                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                class="form-check-input custom-checkbox-small {{ $isRoleBasedOperation ? 'role-based-highlight' : '' }}"
                                                                wire:click="togglePagePermission('{{ $permissionString }}')"
                                                                @checked($isOperationSelected)
                                                                @disabled($isRoleBasedOperation)
                                                                id="{{ $operationId }}">
                                                            <label
                                                                class="form-check-label small {{ $isRoleBasedOperation ? 'text-primary fw-semibold' : '' }}"
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
                                                    <i class="fas fa-exclamation-triangle me-2"></i>No sidebar elements
                                                    found
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


    {{-- Enhanced JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    initRoleSelect();
});

document.addEventListener('livewire:navigated', function() {
    initRoleSelect();
});

function initRoleSelect() {
    const $select = $('#role-select');
    
    if (!$select.length) return;

    // Destroy existing Select2
    if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
    }

    // Initialize Select2
    $select.select2({
        placeholder: "Select roles",
        allowClear: true,
        width: '100%',
        closeOnSelect: false
    });

    // Handle change event
$select.off('change.roleSelect').on('change.roleSelect', function(e) {
    const selectedValues = $(this).val() || [];

    // Show loading
    $('#role-loading').removeClass('d-none');

    // ✅ শুধু property set করো
    @this.set('selectedRoles', selectedValues)
        .then(() => {
            updateRoleDisplay(selectedValues);
        })
        .catch(error => {
            console.error('Error updating roles:', error);
        })
        .finally(() => {
            $('#role-loading').addClass('d-none');
        });
});
    // Initial display update
    const initialValues = $select.val() || [];
    updateRoleDisplay(initialValues);
}


function updateRoleDisplay(selectedValues) {
    const roleNames = selectedValues.map(roleId => {
        return $('#role-select option[value="' + roleId + '"]').text();
    });

    const $display = $('#role-display');
    if (roleNames.length > 0) {
        $display.html('<span class="text-success fw-bold">' + roleNames.join(', ') + '</span>');
    } else {
        $display.html('<span class="text-muted">No Roles Selected</span>');
    }
}

window.addEventListener('roles-updated', function(e) {
    const newRoles = e.detail || [];
    updateRoleDisplay(newRoles);
});


</script>

</div>