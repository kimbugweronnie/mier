<?php

namespace App\Exceptions;

use Exception;

class CustomExceptionHandler extends Exception
{
    //
     // ...
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
