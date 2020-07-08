<?php

namespace Omatech\Editora\app\Http\Controllers;

use Auth;
use Illuminate\Routing\Controller as LaravelController;
use Omatech\Editora\app\Models\Security;
use Illuminate\Support\Facades\Session;

class AuthController extends LaravelController {
    


    public function authenticate()
    {
        if (Auth::attempt(['email' => $email, 'password' => $password]))
        {
            return redirect()->intended('dashboard');
        }
    }




    public function login() {

        $security = new Security();
        
        if ($security->testSession()) {
            return response()->redirectTo(route('editora.get_main'));
        }


        if(isset($_REQUEST['p_username']) && isset($_REQUEST['p_password'])){
            if($security->login($_REQUEST['p_username'], $_REQUEST['p_password'], $_REQUEST['u_lang'])){
                return response()->redirectTo(route('editora.get_main'));
            } else {
                // Session::put('error_login', getMessage('info_error'));
                // return response()->redirectTo(route('editora.action', '/'));
            }
        }
        global $array_langs;
        $array_langs = config('editora-admin.languages');
        global $lg;
        // $lg = getDefaultLanguage();
        $lg = $array_langs[0];
        $this->viewData['array_langs'] = $array_langs;
        $this->viewData['lg'] = $lg;
        return response()->view('editora::pages.login', $this->viewData);
    }

    public function logout()
    {
        $security = new Security();
        
        $security->endSession();

        return response()->redirectTo(route('editora.login'));
    }

    public function maintenance_mode(){

        if (!env('EDITORA_MAINTENANCE_MODE') || env('EDITORA_MAINTENANCE_MODE') != true) {
            return response()->redirectTo(route('editora.login'));
        }

        if (env('EDITORA_MAINTENANCE_MESSAGE')) {
            $message = env('EDITORA_MAINTENANCE_MESSAGE');
        } else {
            $message = 'En mantenimiento';
        }
        return response()->view('editora::pages.maintenance_mode');
        
    }
}
