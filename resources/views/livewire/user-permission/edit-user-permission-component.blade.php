<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col">
        <h3 class="page-title">Edit User Permissions</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('user_permission') }}">User Permissions</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Flash Messages -->
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
          <h4 class="card-title mb-0">Edit User Permissions</h4>
        </div>

        <div class="card-body">
          <form wire:submit.prevent="updateUserPermission">

            <!-- User Selection -->
            <div class="mb-4">
              <label for="user_id" class="form-label fw-semibold">Select User</label>
              <select wire:model.live="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror"
                required>
                <option value="">Choose a user...</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" @if($user->id == $user_id) selected @endif>
                  {{ $user->name }} ({{ $user->email }})
                </option>
                @endforeach
              </select>
              @error('user_id')
              <div class="invalid-feedback">
                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
              </div>
              @enderror
            </div>

            <!-- Selected User Info -->
            @if ($selectedUser)
            <div class="alert alert-info mb-4">
              <div class="d-flex align-items-center">
                <i class="fas fa-user-circle me-3 fs-4"></i>
                <div>
                  <strong>Selected User:</strong> {{ $selectedUser->name }}<br>
                  <strong>Email:</strong> {{ $selectedUser->email }}<br>
                  @if($selectedUser->role_id)
                  <strong>Role ID:</strong> {{ $selectedUser->role_id }}
                  @endif
                </div>
              </div>
            </div>
            @endif

            <!-- Permissions Table -->
            <div class="mb-4">
              <label class="form-label fw-semibold">Permissions</label>
              <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-outline-secondary btn-sm me-2" wire:click="clearAllPermissions">
                  <i class="fas fa-times me-1"></i> Clear All
                </button>
                <button type="button" class="btn btn-sm btn-success" wire:click="selectAllPermissions">
                  <i class="fas fa-check me-1"></i> Select All
                </button>
              </div>

              <div class="table-responsive border rounded" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-bordered table-sm align-middle mb-0">
                  <thead class="table-light sticky-top">
                    <tr>
                      <th style="width: 40%;">Element Name</th>
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
                      <td>
                        <i class="fas fa-file me-2 text-secondary"></i>{{ $elementName }}
                      </td>



                      <td class="text-center">
                        <input type="checkbox" class="form-check-input custom-checkbox"
                          wire:click="toggleSidebarPermission('{{ $elementName }}')" @checked(in_array($elementName,
                          $sidebarPermissions)) id="sidebar_{{ $child['id'] }}">
                      </td>




                      <td>
                        @if (!empty($pageWisePermissionData[$elementName]))
                        <div class="d-flex flex-wrap gap-2">
                          @foreach ($pageWisePermissionData[$elementName] as $permGroup)
                          @foreach ($permGroup['operations_array'] ?? [] as $op)
                          @php $perm = $elementName . ':' . $op; @endphp
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input custom-checkbox-small page-checkbox"
                              wire:click="togglePagePermission('{{ $perm }}')"
                              @if($this->isPagePermissionSelected($perm)) checked @endif
                            id="perm_{{ Str::slug($elementName) }}_{{ $op }}">
                            <label class="form-check-label small" for="perm_{{ Str::slug($elementName) }}_{{ $op }}">
                              {{ ucfirst($op) }}
                            </label>
                          </div>



                          @endforeach
                          @endforeach
                        </div>
                        @else
                        <span class="text-muted small">No operations</span>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                    @endif
                    @endforeach
                    @else
                    <tr>
                      <td colspan="3" class="text-center text-muted">No child sidebar elements found.</td>
                    </tr>
                    @endif
                  </tbody>
                </table>
              </div>

              <!-- Permission Counts -->
              <div class="row mt-3">
                <div class="col-md-6">
                  <div class="alert alert-light">
                    <i class="fas fa-bars me-2"></i>
                    Sidebar Permissions: <strong>{{ $this->sidebarPermissionsCount }}</strong>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="alert alert-light">
                    <i class="fas fa-cogs me-2"></i>
                    Page Operations: <strong>{{ $this->pageWisePermissionsCount }}</strong>
                  </div>
                </div>
              </div>

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

            <!-- Submit -->
            <div class="text-end mt-4">
              <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove><i class="fas fa-save me-1"></i> Update User Permissions</span>
                <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> Updating...</span>
              </button>
              <a href="{{ route('user_permission') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-times me-1"></i> Cancel
              </a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>