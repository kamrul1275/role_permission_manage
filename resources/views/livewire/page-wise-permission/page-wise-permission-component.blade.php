<div class="content container-fluid">

  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col">
        <h3 class="page-title">Page Wise Permissions Overview</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">User Permissions</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- /Page Header -->

  <div class="row">
    <div class="col-md-12">
      <div class="card">


        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-md-6 col-12 mb-2 mb-md-0">
              <h5 class="card-title mb-0">Create Page wise Permission</h5>
            </div>
            <div class="col-12 col-md-6 text-md-end">
              <a href="{{ route('create_page_wise_permission') }}" wire:navigate class="btn btn-primary btn-sm">
                <i data-feather="plus" class="me-1" style="width: 16px; height: 16px;"></i>
                Create Page
              </a>

            </div>

          </div>
        </div>



        {{--


        <div class="col-md-6 col-12 mb-2 mb-md-0">
          <h5 class="card-title mb-0">Page Wise Permissions List</h5>
        </div>
        <div class="col-12 col-md-6 text-md-end">
          <a type="button" class="btn btn-primary" href="{{ route('create_page_wise_permission') }}">
            <i data-feather="plus-circle" class="me-1"></i> Create Page wise Permission
          </a>

        </div> --}}




        <!-- Card Body -->
        <div class="card-body">
          <div class="table-responsive">
            <table class="table text-nowrap">
              <thead class="table-primary custom-table-header">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Page Name</th>
                  <th scope="col">Operations Permissions</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($pages as $page)
                <tr>
                  <th scope="row">{{ $loop->iteration }}</th>
                  <td>{{ $page->page_name }}</td>
                  <td>
                    @foreach(json_decode($page->operations ?? '[]') as $op)
                    <span class="badge bg-primary text-uppercase me-1">{{ $op }}</span>
                    @endforeach
                  </td>
                  <td>
                    <div class="hstack gap-2 fs-15">
                      {{-- Edit Button --}}
                      <a href="{{ route('edit_page_permission', ['id'=> $page->id]) }}" wire:navigate
                        class="btn btn-icon btn-sm btn-soft-info rounded-pill">
                        <i class="feather-edit"></i>
                      </a>

                      {{-- Delete Button --}}
                      {{-- <button wire:click="delete({{ $page->id }})"
                        class="btn btn-icon btn-sm btn-soft-danger rounded-pill">
                        <i class="feather-trash-2"></i>
                      </button> --}}

                      <button wire:click="delete({{ $page->id }})"
                        wire:confirm="Are you sure you want to delete this role?"
                        class="btn btn-icon btn-sm btn-soft-danger rounded-pill">
                        <i class="feather-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" class="text-center">No page permissions found.</td>
                </tr>
                @endforelse
              </tbody>
            </table>

          </div>

          <!-- Pagination Links -->
          <div class="p-3">
            {{ $pages->links() }}
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