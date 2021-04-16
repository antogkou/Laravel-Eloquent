<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

class PostController extends Controller
{
    // public function __construct()
    // {
    //     $this->authorizeResource(Post::class, 'post');
    // }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index()
    {
        return view('post.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): View
    {
        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:255',
        ]);

        Post::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect('/posts/');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View|Response
     */
    public function show(int $id)
    {
        $post = Post::find($id);
        return view('post.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View|Response
     */
    public function edit(int $id)
    {
        $post = Post::find($id);
        return view('post.edit', ['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:255',
            ]);
            $post = Post::find($id);
            $this->authorize('update', $post);
            $post->body = $request->input('body');
            $post->title = $request->input('title');
            $post->save();
            return redirect()->route('post.index')
                ->with('success', 'Post updated successfully');
        } catch (QueryException $ex) {
            return redirect()->route('post.index')
                ->withErrors('error', 'Post not updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $post = Post::find($id);
            $post->delete();
            return redirect()->route('post.index')
                ->with('success', 'Post deleted.');
        } catch (QueryException $ex) {
            return redirect()->route('post.index')
                ->withErrors('error', 'Post not deleted');
        }
    }

    public function like($id): RedirectResponse
    {
        try {
            $user = auth()->user();
            $post = Post::find($id);

            if ($user->likedPosts->contains($post->id)) {
                $post->unlike();
                return redirect()->route('post.index')
                    ->with('success', 'Post unliked!');
            }
            $post->like();
            return redirect()->route('post.index')
                ->with('success', 'Post liked!');
        } catch (QueryException $ex) {
            return redirect()->route('post.index')
                ->withErrors(['Something went wrong', $ex->errorInfo]);
        }
    }
}
