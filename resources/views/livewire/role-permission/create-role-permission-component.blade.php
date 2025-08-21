
<div>
<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col">
        <h3 class="page-title">Create New Role</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('role_permission') }}">Role & Permissions</a></li>
          <li class="breadcrumb-item active">Create</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Flash Messages -->
  @foreach (['success', 'error', 'info'] as $msg)
  @if (session()->has($msg))
  <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} alert-dismissible fade show" role="alert">
    <i class="fas fa-{{ $msg == 'success' ? 'check' : ($msg == 'error' ? 'exclamation' : 'info') }}-circle me-2"></i>
    {{ session($msg) }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif
  @endforeach

  <div class="row">
    <div class="col-12">
      <div class="card">
        <h4 class="card-title mb-0">Create New Role</h4>

        <div class="card-body">
          <form wire:submit.prevent="storeRolePermission">
            <!-- Role Name Input -->
            <div class="mb-3">
              <label for="role_name" class="form-label fw-semibold">Role Name</label>
              <input type="text" wire:model.blur="roleName" id="roleName"
                class="form-control @error('roleName') is-invalid @enderror" placeholder="Enter role name" required>
              @error('roleName')
              <div class="invalid-feedback"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
              @enderror
            </div>

            <!-- Unified Permissions Table -->
            <div class="mb-2">
              <label class="form-label fw-semibold">Permissions</label>
              <div class="d-flex justify-content-end mb-1 flex-wrap">
                <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-1"
                  wire:click="clearAllPermissions">
                  <i class="fas fa-times me-1"></i> Clear All
                </button>
                <button type="button"
                  class="btn btn-sm {{ $this->areAllPermissionsSelected() ? 'btn-danger' : 'btn-success' }} mb-1"
                  wire:click="selectAllPermissions">
                  <i class="fas fa-{{ $this->areAllPermissionsSelected() ? 'minus' : 'check' }} me-1"></i>
                  {{ $this->areAllPermissionsSelected() ? 'Deselect All' : 'Select All' }}
                </button>
              </div>

              <div class="table-responsive border rounded" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-bordered table-sm align-middle mb-0">
                  <thead class="table-light sticky-top custom-table-header">
                    <tr>
                      <th style="min-width: 150px;">Element/Page Name</th>
                      <th class="text-center" style="min-width: 100px;">Sidebar Access</th>
                      <th class="text-center" style="min-width: 200px;">Page Operations</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                    $processedPages = [];
                    @endphp

                    {{-- Parent Rows --}}
                    @foreach ($sidebarData[null] ?? [] as $parent)
                    @php
                    $elementName = $parent['element_name'];
                    $processedPages[] = $elementName;
                    @endphp
                    <tr class="parent-row">
                      <td><strong class="text-primary"><i class="fas fa-folder me-2"></i>{{ $elementName }}</strong></td>
                      <td class="text-center">
                        @if($this->hasPageOperations($pageName))
                        <input type="checkbox" wire:model.live="sidebarPermissions" value="{{ $pageName }}">
                        @else
                        <span class="text-muted small">No operations</span>
                        @endif
                      </td>
                      <td class="text-center">
                        <span class="text-muted small">—</span>
                      </td>
                    </tr>

                    {{-- Child Rows --}}
                    @foreach ($sidebarData[$parent['id']] ?? [] as $child)
                    @php
                    $childElement = $child['element_name'];
                    $processedPages[] = $childElement;
                    @endphp
                    <tr class="child-row">
                      <td class="ps-4">
                        <span class="text-secondary">↳ <i class="fas fa-file me-2"></i>{{ $childElement }}</span>
                      </td>
                      <td class="text-center">
                        @if($this->hasPageOperations($pageName))
                        <input type="checkbox" wire:model.live="sidebarPermissions" value="{{ $pageName }}">
                        @else
                        <span class="text-muted small">No operations</span>
                        @endif
                      </td>
                      <td>
                        @if($this->shouldShowPageOperations($childElement))
                        <div class="d-flex flex-wrap gap-2">
                          @foreach ($pageWisePermissionData[$childElement] as $permissionItem)
                          @foreach ($permissionItem['operations_array'] as $op)
                          @php $perm = $childElement . ':' . $op; @endphp
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input custom-checkbox-small page-checkbox"
                              wire:model.live="pageWisePermissions" value="{{ $perm }}"
                              id="perm_{{ Str::slug($childElement) }}_{{ $op }}"
                              @if($this->isPermissionSelected($perm)) checked @endif>
                            <label class="form-check-label small"
                              for="perm_{{ Str::slug($childElement) }}_{{ $op }}">{{ ucfirst($op) }}</label>
                          </div>
                          @endforeach
                          @endforeach
                        </div>
                        @else
                        <span class="text-muted small">No page operations available</span>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                    @endforeach

                    {{-- Standalone Pages --}}
                    @foreach ($pageWisePermissionData as $pageName => $permissions)
                    @if(!in_array($pageName, $processedPages))
                    <tr class="standalone-page">
                      <td><strong class="text-info">{{ $pageName }}</strong></td>
                      <td class="text-center">
                        @if($this->hasPageOperations($pageName))
                        <input type="checkbox" wire:model.live="sidebarPermissions" value="{{ $pageName }}">
                        @else
                        <span class="text-muted small">No operations</span>
                        @endif
                      </td>
                      <td>
                        @if($this->shouldShowPageOperations($pageName))
                        <div class="d-flex flex-wrap gap-2">
                          @foreach ($permissions as $permissionItem)
                          @foreach ($permissionItem['operations_array'] as $op)
                          @php $perm = $pageName . ':' . $op; @endphp
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input custom-checkbox-small page-checkbox"
                              wire:model.live="pageWisePermissions" value="{{ $perm }}"
                              id="perm_{{ Str::slug($pageName) }}_{{ $op }}" @if($this->isPermissionSelected($perm))
                              checked @endif>
                            <label class="form-check-label small"
                              for="perm_{{ Str::slug($pageName) }}_{{ $op }}">{{ ucfirst($op) }}</label>
                          </div>
                          @endforeach
                          @endforeach
                        </div>
                        @else
                        <span class="text-muted small">No page operations available</span>
                        @endif
                      </td>
                    </tr>
                    @endif
                    @endforeach
                  </tbody>
                </table>
              </div>

              <!-- Validation Errors -->
              @error('sidebarPermissions')
              <div class="text-danger mt-2 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
              @enderror
              @error('pageWisePermissions')
              <div class="text-danger mt-2 small"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
              @enderror
            </div>

            <div class="text-end mt-4">
              <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span><i class="fas fa-save me-1"></i> Save Role</span>
              </button>
              <a href="{{ route('role_permission') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-times me-1"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<style>
  /* Responsive adjustments */
  @media (max-width: 768px) {

        h4 {
      padding: 6px 6px;
      background-color: #FF9F43;
      color: white !important;
    }







    .custom-table-header {
      background-color: #FF9F43 !important;
      color: white !important;
    }

    .custom-table-header th {
      color: white !important;
    }
    .table-responsive {
      border: 0;
    }
    
    .table thead {
      display: none;
    }
    
    .table tr {
      display: block;
      margin-bottom: 1rem;
      border: 1px solid #dee2e6;
      border-radius: 0.25rem;
    }
    
    .table td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.75rem;
      border-bottom: 1px solid #dee2e6;
    }
    
    .table td:before {
      content: attr(data-label);
      font-weight: bold;
      margin-right: 1rem;
      flex: 0 0 40%;
    }
    
    .table td:last-child {
      border-bottom: 0;
    }
    
    .parent-row td:first-child,
    .child-row td:first-child,
    .standalone-page td:first-child {
      font-size: 1rem;
    }
    
    .child-row td:first-child {
      padding-left: 2rem;
    }
    
    /* Make checkboxes more touch-friendly on mobile */
    .form-check-input {
      width: 1.2em;
      height: 1.2em;
    }
    
    /* Adjust button spacing */
    .d-flex.justify-content-end {
      justify-content: flex-start !important;
    }
    
    .d-flex.justify-content-end .btn {
      margin-right: 0.5rem;
      margin-bottom: 0.5rem;
    }
  }
</style>
</div>