<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Security;

class AdminLogout extends BaseController
{
    public function render()
    {
        $_SESSION = array();
        session_destroy();
        redirect_action(route('editora.action', '/'));
        return;
    }
}
