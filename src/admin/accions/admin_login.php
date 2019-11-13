<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;

class AdminLogin extends BaseController
{
    public function render()
    {
        $security = new Security();
        if ($security->login($_REQUEST['p_username'], $_REQUEST['p_password'], $_REQUEST['u_lang'])==1) {
            return response()->redirectTo(route('editora.action', 'get_main'));
        } else {
            Session::put('error_login', getMessage('info_error'));
            return response()->redirectTo(route('editora.action', '/'));
        }
    }
}
