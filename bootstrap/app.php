<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

//new use for exceptions code 15 jan 2026
/*
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Support\Facades\Log;
*/
//use Throwable;---- am comentat si am comentat si codul din with exceptions altfel da eroare in front end

// end new use for exception code 15 jan 2026

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    //->withMiddleware(function (Middleware $middleware) {
        //
    //})
    ->withMiddleware(function (Middleware $middleware) {
       $middleware->statefulApi();
    })
   // ->withExceptions(function (Exceptions $exceptions) {
        //
    //}

->withExceptions(function ($exceptions) {

  /*  $exceptions->render(function (Throwable $e, $request) {

        // Only handle API / JSON requests
        if (! $request->expectsJson()) {
            return null;
        }

        // 422 - Validation errors (safe for production)
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Invalid data provided.',
                'errors'  => $e->errors(),
                'code'    => 'VALIDATION_ERROR',
            ], 422);
        }

        // 401 - Authentication required
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Authentication required.',
                'code'    => 'UNAUTHENTICATED',
            ], 401);
        }

        // 403 - Authorization failed
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'message' => 'You are not authorized to perform this action.',
                'code'    => 'FORBIDDEN',
            ], 403);
        }

        // 404 - Model not found
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'The requested resource was not found.',
                'code'    => 'NOT_FOUND',
            ], 404);
        }

        // HTTP exceptions (429, 405, etc.)
        if ($e instanceof HttpExceptionInterface) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Request error.',
                'code'    => 'HTTP_EXCEPTION',
            ], $e->getStatusCode());
        }

        // 🔥 Production: hide internal errors from users
        if (app()->isProduction()) {
            Log::error($e);

            return response()->json([
                'message' => 'An internal error occurred. Please try again later.',
                'code'    => 'SERVER_ERROR',
            ], 500);
        }

        // Development: let Laravel show full exception details
        return null;
    });
    */    
 }
   //end new exceptions code from 15 jan 2026
    
)->create();
