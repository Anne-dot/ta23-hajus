<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    return Inertia::render('post/Index', [
        'posts' => Post::with('user')
            ->latest()
            ->paginate(10)
            ->through(fn ($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'excerpt' => Str::limit($post->description, 100),
                'created_at' => $post->created_at,
                'created_at_for_humans' => $post->created_at_for_humans,
                'user' => $post->user ? ['id' => $post->user->id, 'name' => $post->user->name] : null,
                'is_owner' => $post->user_id === auth()->id(),
                'can_edit' => $post->user_id === auth()->id() || auth()->user()?->is_admin,
            ]),
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('post/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);

        $validated['user_id'] = auth()->id();

        Post::create($validated);

        return redirect()->route('posts.index');
    }


    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load([
            'comments.user', 
            'user']);
        
        return Inertia::render('post/Show', [
            'post' => $post,
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        if (auth()->id() !== $post->user_id && !auth()->user()->is_admin) {
            abort(403);
        }
        
        return Inertia::render('post/Edit', [
            'post' => $post
        ]);
    }
    
    public function update(Request $request, Post $post)
    {
        if (auth()->id() !== $post->user_id && !auth()->user()->is_admin) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);
        
        $post->update($validated);
        
        return redirect()->route('posts.show', $post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // Optional: Add authorization check
        if (auth()->id() !== $post->user_id && !auth()->user()->is_admin) {
            abort(403);
        }
        
        $post->delete();
        
        return redirect()->route('posts.index');
    }
}
