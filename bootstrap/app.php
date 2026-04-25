<?php

use App\Exceptions\ApiException;
use App\Http\Middleware\ApiAuthResponse;
use App\Http\Middleware\ValidAgent;
use App\Http\Middleware\ValidCustomerBootstrap;
use App\Http\Middleware\ValidLicense;
use App\Http\Middleware\ValidUser;
use App\Http\Middleware\ValidWidgetBootstrap;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',

    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'append-auth-response' => ApiAuthResponse::class,
            'valid-license' => ValidLicense::class,
            'valid-user' => ValidUser::class,
            'valid-agent' => ValidAgent::class,
            'valid-widget-bootstrap' => ValidWidgetBootstrap::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {


        $exception_details = fn(\Throwable $e) => [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'previous' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null,
        ];

        $exceptions->render(function (ApiException $e) use ($exception_details) {
            $data = [
                'success' => false,
                'error' => !empty($e->getErrorData()) ? $e->getErrorData() : [$e->getMessage()]
            ];

            if (config('app.debug')) {
                $data['exception'] = $exception_details($e);
            }
            return response()->json($data, $e->getCode());
        });




        $exceptions->render(function (AuthenticationException $e) {
            return response()->json([
                "authentication" => false
            ], 401);
        });

        $exceptions->render(function (ValidationException $e) {
            return response()->json([
                "success" => false,
                "error" => $e->errors(),
            ], 422);
        });

        $exceptions->renderable(function (\Throwable $e) use ($exception_details) {


            $data = [
                'success' => false,
                'error' => ["Something went wrong, please try again"],

            ];

            if (config('app.debug')) {
                $data['exception'] = $exception_details($e);
            }
            return response()->json($data, 400);
        });


    })->create();
