<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostReportRequest;
use App\Jobs\SendFirebaseNotification;
use App\Models\PostsReport;
use Illuminate\Http\Request;
use App\Traits\Files;

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
                'email' => $report->user->email,
                'title' => $report->post->title_ar,
                'whatsapp' => $report->post->whatsapp,
                'image_url' => $report->image_url
            ];
        });

        return $this->generalResponse($reports);
    }

    public function send_answer(Request $request) {
        $data = $request->validate([
            'post_report_id' => ['required', 'integer', 'exists:posts_reports,id'],
            'answer_ar' => ['required', 'string', 'max:1000'],
            'answer_en' => ['required', 'string', 'max:1000'],
        ]);

        $report = PostsReport::whereId($data['post_report_id'])->with('user')->first();

        $notifiable = $report->user;
        $notifiable->locale = substr($notifiable->fcm, 0, 2);
        $notifiable->token = substr($notifiable->fcm, 2);

        dispatch(new SendFirebaseNotification(
            $notifiable->token,
            $notifiable->locale,
            $data['answer_en'],
            $data['answer_ar'],
            $notifiable
        ));

        return $this->generalResponse(null, null, 200);
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
    public function destroy(PostsReport $posts_report)
    {
        Files::deleteFile(public_path("Images/PostsReports/{$posts_report->image}"));
        $posts_report->delete();
        return $this->generalResponse(null, null);
    }
}
