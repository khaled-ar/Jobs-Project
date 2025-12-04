<?php

namespace App\Traits;

use App\Services\GoogleTranslateService;
use Illuminate\Http\JsonResponse;

trait Responses {

    public function generalResponse(mixed $data, mixed $message = null, int $status = 200) : JsonResponse {

        $locale = app()->getLocale();
        $responses = include base_path('/lang/en/responses.php');
        return response()->json([
            'message' => is_null($message) ? null : __('responses.' . $message),
            // 'message' => is_null($message) ? null : ($locale == 'en' ? $responses[$message] : (new GoogleTranslateService())->translate($responses[$message], $locale, 'en')),
            'data' => $data,
        ], $status);
    }
}
