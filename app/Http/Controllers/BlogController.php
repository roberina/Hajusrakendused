<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')
            ->withCount('comments')
            ->latest()
            ->get();

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(Post $post)
    {
        $post->load(['user', 'comments.user']);

        return Inertia::render('Blog/Show', [
            'post'    => $post,
            'isAdmin' => auth()->id() === $post->user_id,
        ]);
    }

    public function create()
    {
        return Inertia::render('Blog/Form', ['post' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $post = Post::create([
            'user_id'     => auth()->id(),
            'title'       => $data['title'],
            'description' => $data['description'],
        ]);

        return redirect()->route('blog.show', $post)->with('success', 'Postitus loodud!');
    }

    public function edit(Post $post)
    {
        if (auth()->id() !== $post->user_id) abort(403);

        return Inertia::render('Blog/Form', ['post' => $post]);
    }

    public function update(Request $request, Post $post)
    {
        if (auth()->id() !== $post->user_id) abort(403);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $post->update($data);

        return redirect()->route('blog.show', $post)->with('success', 'Postitus uuendatud!');
    }

    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id) abort(403);

        $post->delete();

        return redirect()->route('blog.index')->with('success', 'Postitus kustutatud!');
    }

    public function storeComment(Request $request, Post $post)
    {
        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $post->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $data['body'],
        ]);

        return redirect()->back()->with('success', 'Kommentaar lisatud!');
    }

    public function destroyComment(Post $post, Comment $comment)
    {
        if (auth()->id() !== $comment->user_id && auth()->id() !== $post->user_id) {
            abort(403);
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Kommentaar kustutatud!');
    }
}