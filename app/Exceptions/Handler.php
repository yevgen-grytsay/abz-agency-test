<?php

namespace App\Exceptions;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
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


        $this->renderable(function (NotFoundHttpException $e, Request $request) {

            if ($request->is('api/*') && $e->getPrevious() instanceof ModelNotFoundException) {
                /** @var ModelNotFoundException $modelNotFoundException */
                $modelNotFoundException = $e->getPrevious();

                $message = match(true) {
                    $modelNotFoundException->getModel() === User::class => 'The user with the requested identifier does not exist',
                    default => sprintf('%s not found.', $modelNotFoundException->getModel()),
                };

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'fails' => [
                        'id' => [
                            'User not found',
                        ],
                    ],
                ], 404);
            }

        });

        $this->renderable(function (ValidationException $e, Request $request) {

            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'fails' => $e->errors(),
                ], 422);
            }

        });
    }
}
