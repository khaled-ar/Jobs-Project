<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\{
    AcceptPostRequest,
    DeletePostRequest,
    RejectPostRequest,
    StorePostRequest,
    UpdatePostRequest,
};
use App\Models\Post;
use Illuminate\Http\Request;


class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gender = request('gender');
        $posts = $gender
            ? Post::status()->whereGender($gender)->latest()->paginate(10)
            : Post::status()->latest()->paginate(10);
        $posts->getCollection()->transform(function ($post) {
            return $post->makeHidden(['status', 'user_id']);
        });
        return $this->generalResponse($posts);
    }

    public function all_for_visitor() {
        $gender = request('gender');
        $posts = $gender
            ? Post::whereStatus('active')->whereGender($gender)->latest()->paginate(15)
            : Post::whereStatus('active')->latest()->paginate(15);
        $posts->getCollection()->transform(function ($post) {
            return $post->makeHidden(['status', 'user_id']);
        });
        return $this->generalResponse($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        return $request->store();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function add_post(StorePostRequest $request)
    {
        return $request->store();
    }

    public function accept(AcceptPostRequest $request, Post $post)
    {
        return $request->accept($post);
    }

    public function reject(RejectPostRequest $request, Post $post)
    {
        return $request->reject($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        return $request->update($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeletePostRequest $request, Post $post)
    {
        $post->delete();
        return $this->generalResponse(null, 'Deleted Successfully', 200);
    }
}
