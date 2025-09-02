<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\{
    DeletePostRequest,
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
        return $this->generalResponse(
            $gender
            ? Post::whereGender($gender)->latest()->paginate(10)
            : Post::latest()->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        return $request->store();
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
