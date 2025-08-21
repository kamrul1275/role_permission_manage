<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">📋 Post List</h5>
            @can('create', App\Models\Post::class)
            <a href="{{ route('create_post') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Post
            </a>
            @endcan
        </div>

        <div class="card-body p-0">
  <table class="table text-nowrap">
    <thead class="table-primary">
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 25%;">Title</th>
            <th>Content</th>
            <th style="width: 10%;">User Name</th>
            <th style="width: 20%;">Action</th>
        </tr>
    </thead>
    <tbody>
        @if (count($posts))
            @foreach ($posts as $post)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>

                    <td>
                        @can('view', $post)
                            <strong>{{ $post->title }}</strong>
                        @else
                            <strong>Restricted Content</strong>
                        @endcan
                    </td>

                    <td>
                        @can('view', $post)
                            {{ Str::limit($post->content, 100) }}
                        @else
                            Restricted Content
                        @endcan
                    </td>

                    <td>{{ $post->user->name ?? '' }}</td>

                    <td>
                        <div class="hstack gap-2 fs-15">
                            @can('update', $post)
                                <a href="{{ route('edit_post', $post->id) }}"
                                   class="btn btn-icon btn-sm btn-soft-info rounded-pill"
                                   title="Edit">
                                    <i class="feather-edit"></i>
                                </a>
                            @endcan

                            @can('delete', $post)
                                <button wire:click="deletePost({{ $post->id }})"
                                        class="btn btn-icon btn-sm btn-soft-danger rounded-pill"
                                        title="Delete">
                                    <i class="feather-trash-2"></i>
                                </button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" class="text-center text-muted">No posts found.</td>
            </tr>
        @endif
    </tbody>
</table>

{{-- 
    <div class="p-3">
      {{ $posts->links() }}
    </div> --}}
        </div>
    </div>
</div>