<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        

        
        
    }

    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            // Custom handling for ModelNotFoundException
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['message' => 'Item Not Found'], 404);
            }

            if ($exception instanceof ValidationException) {
                $errors = $exception->validator->errors()->getMessages();
                return response()->errorResponse($errors, 422);
            }

            if ($exception instanceof AuthenticationException) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            
            // Custom handling for NotFoundHttpException
            if ($exception instanceof NotFoundHttpException) {
                return response()->json(['message' => 'The requested link does not exist'], 404);
            }
            
        }

        // Default handling for other exceptions
        return parent::render($request, $exception);
    }
}
