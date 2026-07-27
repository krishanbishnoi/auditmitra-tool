<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
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
    }



    // for custom error
//     public function render($request, Throwable $exception)
// {
//     // You can add specific logic for different exceptions here
//     // For all errors, return the custom error view with a 500 status code

//     return response()->view('custom-error', [], 500);

//     // Or if you want to fallback to default behavior for some exceptions, you can do this:
//     // return parent::render($request, $exception);
// }
} 