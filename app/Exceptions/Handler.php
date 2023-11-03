<?php

namespace App\Exceptions;

use Exception;
use Psr\Log\LogLevel;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($request->expectsJson()){
            if($exception instanceof  ValidationException){
                return failure($exception->getMessage(),422,['errors'=>$exception->errors()]);
            }
            if($exception instanceof  BindingResolutionException){
                return failure($exception->getMessage(),500);
            }
            if($exception instanceof \ErrorException){
                return failure($exception->getMessage(),500);
            }
            if($exception instanceof NotFoundHttpException){
                return failure("URL not found",404, ['type'=>'NotFoundHttpException','code'=>$exception->getCode()]);
            }
            if($exception instanceof ModelNotFoundException ) {
                return failure($exception->getMessage(), 404);
            }
            if($exception instanceof AuthorizationException) {
                return failure("This action is unauthorized.",403);
            }
            if($exception instanceof MethodNotAllowedHttpException) {
                return failure($exception->getMessage(), 405);
            }
            if ($exception instanceof OAuthServerException) {
                return failure($exception->getMessage());
            }
            if ($exception instanceof HttpException) {
                return failure($exception->getMessage());
            }
        }
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? failure('Unauthenticated')
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
