<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\EditoraModel;

class AdminConfigure extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        $menu = [];
        
        if (Session::has('rol_id')) {
            $instances = new Instances;
            $editora = new EditoraModel();

            $params=get_params_info();
            $params['p_mode']='V';
            $params['param1']=4;

            $p_mode = 'I';
            $title=EDITORA_NAME;

            $user = $editora->get_user_info(Session::get('user_id'));
            $menu = $this->loadMenu($instances, $params);
            $messages = [];
            if (isset($_REQUEST['hiddencheck'])) {
                if ($_REQUEST['hiddencheck'] == 'change_password') {
                    if ($security->check_change_password(Session::get('user_id'), $_REQUEST['old_password'])) {
                        if ($_REQUEST['password'] != $_REQUEST['repeat_password']) {
                            $messages['ko_pass'] = 'Las contraseñas deben ser iguales';
                        } else {
                            if ($security->change_password(Session::get('user_id'), $_REQUEST['password'])) {
                                $messages['ok_pass'] = 'La contraseña se ha cambiado correctamente';
                            } else {
                                $messages['ko_pass'] = 'No se ha podido cambiar la contraseña, vuelve a intentarlo o ponte en contacto con el administrador';
                            }
                        }
                    } else {
                        $messages['ko_pass'] = 'La contraseña actual no es correcta';
                    }
                } elseif ($_REQUEST['hiddencheck'] == 'change_user') {
                    $user_name = trim(addslashes($_REQUEST['username']));
                    $complete_name = trim(addslashes($_REQUEST['complete_name']));

                    if ($user_name!='' && $complete_name!='') {
                        if ($editora->exist_username(Session::get('user_id'), $user_name)) {
                            $messages['ko_user'] = 'ya existe este identificador de usuario';
                        } else {
                            $res = $editora->update_user_info(Session::get('user_id'), $user_name, $complete_name);

                            if ($res) {
                                $messages['ok_user'] = 'datos guardados correctamente';
                                Session::put('user_nom', $complete_name);
                            } else {
                                $messages['ko_user'] = 'no se han podido guardar los datos';
                            }
                        }
                    } else {
                        $messages['ko_user'] = 'Todos los campos son obligatorios';
                    }

                    $user = $editora->get_user_info(Session::get('user_id'));
                }
            }
        }

        $viewData = array_merge($menu, [
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
            'user' => $user,
            'messages' => $messages
        ]);

        return response()->view('editora::pages.change_password', $viewData);
    }
}
