<div class="content container-fluid">
  <div class="page-header">
    <div class="row">
      <div class="col">
        <h3 class="page-title">Edit Sidebar Element</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('sidebar') }}">Sidebars</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title mb-0">Edit Sidebar</h4>
        </div>

        <div class="card-body">
          <form wire:submit.prevent="update">

            <div class="mb-3">
              <label class="form-label">Element Name <span class="text-danger">*</span></label>
              <input type="text" wire:model="element_name" class="form-control @error('element_name') is-invalid @enderror">
              @error('element_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Element URL</label>
              <input type="text" wire:model="element_url" class="form-control @error('element_url') is-invalid @enderror">
              @error('element_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Position <span class="text-danger">*</span></label>
              <input type="number" wire:model="position" class="form-control @error('position') is-invalid @enderror">
              @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Parent Sidebar</label>
              <select wire:model="sidebar_id" class="form-select @error('sidebar_id') is-invalid @enderror">
                <option value="">-- Root (No Parent) --</option>
                @foreach($allSidebars as $sb)
                  <option value="{{ $sb->id }}">{{ $sb->element_name }}</option>
                @endforeach
              </select>
              @error('sidebar_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary">
                <i data-feather="edit-2" class="me-1"></i> Update Sidebar
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
