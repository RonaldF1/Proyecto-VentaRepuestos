<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'error' => [
                    'codigo' => 'no_encontrado',
                    'mensaje' => 'La ruta no existe.',
                    'detalles' => [],
                ],
            ], 404);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            return response()->json([
                'error' => [
                    'codigo' => 'no_encontrado',
                    'mensaje' => 'La ruta no existe.',
                    'detalles' => [],
                ],
            ], 404);
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if (!config('app.debug')) {
                return response()->json([
                    'error' => [
                        'codigo' => 'error_interno',
                        'mensaje' => 'Ocurrió un error interno.',
                        'detalles' => [],
                    ],
                ], 500);
            }
        });
    })->create();
