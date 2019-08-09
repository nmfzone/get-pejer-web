<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $e = $this->prepareException($exception);

        if ($this->isApi($request) || $request->expectsJson()) {
            $statusCode = 500;
            $message = $e->getMessage();
            $additionalData = [];

            if ($e instanceof AuthenticationException) {
                $statusCode = 401;
            } elseif ($e instanceof ValidationException) {
                $statusCode = $e->status;
                $additionalData['errors'] = $e->errors();
            } elseif ($this->isHttpException($e)) {
                $statusCode = $e->getStatusCode();
            }

            $key = 'response.codes.' . $statusCode;
            $transMessage = trans($key);

            if (! config('app.debug') && ! $this->isHttpException($e) && $statusCode == 500) {
                $message = 'Something went wrong';
            } elseif ($statusCode == 500 && config('app.debug')) {
                $additionalData = $this->convertExceptionToArray($e);
            }

            return response()->json(array_merge([
                'message' => $transMessage !== $key ? $transMessage : $message,
            ], $additionalData), $statusCode);
        }

        return parent::render($request, $exception);
    }

    /**
     * Determine request is from API routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isApi($request)
    {
        return $request->is('api/*');
    }
}
