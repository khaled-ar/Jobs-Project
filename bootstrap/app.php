<?php

use App\Services\GoogleTranslateService;
use Illuminate\Foundation\{
    Application,
    Configuration\Exceptions,
    Configuration\Middleware
};

use Illuminate\Database\{
    QueryException,
    UniqueConstraintViolationException,

};

use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Register middleware aliases
        $currentAliases = $middleware->getMiddlewareAliases();
        $middleware->alias(array_merge($currentAliases, [
            'lang' => \App\Http\Middleware\Lang::class,
            'admin' => \App\Http\Middleware\Admin::class,
        ]));

    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (Exception|Throwable $e, Request $request) {
            $locale = app()->getLocale();
            if($locale != 'en') {
                $service = new GoogleTranslateService();
                $en_responses = include base_path('/lang/en/responses.php');
            }


            if ($e instanceof ThrottleRequestsException) {
                return response()->json([
                    'message' => isset($service) ? $service->translate($en_responses['error_429'], $locale, 'en') :  __('responses.error_429'),
                ], 429);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => isset($service) ? $service->translate($en_responses['error_404'], $locale, 'en') :  __('responses.error_404'),
                ], 404);
            }

            if ($e instanceof AccessDeniedHttpException) {
                return response()->json([
                    'message' => isset($service) ? $service->translate($en_responses['error_403'], $locale, 'en') :  __('responses.error_403'),
                ], 403);
            }

            if ($e instanceof UniqueConstraintViolationException) {
                return response()->json([
                    'message' => isset($service) ? $service->translate($en_responses['error_unique'], $locale, 'en') :  __('responses.error_unique'),
                ], 400);
            }

            if ($e instanceof QueryException) {
                return response()->json([
                    'message' => isset($service) ? $service->translate($en_responses['error_500'], $locale, 'en') :  __('responses.error_500'),
                ], 500);
            }

            if ($e instanceof ValidationException) {
                $res_errors = collect($e->errors())
                    ->flatten()
                    ->values()
                    ->all();

                if(isset($service)) {
                    foreach($res_errors as $i => $res_error) {
                        $res_errors[$i] = $service->translate($res_error, $locale);
                    }
                }

                return response()->json([
                    'errors' => $res_errors,
                ], 422);
            }
        });
    })->create();
