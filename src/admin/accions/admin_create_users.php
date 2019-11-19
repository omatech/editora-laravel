<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\editoraModel;

class AdminCreateUsers extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();

        $p_mode = 'I';
        $instances = new Instances;
        $editora = new EditoraModel();

        $params=get_params_info();
        $params['p_mode']='V';
        $params['param1']=4;


        $title=EDITORA_NAME;

        $menu = $this->loadMenu($instances, $params);
        $messages = [];
        $user = [];

        $roles = $editora->get_roles();

        if (Session::get('user_type')=='O' && Session::get('rol_id')==1) {
            if (isset($_REQUEST['hiddencheck'])) {
                if ($_REQUEST['hiddencheck'] == 'create_user') {
                    $user_name = trim(addslashes($_REQUEST['username']));
                    $complete_name = trim(addslashes($_REQUEST['complete_name']));
                    $rol = trim(addslashes($_REQUEST['rol']));

                    if ($user_name!='' && $complete_name!='') {
                        if ($editora->exist_username(null, $user_name)) {
                            $messages['ko_user'] = 'ya existe este identificador de usuario';
                        } else {
                            $res = $editora->create_user($user_name, $complete_name, $rol);

                            if ($res) {
                                $messages['ok_user'] = 'datos guardados correctamente. Pass: '.$res;
                            } else {
                                $messages['ko_user'] = 'no se han podido guardar los datos';
                            }
                        }
                    } else {
                        $messages['ko_user'] = 'Todos los campos son obligatorios';
                    }
                }
            }

            $view ='editora::pages.create_user';
        } else {
            $instance['instance_info']=null;
            $title = getMessage('error_role_privileges');
            $view ='editora::pages.permission_denied';
        }

        $viewData = array_merge($menu, [
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
            'user' => $user,
            'roles' => $roles,
            'messages' => $messages
        ]);

        return response()->view($view, $viewData);
    }
}
