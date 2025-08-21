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

  {{-- ===================================
  FLASH MESSAGES
  =================================== --}}
  @if (session()->has('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  @if (session()->has('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title mb-0">Edit User Role & Permissions</h4>
        </div>

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
                  <i class="fas fa-user-circle me-3 fs-4"></i>
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

              <div class="col-md-12 mb-4">
                <label class="form-label fw-semibold">
                  Select Roles
                </label>

                {{-- Selected Role Tags --}}
                <div class="role-tag-container mb-3" id="roleTagsContainer">
                  @if(!empty($selectedRoles))
                    @foreach($selectedRoles as $roleId)
                      @php
                        $role = collect($roles)->firstWhere('id', $roleId);
                      @endphp
                      @if($role)
                      <div class="role-tag">
                        {{ $role->role_name }}
                        <span class="role-tag-remove" wire:click="removeRole({{ $roleId }})" wire:loading.attr="disabled">
                          <i class="fas fa-times"></i>
                        </span>
                      </div>
                      @endif
                    @endforeach
                  @else
                  <div class="text-muted" id="noRolesMessage">No roles selected</div>
                  @endif
                </div>

                {{-- Dropdown --}}
                <div class="role-dropdown" id="roleDropdownWrapper" wire:ignore.self>
                  <div class="role-dropdown-toggle" id="roleDropdownToggle">
                    <span>Select roles from the list</span>
                    <i class="fas fa-chevron-down"></i>
                    <span class="spinner-border spinner-border-sm" style="display: none;" id="dropdownSpinner"></span>
                  </div>

                  <div class="role-dropdown-menu" id="roleDropdown">
                    {{-- 🔍 Search Box --}}
                    <div class="role-dropdown-search">
                      <input type="text" id="roleSearchInput" placeholder="Search roles..." wire:loading.attr="disabled">
                    </div>

                    {{-- Role List --}}
                    <div id="roleDropdownList">
                      @foreach($roles as $role)
                        <div class="role-dropdown-item {{ in_array($role->id, $selectedRoles) ? 'active' : '' }}"
                          wire:click="toggleRole({{ $role->id }})" wire:loading.attr="disabled">
                          {{ $role->role_name }}
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>

                @error('selectedRoles')
                <div class="invalid-feedback d-block">
                  <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                </div>
                @enderror
              </div>

              {{-- ===================================
              SECTION 3: PERMISSIONS MANAGEMENT
              =================================== --}}
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <div class="text-start"></div>
                  <div class="text-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm me-2" wire:click="clearAllPermissions">
                      <i class="fas fa-eraser me-1"></i> Clear Additional
                    </button>
                    <button type="button" class="btn btn-success btn-sm" wire:click="selectAllPermissions">
                      <i class="fas fa-check-double me-1"></i> Select All
                    </button>
                  </div>
                </div>

                {{-- Permissions Table --}}
                <div class="table-responsive border rounded" style="max-height: 600px; overflow-y: auto;">
                  <table class="table table-bordered table-sm align-middle mb-0">
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
                              @php $elementName = $child['element_name'] ?? ''; @endphp
                              <tr>
                                <td><i class="fas fa-file-alt me-2 text-secondary"></i><strong>{{ $elementName }}</strong></td>
                                <td class="text-center">
                                  @php
                                    $isRoleBasedSidebar = in_array($elementName, $roleSidebarPermissions);
                                    $isSidebarSelected = $this->isSidebarSelected($elementName);
                                  @endphp
                                  <div class="form-check d-inline-block">
                                    <input type="checkbox"
                                      class="form-check-input {{ $isRoleBasedSidebar ? 'border-primary' : '' }}"
                                      wire:click="toggleSidebarPermission('{{ $elementName }}')"
                                      @checked($isSidebarSelected) @disabled($isRoleBasedSidebar)
                                      id="sidebar_{{ $child['id'] }}">
                                  </div>
                                </td>
                                <td>
                                  @if (!empty($pageWisePermissionData[$elementName]))
                                  <div class="d-flex flex-wrap gap-2">
                                    @foreach ($pageWisePermissionData[$elementName] as $permGroup)
                                      @foreach ($permGroup['operations_array'] ?? [] as $operation)
                                        @php
                                          $permissionString = $elementName . ':' . $operation;
                                          $isRoleBasedOperation = in_array($permissionString, $rolePageWisePermissions);
                                          $isOperationSelected = $this->isPagePermissionSelected($permissionString);
                                        @endphp
                                        <div class="form-check">
                                          <input type="checkbox"
                                            class="form-check-input custom-checkbox-small {{ $isRoleBasedOperation ? 'border-primary' : '' }}"
                                            wire:click="togglePagePermission('{{ $permissionString }}')"
                                            @checked($isOperationSelected) @disabled($isRoleBasedOperation)
                                            id="perm_{{ Str::slug($elementName) }}_{{ $operation }}">
                                          <label class="form-check-label small"
                                            for="perm_{{ Str::slug($elementName) }}_{{ $operation }}">
                                            {{ ucfirst($operation) }}
                                          </label>
                                        </div>
                                      @endforeach
                                    @endforeach
                                  </div>
                                  @else
                                  <span class="text-muted small"><i class="fas fa-info-circle me-1"></i>No operations available</span>
                                  @endif
                                </td>
                              </tr>
                            @endforeach
                          @endif
                        @endforeach
                      @else
                      <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                          <i class="fas fa-inbox me-2"></i>No sidebar elements found
                        </td>
                      </tr>
                      @endif
                    </tbody>
                  </table>
                </div>

                @error('sidebarPermissions')
                <div class="text-danger mt-2 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                @enderror

                @error('pageWisePermissions')
                <div class="text-danger mt-2 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                @enderror
              </div>

              {{-- ===================================
              SECTION 4: ACTION BUTTONS
              =================================== --}}
              <div class="text-end mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary btn-lg me-3">
                  <i class="fas fa-save me-2"></i> Update Role & Permissions
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
  CUSTOM STYLES
  =================================== --}}
  <style>
    /* Table header styling */
    .custom-table-header {
      background-color: #FF9F43 !important;
      color: white !important;
    }

    .custom-table-header th {
      color: white !important;
      font-weight: 600;
    }

    /* Checkbox styling */
    .form-check-input {
      cursor: pointer;
    }

    .form-check-input:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    /* Role-based checkbox styling (blue border) */
    .form-check-input.border-primary {
      border-color: #FF9F43 !important;
      border-width: 2px !important;
    }

    .form-check-input.border-primary:checked {
      background-color: #FF9F43 !important;
    }

    /* Small checkboxes for operations */
    .custom-checkbox-small {
      width: 0.9em;
      height: 0.9em;
    }

    /* Role indicators */
    .text-primary {
      color: #FF9F43 !important;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
      .table-responsive {
        font-size: 0.9em;
      }

      .btn-lg {
        font-size: 0.9em;
        padding: 0.5rem 1rem;
      }
    }

    /* Loading state */
    [wire\:loading] .fa-spinner {
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    /* Sticky header enhancement */
    .sticky-top {
      top: 0;
      z-index: 10;
    }

    /* Alert enhancements */
    .alert {
      border-left: 4px solid;
    }

    .alert-info {
      border-left-color: #0dcaf0;
    }

    .alert-success {
      border-left-color: #198754;
    }

    .alert-danger {
      border-left-color: #dc3545;
    }

    /* Role Tag Styles */
    .role-tag-container {
      min-height: 46px;
      border: 1px solid #ced4da;
      border-radius: 6px;
      padding: 8px 12px;
      background: white;
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
    }

    .role-tag {
      background-color: #FF9F43;
      color: white;
      padding: 5px 12px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      font-size: 14px;
      font-weight: 500;
    }

    .role-tag-remove {
      margin-left: 8px;
      cursor: pointer;
      font-size: 14px;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .role-tag-remove:hover {
      background-color: rgba(0, 0, 0, 0.2);
    }

    .role-dropdown {
      position: relative;
      display: inline-block;
      width: 100%;
    }

    .role-dropdown-toggle {
      width: 100%;
      text-align: left;
      padding: 10px 15px;
      border: 1px solid #ced4da;
      border-radius: 6px;
      background: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
    }

    .role-dropdown-toggle:hover {
      border-color: #adb5bd;
    }

    .role-dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      width: 100%;
      max-height: 250px;
      overflow-y: auto;
      background: white;
      border: 1px solid #ddd;
      border-radius: 6px;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      margin-top: 5px;
      display: none;
      opacity: 0;
      transform: translateY(-10px);
      transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .role-dropdown-item {
      padding: 10px 15px;
      cursor: pointer;
      border-bottom: 1px solid #f1f1f1;
      display: flex;
      align-items: center;
    }

    .role-dropdown-item:hover {
      background-color: #f8f9fa;
    }

    .role-dropdown-item:last-child {
      border-bottom: none;
    }

    .role-dropdown-item input {
      margin-right: 10px;
    }

    .instructions {
      color: #6c757d;
      font-size: 13px;
    }

    .dropdown-open .role-dropdown-menu {
      display: block;
      opacity: 1;
      transform: translateY(0);
    }

    .dropdown-open .role-dropdown-toggle {
      border-color: #86b7fe;
      outline: 0;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Prevent text selection during Livewire updates */
    .role-dropdown * {
      user-select: none;
    }

    /* Loading state for dropdown */
    .role-dropdown.loading {
      opacity: 0.7;
      pointer-events: none;
    }
  </style>


{{-- ===================================
DROPDOWN SCRIPT (only one clean version)
=================================== --}}
<script>
(function () {
  function bindRoleDropdown() {
    const wrapper = document.getElementById('roleDropdownWrapper');
    if (!wrapper) return;
    if (wrapper.dataset.bound === '1') return;
    wrapper.dataset.bound = '1';

    const toggle = wrapper.querySelector('#roleDropdownToggle');
    const menu   = wrapper.querySelector('.role-dropdown-menu');
    const input  = wrapper.querySelector('#roleSearchInput');
    const list   = wrapper.querySelector('#roleDropdownList');
    const spinner = document.getElementById('dropdownSpinner');

    const open = () => { wrapper.classList.add('dropdown-open'); if (input) input.focus(); };
    const close = () => wrapper.classList.remove('dropdown-open');

    if (toggle) {
      toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        wrapper.classList.contains('dropdown-open') ? close() : open();
      });
    }

    if (menu) {
      menu.addEventListener('click', e => e.stopPropagation());
    }

    if (input && list) {
      input.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        list.querySelectorAll('.role-dropdown-item').forEach(item => {
          const txt = (item.textContent || '').toLowerCase();
          item.style.display = txt.includes(q) ? '' : 'none';
        });
      });
    }

    document.addEventListener('click', function (e) {
      if (wrapper.classList.contains('dropdown-open') && !wrapper.contains(e.target)) {
        close();
      }
    });

    if (window.Livewire) {
      Livewire.on('dropdown-loading', () => {
        wrapper.classList.add('loading');
        if (spinner) spinner.style.display = 'inline-block';
        if (toggle) toggle.style.pointerEvents = 'none';
      });
      Livewire.on('dropdown-loaded', () => {
        wrapper.classList.remove('loading');
        if (spinner) spinner.style.display = 'none';
        if (toggle) toggle.style.pointerEvents = 'auto';
      });
      Livewire.hook && Livewire.hook('message.processed', () => bindRoleDropdown());
    }
  }

  document.addEventListener('livewire:load', bindRoleDropdown);
  document.addEventListener('livewire:navigated', bindRoleDropdown);
})();
</script>
