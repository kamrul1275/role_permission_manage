<div>
  <div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="page-title mb-1">Role & Permission Overview</h3>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a>
              </li>
              <li class="breadcrumb-item active">Roles</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <!-- Card Header - Responsive -->
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-md-6 col-12 mb-2 mb-md-0">
                <h5 class="card-title mb-0">Role List with Permissions</h5>
              </div>
              <div class="col-12 col-md-6 text-md-end">
                @can('create', App\Models\RoleAndPermission::class)
                <a href="{{ route('create_role') }}" wire:navigate class="btn btn-primary btn-sm">
                  <i data-feather="plus" class="me-1" style="width: 16px; height: 16px;"></i>
                  Create Role
                </a>
                @endcan
              </div>

            </div>
          </div>

          <div class="card-body p-0">
            @if($roles->count() > 0)

            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-lg-block">
              <table class="table table-striped mb-0">

                <table class="table">
                  <thead class="table-light custom-table-header">
                    <tr>
                      <th>No</th>
                      <th>Role Name</th>
                      <th>Sidebar Permissions</th>
                      <th>Page-wise Permissions</th>
                      <th class="text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($roles as $role)
                    <tr>
                      <td class="px-4 py-1">{{ $loop->iteration }}</td>
                      <td class="py-1">
                        <h6 class="mb-0">{{ $role->role_name }}</h6>
                      </td>
                      <td class="py-1">
                        @php
                        $sidebarPermissions = is_string($role->sidebar_permissions)
                        ? json_decode($role->sidebar_permissions, true) ?: []
                        : ($role->sidebar_permissions ?: []);
                        @endphp

                        @if(count($sidebarPermissions) > 0)
                        <div class="d-flex flex-wrap gap-1">
                          @foreach($sidebarPermissions as $permission)
                          <span class="badge bg-primary">{{ $permission }}</span>
                          @endforeach
                        </div>
                        @else
                        <span class="text-muted">No sidebar access</span>
                        @endif
                      </td>
                      <td class="py-3">
                        @php
                        $pageWisePermissions = is_string($role->page_wise_permissions)
                        ? json_decode($role->page_wise_permissions, true) ?: []
                        : ($role->page_wise_permissions ?: []);
                        @endphp

                        @if(count($pageWisePermissions) > 0)
                        <div class="d-flex flex-wrap gap-1">
                          @foreach($pageWisePermissions as $permission)
                          <span class="badge bg-success">{{ $permission }}</span>
                          @endforeach
                        </div>
                        @else
                        <span class="text-muted">No page access</span>
                        @endif
                      </td>






                      <td class="py-3 text-center">
                        <div class="d-flex gap-2 justify-content-center">
                          @can('update', $role)
                          <a href="{{ route('edit_role_permission', $role->id) }}" wire:navigate
                            class="btn btn-icon btn-sm btn-soft-info rounded-pill">
                            <i class="feather-edit"></i>
                          </a>
                          @endcan

                          @php
                          $isSuperAdmin = $role->role_name === 'SuperAdmin';

                          // dd($isSuperAdmin);
                          @endphp

                          @can('delete', $role)
                          @if (! $isSuperAdmin)
                          <button wire:click="delete({{ $role->id }})"
                            wire:confirm="Are you sure you want to delete this role?"
                            class="btn btn-icon btn-sm btn-soft-danger rounded-pill">
                            <i class="feather-trash"></i>
                          </button>
                          @endif
                          @endcan
                        </div>
                      </td>





                    </tr>
                    @endforeach
                  </tbody>
                </table>
            </div>

            <!-- Mobile/Tablet Card View -->

            <!-- Mobile/Tablet Card View -->
            <div class="d-lg-none">
              @foreach($roles as $role)
              <div class="card mb-3 shadow-sm">
                <div class="card-body">
                  <h6 class="mb-2">{{ $role->role_name }}</h6>

                  <!-- Sidebar Permissions -->
                  <p class="mb-1 fw-bold">Sidebar Permissions:</p>
                  @php
                  $sidebarPermissions = is_string($role->sidebar_permissions)
                  ? json_decode($role->sidebar_permissions, true) ?: []
                  : ($role->sidebar_permissions ?: []);
                  @endphp
                  @if(count($sidebarPermissions) > 0)
                  <div class="d-flex flex-wrap gap-1 mb-2">
                    @foreach($sidebarPermissions as $permission)
                    <span class="badge bg-primary">{{ $permission }}</span>
                    @endforeach
                  </div>
                  @else
                  <span class="text-muted">No sidebar access</span>
                  @endif

                  <!-- Page-wise Permissions -->
                  <p class="mb-1 fw-bold">Page-wise Permissions:</p>
                  @php
                  $pageWisePermissions = is_string($role->page_wise_permissions)
                  ? json_decode($role->page_wise_permissions, true) ?: []
                  : ($role->page_wise_permissions ?: []);
                  @endphp
                  @if(count($pageWisePermissions) > 0)
                  <div class="d-flex flex-wrap gap-1 mb-2">
                    @foreach($pageWisePermissions as $permission)
                    <span class="badge bg-success">{{ $permission }}</span>
                    @endforeach
                  </div>
                  @else
                  <span class="text-muted">No page access</span>
                  @endif

                  <!-- Actions -->
                  <div class="d-flex gap-2 mt-3">
                    @can('update', $role)
                    <a href="{{ route('edit_role_permission', $role->id) }}" wire:navigate
                      class="btn btn-sm btn-outline-info">
                      <i class="feather-edit"></i> Edit
                    </a>
                    @endcan

                    @php $isSuperAdmin = $role->role_name === 'SuperAdmin'; @endphp

                    @can('delete', $role)
                    @if (! $isSuperAdmin)
                    <button wire:click="delete({{ $role->id }})"
                      wire:confirm="Are you sure you want to delete this role?" class="btn btn-sm btn-outline-danger">
                      <i class="feather-trash"></i> Delete
                    </button>
                    @endif
                    @endcan
                  </div>
                </div>
              </div>
              @endforeach
            </div>


            <!-- Pagination Links -->
            <div class="p-3">
              {{ $roles->links() }}
            </div>

            @else
            <!-- Empty State -->
            <div class="text-center py-5">
              <div class="mb-3">
                <i data-feather="users" class="text-muted" style="width: 48px; height: 48px;"></i>
              </div>
              <h5 class="text-muted mb-2">No roles found</h5>
              <p class="text-muted mb-3">Create your first role to get started.</p>
              @can('create',App\Models\RoleAndPermission::class)
              <a href="{{ route('create_role_permission') }}" wire:navigate class="btn btn-primary">
                <i data-feather="plus" class="me-1" style="width: 16px; height: 16px;"></i>
                Create Role
              </a>
              @endcan
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .custom-table-header {
      background-color: #FF9F43 !important;
      color: white !important;
    }

    .custom-table-header th {
      color: white !important;
    }

    .table th,
    .table td {
      padding: 5px !important;
    }
  </style>


  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Feather icons
      if (typeof feather !== 'undefined') {
        feather.replace();
      }
    });



  </script>
</div>