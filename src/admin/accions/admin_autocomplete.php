<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\editoraModel;

class AdminAutocomplete extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        $ret = '';
        if(isset($_SESSION['rol_id'])) {
            $editora = new editoraModel();

            $params['param1']=$params['param12'];
            $child_class_id=$_REQUEST['p_child_class_id'];
            $term=$_REQUEST['term'];

            $ret = $editora->get_autocomplete($_SESSION['u_lang'],$child_class_id, $term);
        }
        return $ret;
    }

}
