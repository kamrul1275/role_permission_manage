<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col">
        <h3 class="page-title">Sidebar Overview</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Sidebars</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- /Page Header -->

  <div class="row">
    <div class="col-md-12">
      <div class="card">

        <!-- Card Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title mb-0">Sidebar List</h4>

          @can('create',App\Models\Sidebar::class)
          <a type="button" class="btn btn-primary" wire:navigate href="{{ route('create_sidebar') }}">
            <i data-feather="plus-circle" class="me-1"></i> Add Sidebar
          </a>
          @endcan

        </div>

        <!-- Card Body -->
        <div class="card-body">
          <div class="table-responsive">
            <table class="table text-nowrap">
              <thead class="table-primary custom-table-header">
                <tr>
                  <th>No</th>
                  <th>Icon</th>
                  <th>Element Name</th>
                  <th>URL</th>
                  <th>Position</th>
                  <th>Parent Sidebar</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if ($sidebars)
                @forelse($sidebars as $sidebar)
                <tr>
                  <th scope="row">{{ $loop->iteration }}</th>

                  <!-- Icon -->
                  <td>
                    @if(Str::startsWith($sidebar->sidebar_icon, '<svg')) {!! $sidebar->sidebar_icon !!}
                      <!-- Render raw SVG -->
                      @else
                      <i class="{{ $sidebar->sidebar_icon }}"></i> <!-- Render font class -->
                      @endif
                  </td>

                  <!-- Sidebar Data -->
                  <td>{{ $sidebar->element_name }}</td>
                  <td>{{ $sidebar->element_url ?? 'N/A' }}</td>
                  <td>{{ $sidebar->position }}</td>
                  <td>{{ $sidebar->parent?->element_name ?? 'Root' }}</td>

                  <!-- Action Buttons -->
                  <td>
                    <div class="hstack gap-2 fs-15">
                      @can('update', $sidebar)
                      <a wire:navigate href="{{ route('edit_sidebar', $sidebar->id) }}"
                        class="btn btn-icon btn-sm btn-soft-info rounded-pill" title="Edit">
                        <i class="feather-edit"></i>
                      </a>
                      @endcan

                      @can('delete', $sidebar)
                      <button wire:click="delete({{ $sidebar->id }})"
                        class="btn btn-icon btn-sm btn-soft-danger rounded-pill" title="Delete">
                        <i class="feather-trash-2"></i>
                      </button>
                      @endcan
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center text-muted">No sidebar elements found.</td>
                </tr>
                @endforelse
                @endif
              </tbody>
            </table>

          </div>

          <div class="mt-3 d-flex justify-content-center">
            {{ $sidebars->links('pagination::bootstrap-5') }}
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
  </style>

</div>