<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\EditoraModel;

class AdminAutocomplete extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        $ret = '';

        if(Session::has('rol_id')) {
            $editora = new EditoraModel();

            $params['param1']=$params['param12'];
            $child_class_id=$_REQUEST['p_child_class_id'];
            $term=$_REQUEST['term'];

            $ret = $editora->get_autocomplete(Session::get('u_lang'), $child_class_id, $term);
        }
        return $ret;
    }

}
