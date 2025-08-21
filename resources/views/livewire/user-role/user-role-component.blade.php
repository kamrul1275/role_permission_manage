<div>
    <div class="card shadow">
        <div class="card-header text-white d-flex justify-content-between">
            <h5 class="mb-0">User Role Management</h5>
            <div class="d-flex justify-content-end gap-2">
                @can('create', App\Models\User::class)
                <a type="button" class="btn btn-primary btn-sm" wire:navigate href="{{ route('create_user') }}">
                    <i data-feather="plus-circle" class="me-1"></i> Create User Role
                </a>
                @endcan
                {{-- @can('create', App\Models\User::class)
                <a type="button" class="btn btn-success btn-sm" wire:navigate href="{{ route('assign_user_role') }}">
                    <i data-feather="plus-circle" class="me-1"></i> Assign User Role
                </a>
                @endcan --}}
            </div>
        </div>

        <!-- Updated Search Section -->
        <div class="card-body border-bottom bg-light">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="searchName" class="form-label small text-muted">Name</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i data-feather="user"></i></span>
                        <input type="text" id="searchName" wire:model.live.debounce.500ms="searchName"
                            class="form-control" placeholder="Search by name...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="searchEmail" class="form-label small text-muted">Email</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i data-feather="mail"></i></span>
                        <input type="text" id="searchEmail" wire:model.live.debounce.500ms="searchEmail"
                            class="form-control" placeholder="Search by email...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="searchRole" class="form-label small text-muted">Role</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i data-feather="award"></i></span>
                        <input type="text" id="searchRole" wire:model.live.debounce.500ms="searchRole"
                            class="form-control" placeholder="Search by role...">
                    </div>
                </div>
            </div>





            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="form-text">Search across multiple fields</div>
                <button wire:click="resetFilters" wire:loading.attr="disabled" wire:target="resetFilters"
                    class="btn btn-sm btn-outline-secondary">
                    <span wire:loading.remove wire:target="resetFilters">
                        <i data-feather="refresh-cw" class="me-1" style="width: 14px; height: 14px;"></i>
                        Reset Filters
                    </span>
                    <span wire:loading wire:target="resetFilters">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Resetting...
                    </span>
                </button>
            </div>




            {{-- <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="form-text">Search across multiple fields</div>
                <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary">
                    <i data-feather="refresh-cw" class="me-1" style="width: 14px; height: 14px;"></i>
                    Reset Filters
                </button>
            </div> --}}
        </div>
        <div class="card-body table-responsive bg-amber-600 text-white position-relative">


            <table class="table table-striped mb-0">
                <thead class="table-light custom-table-header">
                    <tr>
                        <th>No</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th style="width: 40%">Role & Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($users && $users->count() > 0)
                    @foreach($users as $index => $user)
                    @php
                    $isSuperAdmin = $user->rolePermissions->contains('role_name', 'SuperAdmin');
                    $userCustomPermissions = $user->userPermissions &&
                    is_array($user->userPermissions->page_wise_permissions)
                    ? $user->userPermissions->page_wise_permissions
                    : [];
                    @endphp
                    <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>


                        {{-- Role & Permission --}}



                        <td>
                            @if ($user->rolePermissions->isNotEmpty())
                            <div class="mb-2 d-flex align-items-start">
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ($user->rolePermissions as $role)


                                    <span class="badge bg-info clickable-role d-flex align-items-center py-1 px-2"
                                        style="font-size: 0.75rem; cursor: pointer;"
                                        onclick="togglePermissions('permissions-{{ $user->id }}-{{ $role->id }}', this)">
                                        {{ $role->role_name }}
                                        <i data-feather="chevron-down" class="ms-1 toggle-icon"
                                            style="width: 14px; height: 14px;"></i>
                                    </span>

                                    @endforeach
                                </div>
                            </div>

                            @foreach ($user->rolePermissions as $role)
                            <div id="permissions-{{ $user->id }}-{{ $role->id }}" class="permission-details d-none">
                                <div class="mb-2">
                                    <strong>{{ $role->role_name }} Permissions:</strong>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @forelse ($role->page_wise_permissions ?? [] as $perm)
                                        <span class="badge bg-primary">{{ $perm }}</span>
                                        @empty
                                        <span class="text-muted">No Role Permissions</span>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <strong class="me-1">Custom Permissions:</strong>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @forelse ($userCustomPermissions as $perm)
                                        <span class="badge bg-success">{{ $perm }}</span>
                                        @empty
                                        <span class="text-muted">No Custom Permissions</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <span class="text-muted">No Role Assigned</span>
                            @endif
                        </td>




                        0
                        <td>

                            <div class="d-flex gap-2">
                                @can('update', $user)
                                <a href="{{ route('edit_user_role', ['id' => $user->id]) }}" wire:navigate
                                    class="btn btn-icon btn-sm btn-soft-info rounded-pill">
                                    <i data-feather="edit"></i>
                                </a>
                                @endcan

                                @if (!$isSuperAdmin)
                                @can('delete', $user)
                                <a href="javascript:void(0);"
                                    onclick="if(confirm('Are you sure you want to delete this user?')) { @this.delete({{ $user->id }}) }"
                                    class="btn btn-icon btn-sm btn-soft-danger rounded-pill">
                                    <i data-feather="trash"></i>
                                </a>
                                @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i data-feather="users" class="feather-32 mb-2"></i>
                            <p>No users found matching your criteria</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>

            <div class="mt-3 d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        </div>

    </div>


    <script>
        const expandedPermissions = new Set();
        function initFeather() {
            if (typeof feather !== 'undefined') feather.replace();
            expandedPermissions.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.style.display = 'block';
                    const icon = element.previousElementSibling?.querySelector('.toggle-icon');
                    if (icon) {
                        icon.dataset.feather = 'chevron-up';
                        if (typeof feather !== 'undefined') feather.replace(icon);
                    }
                }
            });
        }
        function togglePermissions(elementId, clickedElement) {
            const element = document.getElementById(elementId);
            const icon = clickedElement.querySelector('.toggle-icon');
            if (element) {
                if (element.style.display === 'none' || element.style.display === '') {
                    element.style.display = 'block';
                    icon.dataset.feather = 'chevron-up';
                    expandedPermissions.add(elementId);
                } else {
                    element.style.display = 'none';
                    icon.dataset.feather = 'chevron-down';
                    expandedPermissions.delete(elementId);
                }
                if (typeof feather !== 'undefined') feather.replace(icon);
            }
        }
        document.addEventListener('DOMContentLoaded', initFeather);
        setInterval(function() {
            const needsInit = document.querySelector('[data-feather]:not(:has(+ svg.feather))');
            if (needsInit && typeof feather !== 'undefined') feather.replace();
        }, 500);
        document.addEventListener('livewire:navigated', () => {
            expandedPermissions.clear();
            setTimeout(initFeather, 100);
        });
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                let shouldInit = false;
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1 && (
                                node.querySelector && node.querySelector('[data-feather]') ||
                                node.hasAttribute && node.hasAttribute('data-feather')
                            )) {
                                shouldInit = true;
                            }
                        });
                    }
                });
                if (shouldInit) setTimeout(initFeather, 100);
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }



// role & permission toggle function


    function togglePermissions(elementId, clickedElement) {
        const element = document.getElementById(elementId);
        const icon = clickedElement.querySelector('.toggle-icon');

        if (element.classList.contains('d-none')) {
            element.classList.remove('d-none');
            icon.dataset.feather = 'chevron-up';
        } else {
            element.classList.add('d-none');
            icon.dataset.feather = 'chevron-down';
        }

        if (typeof feather !== 'undefined') feather.replace();
    }







    </script>
</div>