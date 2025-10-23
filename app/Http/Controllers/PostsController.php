<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\{
    AcceptPostRequest,
    DeletePostRequest,
    RejectPostRequest,
    StorePostRequest,
    UpdatePostRequest,
};
use App\Models\{
    Post,
    Setting
};
use Illuminate\Http\Request;


class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gender = request('gender');
        $genders = $gender == 'female' ? ['انثى', 'female'] : ['ذكر', 'male'];
        $posts = $gender
            ? Post::status()->whereIn('gender', $genders)->latest()->paginate(10)
            : Post::status()->latest()->paginate(10);
        $posts->getCollection()->transform(function ($post) {
            $post->gender = ($post->gender == 'male' || $post->gender == 'ذكر') ? 'ذكر' : 'انثى';
            return $post->makeHidden(['status', 'user_id']);
        });
        
        $response = $posts;

        if(request('status') == 'pending') {
            $response = [
                'current_page' => $posts->currentPage(),
                'data' => $posts->items(),
                'total' => $posts->total(),
                'per_page' => $posts->perPage(),
                'auto_approval' => Setting::where('key', 'post.automatic_approval')->first()->value
            ];
        }
        return $this->generalResponse($response);
    }

    public function all_for_visitor() {
        $gender = request('gender');
        $genders = $gender == 'female' ? ['انثى', 'female'] : ['ذكر', 'male'];

        $posts = $gender
            ? Post::whereStatus('active')->whereIn('gender', $genders)->latest()->paginate(15)
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

    public function config() {
        Setting::updateOrCreate(
            ['key' => 'post.automatic_approval'],
            ['value' => request('automatic_approval')]
        );
        return $this->generalResponse(null, null, 200);
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
