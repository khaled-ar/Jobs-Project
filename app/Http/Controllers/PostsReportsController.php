<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostReportRequest;
use App\Models\PostsReport;
use Illuminate\Http\Request;

class PostsReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = PostsReport::whereAnswered(0)->with(['post', 'user'])->get()->map(function($report) {
            return [
                'id' => $report->id,
                'post_id' => $report->post_id,
                'username' => $report->user->username,
                'title' => $report->post->title_ar,
                'whatsapp' => $report->post->whatsapp,
                'image_url' => $report->image_url
            ];
        });

        return $this->generalResponse($reports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostReportRequest $request)
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
