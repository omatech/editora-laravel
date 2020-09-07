<?php

namespace Omatech\Editora\app\Http\Controllers;

use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


use Omatech\Editora\app\Middleware\EditoraAuth;
use Omatech\Editora\app\Middleware\EditoraLocale;
// use Omatech\Editora\Admin\Util\Urls;
use Omatech\Editora\app\Models\Layout;
use Omatech\Editora\app\Models\Security;
use Omatech\Editora\cofig\admin;

class Controller extends LaravelController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    private $action;

    public function __construct()
    {
        $this->middleware([EditoraAuth::class]);

        global $array_langs;
        $array_langs = config('editora-admin.languages');
        global $lg;
        // $lg = getDefaultLanguage();
        $lg = $array_langs[0];
    }

    public function loadMenu($instances)
    {
        // $lg = getDefaultLanguage();
        $lg = 'es';
        $ly = new Layout();
        $menu = $ly->get_topMenu($lg);
        $favorites = $instances->getFavorites();
        $last_accessed = $instances->getLastInstances();

        return [
            'menu' => $menu,
            'last_accessed' => $last_accessed,
            'favorites' => $favorites,
        ];
    }
}
