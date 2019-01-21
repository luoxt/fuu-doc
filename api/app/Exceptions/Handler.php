<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // not found 404page -- zicai 2017-10-18 10:21:56
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException)
        {
            return reJson(false, '404', 'bad request', [], 404);
        }
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException)
        {
            return reJson(false, '403', 'bad method', [], 403);
        }

        //API异常处理 luoxt 2017-08-23
        if($e instanceof ApiException) {
            $excode = $e->getCode();
            $exmessage = $e->getMessage();

            return reJson(false, $excode, $exmessage);

        }

        return parent::render($request, $e);
    }
}
