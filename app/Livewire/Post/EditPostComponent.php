<?php

namespace App\Livewire\Post;

use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Layout;

class EditPostComponent extends Component
{


    public $postId;
    public $title;
    public $content;



    public function mount()
    {
        $post = Post::findOrFail($this->postId);
        $this->title = $post->title;
        $this->content = $post->content;
    }




    public function update()
{
    $this->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);

    $data = Post::findOrFail($this->postId); // ✅ FIXED

    $data->title = $this->title;
    $data->content = $this->content;
    $data->save();

    session()->flash('success', 'Post updated successfully!');
    $this->reset(['title', 'content']);
    return redirect()->route('posts');
}




    #[Layout('components.layouts.app.base')]
    public function render()
    {

        $post = Post::first(); // or new Post if no posts yet

        $this->authorize('update', $post ?? Post::class);

        return view('livewire.post.edit-post-component');
    }
}
