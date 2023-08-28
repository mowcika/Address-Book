<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        \Illuminate\Auth\Access\AuthorizationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token Not authenticated'
                ], 401);
            }
        });
        $this->renderable(function (\Illuminate\Auth\AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access Denied!'
                ], 401);
            }
        });
    }

    public function render($request, Throwable $exception)
    {
        //Useful since some methods cannot be accessed in certain URL extensions
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'status' => false,
                'message' => 'Access Denied!'
            ], 401);
        }

        return parent::render($request, $exception);
    }
}
