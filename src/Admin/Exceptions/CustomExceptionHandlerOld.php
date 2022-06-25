<?php

namespace Omatech\Editora\Admin\Exceptions;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Session\TokenMismatchException;

class CustomExceptionHandlerOld implements ExceptionHandlerContract
{
    public function __construct(Container $container, ExceptionHandlerContract $appExceptionHandler)
    {
        $this->container = $container;
        $this->appExceptionHandler = $appExceptionHandler;
    }

    public function render($request, \Exception $e)
    {

        if ($e instanceof TokenMismatchException) {
            return redirect()->route('editora.action', 'logout');
        }

        return $this->appExceptionHandler->render($request, $e);
    }

    public function report(\Exception $e)
    {
        $this->appExceptionHandler->report($e);
    }

    public function renderForConsole($output, \Exception $e)
    {
        $this->appExceptionHandler->renderForConsole($output, $e);
    }

    public function shouldReport(\Exception $e)
    {
        return $this->appExceptionHandler->shouldReport($e);
    }
}
