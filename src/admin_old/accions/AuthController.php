<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Middleware\EditoraAuth;
use Omatech\Editora\Admin\Middleware\EditoraLocale;
use Omatech\Editora\Admin\Models\layout;
use Illuminate\Support\Facades\Session;

class AuthController extends BaseController
{
    public function __construct()
    {
        $this->middleware([
            EditoraAuth::class
        ]);
    }

    public function loadMenu($in, $params)
    {
        $ly=new layout();
        $lg = getDefaultLanguage();


        $menu = $ly->get_topMenu($lg);
        $favorites = $in->getFavorites();
        $last_accessed = $in->getLastInstances();

        return [
            'menu' => $menu,
            'last_accessed' => $last_accessed,
            'favorites' => $favorites,
        ];
    }
}
