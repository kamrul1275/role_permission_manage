<div class="content container-fluid">

  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col">
        <h3 class="page-title">Create Page-wise Permission</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('page_wise_permission') }}">Page Permissions</a></li>
          <li class="breadcrumb-item active">Create</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- /Page Header -->

  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title mb-0">New Page Permission</h4>
        </div>

        <div class="card-body">
          <form wire:submit.prevent="storePagePermission">

            <!-- Page Name -->
            <div class="mb-3">
              <label for="page_name" class="form-label">Page Name</label>
              <input type="text" id="page_name" wire:model.defer="page_name" class="form-control @error('page_name') is-invalid @enderror" placeholder="Enter page name">
              @error('page_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Operations (JSON or comma-separated) -->
            <div class="mb-3">
              <label for="operations" class="form-label">Operations (JSON or comma-separated)</label>
              <input type="text" id="operations" wire:model.defer="operations" class="form-control @error('operations') is-invalid @enderror" placeholder='["view", "create", "edit", "delete"] or view,create,edit'>
              @error('operations') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary">
                <i data-feather="save" class="me-1"></i> Save
              </button>
              <a href="" class="btn btn-secondary ms-2">
                Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>
