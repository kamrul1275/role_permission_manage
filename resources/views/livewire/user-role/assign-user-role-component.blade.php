<div class="container mt-5">
    <div class="card shadow-lg">
        {{-- <div class="card-header bg-primary text-white">
            <h4 class="mb-0 text-white">Assign Role to User</h4>
        </div> --}}

  <h4 class="card-title mb-0" > Assign Role to User</h4>

        <div class="card-body">

            {{-- Flash Messages --}}
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- User Selection --}}
            <div class="mb-3">
                <label for="user" class="form-label">Select User</label>
                <select id="user" wire:model="selectedUser" class="form-select">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Role Selection --}}
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


{{-- <div class="mb-3">
    <label class="form-label">Select Roles</label>
    @foreach($roles as $role)
        <div class="form-check">
            <input class="form-check-input" type="checkbox" 
                   wire:model="selectedRoles" 
                   value="{{ $role->id }}" 
                   id="role_{{ $role->id }}">
            <label class="form-check-label" for="role_{{ $role->id }}">
                {{ $role->role_name }}
            </label>
        </div>
    @endforeach
</div> --}}


            {{-- Assign Button --}}
            <button wire:click="assignRole" class="btn btn-success">
                Assign Role
            </button>
        </div>
    </div>

    <style>
       
       h4{
padding:6px 6px; 
background-color: #FF9F43; 
color: white !important; 

       }
       
       
    </style>
</div>
