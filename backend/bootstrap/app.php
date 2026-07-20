<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /**
         * Always return JSON for API routes.
         */
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        /**
         * 404 Not Found
         */
        $exceptions->render(function (
            ModelNotFoundException|NotFoundHttpException $e,
            Request $request
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Resource not found.',
            ], Response::HTTP_NOT_FOUND);
        });

        /**
         * 403 Forbidden
         */
        $exceptions->render(function (
            AuthorizationException $e,
            Request $request
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden.',
            ], Response::HTTP_FORBIDDEN);
        });

        /**
         * Unhandled Exception (500)
         */
        $exceptions->render(function (
            Throwable $e,
            Request $request
        ) {

            return response()->json([
                'status' => 'error',
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Internal server error.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        });

    })->create();
