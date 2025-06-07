<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\CustomException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware
        ->alias(['auth_api' => Auth::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function(\Throwable $e, $request){

            $exceptionCode = $e->getCode() ?: 500;
            $response = [
                'code'=> 'unexpected',
                'success'=> false,
                'message' => 'Unexpected error. Please try again later.'
            ];


            switch(true){
                case $e instanceof ValidationException:

                    $response['message'] = 'The provided data is invalid.';
                    $response['errors'] = $e->errors();
                    $exceptionCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                    break;

                case $e instanceof AuthenticationException:
                    $response['message'] = 'You are not authenticated.';
                    $exceptionCode = Response::HTTP_UNAUTHORIZED;
                    break;

                case $e instanceof AuthorizationException:
                    $response['message'] = 'You are not authorized to perform this action.';
                    $exceptionCode = Response::HTTP_FORBIDDEN;
                    break;

                case $e instanceof ModelNotFoundException:
                    $response['message'] = 'The requested resource was not found.';
                    $exceptionCode = Response::HTTP_NOT_FOUND;
                    break;

                case $e instanceof HttpExceptionInterface:
                    $response['message'] = $e->getMessage() ?: 'The route does not exist or the HTTP method is not allowed for this route.';
                    $exceptionCode = $e->getStatusCode();
                    break;
                case $e instanceof CustomException;
                    $response['message'] = $e->getMessage();
                    break;
                default:
                    break;
            }

            return response()->json($response, $exceptionCode);

        });

    })->create();
