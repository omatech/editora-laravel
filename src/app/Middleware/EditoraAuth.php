<?php

namespace Omatech\Editora\app\Middleware;


use Closure;
use Omatech\Editora\app\Models\Security;

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
        
        
        if (env('EDITORA_MAINTENANCE_MODE') && env('EDITORA_MAINTENANCE_MODE') === true) {
            return response()->redirectTo(route('editora.maintenance_mode'));
        }
        
        
        
        $security = new Security();
        
        if(!$security->testSession()) {
            $security->endSession();
            setcookie("editorasession", 1, time()-3600, '/', request()->getHost());
            return response()->redirectTo(route('editora.login'));
        }else{
            setcookie("editorasession", 1, time()+10800, '/', request()->getHost());
        }
        
        return $next($request);
    }
}
