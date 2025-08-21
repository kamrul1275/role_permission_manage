<?php

namespace App\Livewire\Post;

use App\Models\Post;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Gate;


class PostComponent extends Component
{

    // use WithPagination; // Add this
    // protected $paginationTheme = 'bootstrap'; // For Bootstrap styling


    public $posts;



    public function mount()
    {
        $this->authorize('viewAny', Post::class);
        $this->posts = Post::with('user')->latest()->get();
        //   dd($this->posts);
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);
        // dd($post);
        $this->authorize('delete', $post);

        $post->delete();
        session()->flash('success', 'Post deleted successfully!');
        return redirect()->route('posts');
    }




    #[Layout('components.layouts.app.base')]

    public function render()
    {

        // $this->authorize('viewAny', Post::class);
        return view('livewire.post.post-component');
    }
}
