<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\EditoraModel;
use Illuminate\Support\Facades\Session;

class AdminNewInstance extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();


        $instances = new Instances;
        $at=new attributes();
        $editora = new EditoraModel();

        $params['p_mode'] = 'V';

        $p_mode = 'I';
        $title=EDITORA_NAME." -> ".getMessage('info_view_object');

        $menu = $this->loadMenu($instances, $params);

        if (Session::get('rol_id')==1 || $security->getAccess('insertable', $params)) {
            $instance = $at->getInstanceAttributes($p_mode, $params);
            $instance['instance_info']['class_id'] = $params['param1'];
            $class_info = $editora->get_class_info($params['param1']);
            $view ='editora::pages.instance';
        } else {
            $instance['instance_info']=null;
            $title = getMessage('error_role_privileges');
            $view ='editora::pages.permission_denied';
        }

        $viewData = array_merge($menu, [
            'instance' => $instance['instance_info'],
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
            'class' => $class_info,
            'status_list' => isset($instance['status_list']) ? $instance['status_list'] : null
        ]);


        return response()->view($view, $viewData);
    }
}
