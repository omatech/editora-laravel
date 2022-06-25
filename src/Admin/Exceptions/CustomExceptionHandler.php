<?php

namespace Omatech\Editora\Admin\Exceptions;

use Illuminate\Contracts\Container\Container;
use Illuminate\Session\TokenMismatchException;
use Throwable;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

class CustomExceptionHandler implements ExceptionHandlerContract
{
    public function __construct(Container $container, ExceptionHandlerContract $appExceptionHandler)
    {
        $this->container = $container;
        $this->appExceptionHandler = $appExceptionHandler;
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            return redirect()->route('editora.action', 'logout');
        }

        return $this->appExceptionHandler->render($request, $e);
    }
    
    public function report(Throwable $e)
    {
        $this->appExceptionHandler->report($e);
    }

    public function renderForConsole($output, Throwable $e)
    {
        $this->appExceptionHandler->renderForConsole($output, $e);
    }

    public function shouldReport(Throwable $e)
    {
        return $this->appExceptionHandler->shouldReport($e);
    }
}
