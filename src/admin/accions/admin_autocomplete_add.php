<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\relations;

class AdminAutocompleteAdd extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();

        if(isset($_SESSION['rol_id'])) {
            $at = new attributes();
            $r = new relations();

            $child_inst_id = $_REQUEST['p_child_inst_id'];
            $parent_inst_id = $_REQUEST['p_parent_inst_id'];
            $rel_id = $_REQUEST['p_rel_id'];
            $tab_id = $params['p_tab'];

            $param_arr = array();
            $param_arr['param9'] = $rel_id;
            $param_arr['param11'] = $parent_inst_id;
            $param_arr['param13'] = $child_inst_id;
            $param_arr['param14'] = $tab_id;

            $r->createRelation($param_arr);
            $rows = $at->getRelatedInstances($rel_id, $parent_inst_id);
            $viewData['attribute'] = $rows;
            $viewData['instance']['id'] = $parent_inst_id;
            $viewData['attribute']['id'] = $rel_id;
            return view('editora::templates.relation_ajax_list', $viewData);
        }
        die();
    }

}
