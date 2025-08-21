<div>
    <div class="card shadow">
        <div class="card-header text-white d-flex justify-content-between">
            <h5 class="mb-0">User Role Management</h5>
            <div class="d-flex justify-content-end gap-2"> @can('create', App\Models\User::class) <a type="button"
                    class="btn btn-primary btn-sm" wire:navigate href="{{ route('create_user') }}"> <i
                        data-feather="plus-circle" class="me-1"></i> Create User Role </a> @endcan {{-- @can('create',
                App\Models\User::class) <a type="button" class="btn btn-success btn-sm" wire:navigate
                    href="{{ route('assign_user_role') }}"> <i data-feather="plus-circle" class="me-1"></i> Assign User
                    Role </a> @endcan --}} </div>
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
                    </tr> <!-- 🔍 Search Row -->
                    <tr>
                        <th></th> <!-- No column (empty) -->
                        <th>
                            <div class="input-group input-group-sm"> <span class="input-group-text"><i
                                        data-feather="user"></i></span> <input type="text"
                                    wire:model.live.debounce.500ms="searchName" class="form-control"
                                    placeholder="Search name..."> </div>
                        </th>
                        <th>
                            <div class="input-group input-group-sm"> <span class="input-group-text"><i
                                        data-feather="mail"></i></span> <input type="text"
                                    wire:model.live.debounce.500ms="searchEmail" class="form-control"
                                    placeholder="Search email..."> </div>
                        </th>
                        <th>
                            <div class="input-group input-group-sm"> <span class="input-group-text"><i
                                        data-feather="award"></i></span> <input type="text"
                                    wire:model.live.debounce.500ms="searchRole" class="form-control"
                                    placeholder="Search role..."> </div>
                        </th> <!-- Actions column: Reset Filters button -->
                        <th> <button wire:click="resetFilters" wire:loading.attr="disabled" wire:target="resetFilters"
                                class="btn btn-sm w-100"
                                style="background-color: transparent; color: #fff; border: 1px solid #fff;"> <span
                                    wire:loading.remove wire:target="resetFilters"> <i data-feather="refresh-cw"
                                        class="me-1" style="width: 14px; height: 14px; color:white;"></i> Reset Filters
                                </span> <span wire:loading wire:target="resetFilters"> <span
                                        class="spinner-border spinner-border-sm me-1"></span> Resetting... </span>
                            </button> </th>
                    </tr>
                </thead>
                <tbody> @if ($users && $users->count() > 0) @foreach($users as $index => $user) @php $isSuperAdmin =
                    $user->rolePermissions->contains('role_name', 'SuperAdmin'); $userCustomPermissions =
                    $user->userPermissions && is_array($user->userPermissions->page_wise_permissions) ?
                    $user->userPermissions->page_wise_permissions : []; @endphp <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>  
                        <td> @if($user->rolePermissions->isNotEmpty()) 
                            <div class="mb-2 d-flex align-items-start">
                                <div class="d-flex flex-wrap gap-1"> @foreach ($user->rolePermissions as $role) <span
                                        class="badge bg-info clickable-role d-flex align-items-center py-1 px-2"
                                        style="font-size: 0.75rem; cursor: pointer;"
                                        onclick="togglePermissions('permissions-{{ $user->id }}-{{ $role->id }}', this)">
                                        {{ $role->role_name }} <i data-feather="chevron-down" class="ms-1 toggle-icon"
                                            style="width: 14px; height: 14px;"></i> </span> @endforeach </div>
                            </div> @foreach ($user->rolePermissions as $role) <div
                                id="permissions-{{ $user->id }}-{{ $role->id }}" class="permission-details d-none">
                                <div class="mb-2"> <strong>{{ $role->role_name }} Permissions:</strong>
                                    <div class="d-flex flex-wrap gap-1 mt-1"> @forelse ($role->page_wise_permissions ??
                                        [] as $perm) <span class="badge bg-primary">{{ $perm }}</span> @empty <span
                                            class="text-muted">No Role Permissions</span> @endforelse </div>
                                </div>
                                <div class="mt-2"> <strong class="me-1">Custom Permissions:</strong>
                                    <div class="d-flex flex-wrap gap-1 mt-1"> @forelse ($userCustomPermissions as $perm)
                                        <span class="badge bg-success">{{ $perm }}</span> @empty <span
                                            class="text-muted">No Custom Permissions</span> @endforelse </div>
                                </div>
                            </div> @endforeach @else <span class="text-muted">No Role Assigned</span> @endif </td> 0
                        <td>
                            <div class="d-flex gap-2"> @can('update', $user) <a
                                    href="{{ route('edit_user_role', ['id' => $user->id]) }}" wire:navigate
                                    class="btn btn-icon btn-sm btn-soft-info rounded-pill"> <i data-feather="edit"></i>
                                </a> @endcan @if (!$isSuperAdmin) @can('delete', $user) <a href="javascript:void(0);"
                                    onclick="if(confirm('Are you sure you want to delete this user?')) { @this.delete({{ $user->id }}) }"
                                    class="btn btn-icon btn-sm btn-soft-danger rounded-pill"> <i
                                        data-feather="trash"></i> </a> @endcan @endif </div>
                        </td>
                    </tr> @endforeach @else <tr>
                        <td colspan="5" class="text-center text-muted py-4"> <i data-feather="users"
                                class="feather-32 mb-2"></i>
                            <p>No users found matching your criteria</p>
                        </td>
                    </tr> @endif </tbody>
            </table>
            <div class="mt-3 d-flex justify-content-center"> {{ $users->links() }} </div>
        </div>
    </div>

    <script>
        // Track expanded permissions to restore after Livewire re-renders
    let expandedPermissions = new Set();

    // Initialize Feather icons and restore expanded permissions
    function initFeather() {
        if (typeof feather !== 'undefined') feather.replace();

        expandedPermissions.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.classList.remove('d-none');

            const icon = document.querySelector(`[onclick*="${id}"] .toggle-icon`);
            if (icon) icon.dataset.feather = 'chevron-up';
        });

        if (typeof feather !== 'undefined') feather.replace();
    }

    // Toggle permission block when role badge is clicked
    function togglePermissions(elementId, clickedElement) {
        const element = document.getElementById(elementId);
        const icon = clickedElement.querySelector('.toggle-icon');

        if (element.classList.contains('d-none')) {
            element.classList.remove('d-none');
            icon.dataset.feather = 'chevron-up';
            expandedPermissions.add(elementId);
        } else {
            element.classList.add('d-none');
            icon.dataset.feather = 'chevron-down';
            expandedPermissions.delete(elementId);
        }

        if (typeof feather !== 'undefined') feather.replace();
    }

    // Run Feather icon initialization after DOM load
    document.addEventListener('DOMContentLoaded', initFeather);

    // Restore expanded permissions after Livewire updates
    document.addEventListener('livewire:load', initFeather);
    document.addEventListener('livewire:update', initFeather);

    // Optional: Observe DOM changes dynamically (if Livewire swaps nodes)
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(mutations => {
            let shouldInit = false;
            mutations.forEach(mutation => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1 && (
                            node.querySelector && node.querySelector('[data-feather]') ||
                            node.hasAttribute && node.hasAttribute('data-feather')
                        )) {
                            shouldInit = true;
                        }
                    });
                }
            });
            if (shouldInit) setTimeout(initFeather, 50);
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }
    </script>

</div>