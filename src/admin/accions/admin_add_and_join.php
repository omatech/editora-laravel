<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\attributes;

class AdminAddAndJoin extends AuthController
{
    public function render()
    {
        $security = new Security;

        $params = get_params_info();
        $params['param1'] = $params['param12'];
        $params['param2'] = null;

        $instances = new Instances;
        $at=new attributes();

        $params['p_mode'] = 'V';
        $p_mode = 'I';
        $title=EDITORA_NAME." -> ".getMessage('info_view_object');

        $params['p_inst_id']= $_REQUEST['p_inst_id'];
        $menu = $this->loadMenu($instances, $params);


        if (Session::get('rol_id') == 1 || $security->getAccess('insertable',$params)) {
            $instance = $at->getInstanceAttributes($p_mode, $params);
            $instance['instance_info']['class_id'] = $params['param1'];
            $instance['instance_info']['form_relation'] = $params;
            $view ='editora::pages.instance';
        }else{
            $instance['instance_info']=null;
            $title = getMessage('error_role_privileges');
            $view ='editora::pages.permission_denied';
        }

        $viewData = array_merge($menu, [
            'instance' => $instance['instance_info'],
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title
        ]);

        return response()->view($view, $viewData);
    }
}
