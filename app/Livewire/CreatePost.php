<?php
 
namespace App\Livewire;
 
use Livewire\Component;
use App\Models\Post;
 
class CreatePost extends Component
{
    public $title;
 
    public function save() 
    {
        $post = Post::create([
            'title' => $this->title
        ]);
 
        return redirect()->to('/posts')
             ->with('status', 'Post created!');
    }
 
    public function render()
    {
        return view('livewire.pages.index');
    }
}