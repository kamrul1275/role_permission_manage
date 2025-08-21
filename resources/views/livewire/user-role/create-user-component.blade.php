<div class="container mt-5">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Create New User</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user_list') }}">User List</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ul>
            </div>
        </div>
    </div>



    <div class="card shadow-lg">
        {{-- <div class="card-header bg-primary text-white"> --}}
            {{-- <h4 class="mb-0 text-white">
                <i class="fas fa-user-plus me-2"></i>
                Create New User
            </h4> --}}

            <h4 class="card-title mb-0" style="padding:6px 6px; background-color: #FF9F43; color: white;" !important;
                color: white !important;> Create New User</h4>
            {{--
        </div> --}}
        {{-- </div> --}}
    <div class="card-body">

        {{-- Flash Messages --}}
        @if (session()->has('success'))
        <div class="alert alert-primary alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Form --}}
        <form wire:submit.prevent="createUser">
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Name</label>
                <input type="text" wire:model.defer="name" id="name" class="form-control">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email</label>
                <input type="email" wire:model.defer="email" id="email" class="form-control">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" wire:model.defer="password" id="password" class="form-control">
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-bold">Confirm Password</label>
                <input type="password" wire:model.defer="password_confirmation" id="password_confirmation"
                    class="form-control">
            </div>









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



            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Create User
            </button>
        </form>
    </div>
</div>

<style>
    . btn-primary {
        padding: 5px !important;
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
</div>