<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Relations;
use Illuminate\Support\Facades\Session;

class AdminJoinAll extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();

        if (Session::get('rol_id') == 1 || $security->getAccess('browseable', $params)) {
            $instances = new Instances;
            $re = new relations();
            $params['p_mode'] = 'R';
            $params['p_acces_type'] = 'A';
            $parent_class_id = $params['param1'] = $params['param10'];
            $parent_id = $params['param2'] = $params['param11'];
            if (isset($_REQUEST['rel_chb']) && $_REQUEST['rel_chb']) {
                $array = $_REQUEST['rel_chb'];
                foreach ($array as $value) {
                    $params['param13'] = $value;
                    $re->createRelation($params);
                    $instances->logAccess($params);
                }
            }
        }
        return redirect(route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$parent_class_id.'&p_inst_id='.$parent_id));
    }
}
