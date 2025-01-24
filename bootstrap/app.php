<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function(Exception $ex){
            return failedApiResponse($ex->getMessage(), [], $ex->getTraceAsString(), 500);
        });

        $exceptions->render(function(HttpException $ex){
            return failedApiResponse($ex->getMessage(), $ex->getHeaders()['data'] ?? [], [], $ex->getStatusCode());
        });

        $exceptions->render(function(ValidationException $ex){
            return failedApiResponse($ex->getMessage(),[], $ex->errors(), 400);
        });

        $exceptions->render(function(Exception $ex){
            return failedApiResponse($ex->getMessage(), [], $ex->getTraceAsString(), 500);
        });
    })->create();
