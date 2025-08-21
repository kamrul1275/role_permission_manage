<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📝 Edit Post</h5>
                </div>

                <div class="card-body">
                    <form wire:submit.prevent="update">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
       <input type="text" class="form-control @error('title') is-invalid @enderror"
       wire:model="title" placeholder="Enter post title">

                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      wire:model="content"  rows="5" placeholder="Write your content..."></textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-save me-1"></i> Update
                            </button>
                            <a href="{{ route('posts') }}" class="btn btn-secondary px-4">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
