<?php

namespace Omatech\Editora\Admin\Middleware;

use Closure;
use Omatech\Editora\Admin\Models\Security;

class EditoraAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $security = new Security();

        if($security->testSession() == 0) {
            $security->endSession();
            setcookie("editorasession", 1, time()-3600, '/', request()->getHost());
        }else{
            setcookie("editorasession", 1, time()+10800, '/', request()->getHost());
        }

        return $next($request);
    }
}
