<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;

class AdminLogout extends BaseController
{
    public function render()
    {
        Session::remove('user_id');
        Session::remove('user_nom');
        Session::remove('rol_id');
        Session::remove('rol_nom');
        Session::remove('user_type');
        Session::remove('user_language');
        Session::remove('u_language');
        Session::remove('u_lang');
        Session::remove('last_page');
        Session::remove('error_login');
        Session::remove('classes_cache');

        return response()->redirectTo(route('editora.action', '/'));
    }
}
