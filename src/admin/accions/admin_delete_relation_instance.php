<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Relations;

class AdminDeleteRelationInstance extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();

        if($_SESSION['rol_id']==1 || $security->getAccess('browseable',$params)) {
            $at=new attributes();
            $re=new relations();

            $re->deleteRelationInstance($params);
            $rel_id=$_REQUEST['p_rel_id'];
            $parent_inst_id=$_REQUEST['parent_inst_id'];

            $rows=$at->getRelatedInstances($rel_id, $parent_inst_id);

            $viewData['attribute'] = $rows;
            $viewData['instance']['id'] = $parent_inst_id;
            $viewData['attribute']['id'] = $rel_id;
            return view('editora::templates.relation_ajax_list', $viewData);

        }
       die();
    }

}

