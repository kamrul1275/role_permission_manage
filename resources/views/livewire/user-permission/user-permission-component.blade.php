<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col">
        <h3 class="page-title">User Permissions Overview</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">User Permissions</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- /Page Header -->

  <div class="row">
    <div class="col-12">
      <div class="card">

        <!-- Card Header -->
        <div class="card-header">
          <div
            class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
            <h4 class="card-title mb-0">User Permissions List</h4>
            @can('create', App\Models\UserPermission::class)
            <a type="button" class="btn btn-primary btn-sm" wire:navigate href="{{ route('create_user_permission') }}">
              <i data-feather="plus-circle" class="me-1"></i>
              <span class="d-none d-sm-inline">Assign User Permission</span>
              <span class="d-inline d-sm-none">Add</span>
            </a>
            @endcan
          </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-0 p-sm-3">

          <!-- Responsive Table View -->
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="custom-table-header">
                <tr>
                  <th scope="col" class="d-none d-sm-table-cell" style="width: 60px;">No</th>
                  <th scope="col">User</th>
                  <th scope="col" class="d-none d-md-table-cell">Role</th>
                  <th scope="col" class="d-none d-lg-table-cell">Sidebar Permissions</th>
                  <th scope="col" class="d-none d-lg-table-cell">Page-wise Permissions</th>
                  <th scope="col" style="width: 100px;">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($userPermissions as $permission)
                <tr>
                  <td class="d-none d-sm-table-cell">{{ $loop->iteration }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="me-2">
                        <span class="badge bg-primary rounded-circle p-2">
                          {{ strtoupper(substr($permission->user->name ?? 'NA', 0, 2)) }}
                        </span>
                      </div>
                      <div>
                        <div>{{ $permission->user->name ?? 'N/A' }}</div>
                        <div class="d-block d-md-none">
                          <small class="text-muted">{{ $permission->user->rolePermissions->role_name ?? 'N/A' }}</small>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="d-none d-md-table-cell">
                    <span class="badge bg-secondary">{{ $permission->user->rolePermissions->role_name ?? 'N/A' }}</span>
                  </td>

                  <!-- Sidebar Permissions -->
                  <td class="d-none d-lg-table-cell">
                    <div class="d-flex flex-wrap gap-1">
                      @if(is_array($permission->sidebar_permissions) && count($permission->sidebar_permissions))
                      @foreach($permission->sidebar_permissions as $item)
                      <span class="badge bg-info text-white">{{ $item }}</span>
                      @endforeach
                      @else
                      <span class="badge bg-secondary">No permissions</span>
                      @endif
                    </div>
                  </td>

                  <!-- Page-wise Permissions -->
                  <td class="d-none d-lg-table-cell">
                    <div class="d-flex flex-wrap gap-1">
                      @if(is_array($permission->page_wise_permissions) && count($permission->page_wise_permissions))
                      @foreach($permission->page_wise_permissions as $permissionString)
                      @php
                      $parts = explode(':', $permissionString);
                      $page = $parts[0] ?? '';
                      $action = $parts[1] ?? '';
                      @endphp
                      @if($page && $action)
                      <span class="badge bg-success text-white">{{ $page }}:{{ $action }}</span>
                      @endif
                      @endforeach
                      @else
                      <span class="badge bg-secondary">No permissions</span>
                      @endif
                    </div>
                  </td>

                  <!-- Actions -->
                  <td>
                    <div class="d-flex gap-1">
                      @can('update', $permission)
                      <a href="{{ route('edit_user_permission', $permission->id) }}"
                        class="btn btn-icon btn-sm btn-outline-info rounded-pill">
                        <i class="feather-edit"></i>
                      </a>
                      @endcan

                      @can('delete', $permission)
                      <button wire:click='delete({{ $permission->id }})'
                        class="btn btn-icon btn-sm btn-outline-danger rounded-pill">
                        <i class="feather-trash"></i>
                      </button>
                      @endcan
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center py-4">No user permissions found.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>


          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-3 px-3">
            {{ $userPermissions->links('pagination::bootstrap-5') }}
          </div>

        </div>
      </div>
    </div>
  </div>
</div>