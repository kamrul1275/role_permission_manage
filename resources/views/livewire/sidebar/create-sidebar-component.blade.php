<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col">
        <h3 class="page-title">Add Sidebar Element</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('sidebar') }}">Sidebars</a></li>
          <li class="breadcrumb-item active">Create</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- /Page Header -->

  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title mb-0">Create Sidebar</h4>
        </div>

        <div class="card-body">
          <form wire:submit.prevent="store">

            <!-- Element Name -->
            <div class="mb-3">
              <label class="form-label">Element Name <span class="text-danger">*</span></label>
              <input type="text" wire:model="element_name"
                class="form-control @error('element_name') is-invalid @enderror">
              @error('element_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Element URL -->
            <div class="mb-3">
              <label class="form-label">Element URL</label>
              <input type="text" wire:model="element_url"
                class="form-control @error('element_url') is-invalid @enderror">
              @error('element_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Sidebar Icon -->
            <div class="mb-3">
              <label class="form-label">Sidebar Icon</label>
              <input type="text" wire:model="sidebar_icon"
                class="form-control @error('sidebar_icon') is-invalid @enderror"
                placeholder="e.g., fa fa-home or bx bx-user">
              @error('sidebar_icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="form-text text-muted">Use icon class like <code>fa fa-home</code> or
                <code>bx bx-user</code></small>
            </div>


            <!-- Position -->
            <div class="mb-3">
              <label class="form-label">Position <span class="text-danger">*</span></label>
              <input type="number" wire:model="position" class="form-control @error('position') is-invalid @enderror">
              @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Parent Sidebar -->
            <div class="mb-3">
              <label class="form-label">Parent Sidebar</label>
              <select wire:model="sidebar_id" class="form-select @error('sidebar_id') is-invalid @enderror">
                <option value="">-- Root (No Parent) --</option>
                @foreach($allSidebars as $sidebar)
                <option value="{{ $sidebar->id }}">{{ $sidebar->element_name }}</option>
                @endforeach
              </select>
              @error('sidebar_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Submit Button -->
            <div class="text-end">
              <button type="submit" class="btn btn-success">
                <i data-feather="save" class="me-1"></i> Save Sidebar
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>