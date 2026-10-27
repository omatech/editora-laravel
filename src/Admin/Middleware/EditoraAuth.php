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
            // Return the redirect instead of sending it: the action must not run without a session.
            setcookie("editorasession", 0, time()-3600, '/', request()->getHost());
            return response()->redirectTo(APP_BASE);
        }

        $cookieVal = env('APP_KEY').$_SERVER['HTTP_HOST'];
        $cookieHash= md5($cookieVal);;
        setcookie("editorasession", $cookieHash, time()+10800, '/', request()->getHost());

        return $next($request);
    }
}
