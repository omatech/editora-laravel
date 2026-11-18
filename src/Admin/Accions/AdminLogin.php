<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;

class AdminLogin extends BaseController
{
    public function render()
    {
        $request = request();
        if (!$request->isMethod('POST')) {
            return response('', 405, ['Allow' => 'POST']);
        }
        $input = $request->all();
        $language = $input['u_lang'] ?? null;
        if (!is_string($input['p_username'] ?? null) || !is_string($input['p_password'] ?? null)
            || ($language !== null && !is_string($language))) {
            return response('Invalid login data.', 422);
        }
        $security = new Security();
        if ($security->login($input['p_username'], $input['p_password'], $language)==1) {
            return response()->redirectTo(route('editora.action', 'get_main'));
        } else {
            Session::put('error_login', getMessage('info_error'));
            return response()->redirectTo(route('editora.action', '/'));
        }
    }
}
