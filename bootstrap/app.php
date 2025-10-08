<?php

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

            // return response()->json([
            //     get_class($e)
            // ]);

            if ($e instanceof ThrottleRequestsException) {
                return response()->json([
                    'message' => __('responses.error_429'),
                ], 429);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => __('responses.error_404'),
                ], 404);
            }

            if ($e instanceof AccessDeniedHttpException) {
                return response()->json([
                    'message' => __('responses.error_403'),
                ], 403);
            }

            if ($e instanceof UniqueConstraintViolationException) {
                return response()->json([
                    'message' => __('responses.error_unique'),
                ], 400);
            }

            // if ($e instanceof QueryException) {
            //     return response()->json([
            //         'message' => __('responses.error_500'),
            //     ], 500);
            // }

            if ($e instanceof ValidationException) {
                $res_errors = collect($e->errors())
                    ->flatten()
                    ->values()
                    ->all();

                return response()->json([
                    'errors' => $res_errors,
                ], 422);
            }
        });
    })->create();
