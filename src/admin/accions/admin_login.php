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
            if (isset($_SESSION['last_page']) && $_SESSION['last_page']!='') {
                redirect_action($_SESSION['last_page']);
            } else {
                redirect_action(route('editora.action', 'get_main'));
            }
        } else {
            $_SESSION['error_login'] = getMessage('info_error');
            redirect_action(route('editora.action', '/'));
            return;
        }
    }
}
