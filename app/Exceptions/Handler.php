<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;

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
        $this->renderable(function (Exception $e, $request) {
            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return $this->errorResponse(['error' => 'هیچ نتیجه ای برای نمایش پیدا نشد'], 404);
                }
            }
        });

        $this->renderable(function (Exception $e, $request) {
            if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
                throw new AccessDeniedException();
            }
        });

        $this->renderable(function (Exception $e, $request) {
            if ($e instanceof ValidationException) {
                if ($request->wantsJson() || $request->is('api/*')) {
                    return $this->errorResponse($e->errors(), 422);
                }
            }
        });

    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->wantsJson() || $request->is('api/*')
            ? $this->errorResponse(['error' => 'لطفا ابتدا وارد سایت شوید'], 401)
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
