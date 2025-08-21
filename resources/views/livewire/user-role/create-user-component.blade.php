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


            <div class="mb-3">
                <label for="roles" class="form-label">Select Roles</label>
                <select id="roles" wire:model="selectedRoles" class="form-select" multiple>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">
                        {{ $role->role_name }}
                    </option>
                    @endforeach
                </select>

                <small class="form-text text-muted">
                    Hold <kbd>Ctrl</kbd> (Windows) or <kbd>Cmd</kbd> (Mac) to select multiple roles.
                </small>
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
</style>
</div>