<?php

namespace App\Livewire\Post;

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class CreatePostComponent extends Component
{
    public $title;
    public $content;

    public function store()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $data =  new Post();
        $data->title = $this->title;
        $data->content = $this->content;
        $data->save();
        session()->flash('success', 'Post created successfully!');
        $this->reset(['title', 'content']); // reset form fields
        return redirect()->route('posts'); // redirect to posts list
    }

    #[Layout('components.layouts.app.base')]
    public function render()
    {
    $post = Post::first(); // or new Post if no posts yet

    // $this->authorize('create', $post ?? Post::class);
    $this->authorize('viewAny', Post::class); 
        return view('livewire.post.create-post-component');
    }
}
