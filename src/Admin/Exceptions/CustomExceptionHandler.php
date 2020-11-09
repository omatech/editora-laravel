<?php

namespace Omatech\Editora\Admin\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;

class CustomExceptionHandler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Exception $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            return redirect()->route('editora.action', 'logout');
        }

        return parent::render($request, $exception);
    }
}