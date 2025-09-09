<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait Responses {

    public function generalResponse(mixed $data, mixed $message = null, int $status = 200) : JsonResponse {
        if(request()->user()) {
            app()->setLocale('ar');
        }

        return response()->json([
            'message' => is_null($message) ? null : __('responses.' . $message),
            'data' => $data,
        ], $status);
    }
}
