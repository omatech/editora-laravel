<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Templates\InstancesTemplate;
use Illuminate\Support\Facades\Session;

class AdminDeleteInstance extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        $menu = [];

        if (Session::get('rol_id')==1 || $security->getAccess('deleteable', $params)) {
            $instances = new Instances;

            $params['p_mode']='V';

            $menu = $this->loadMenu($instances, $params);

            $title=EDITORA_NAME." -> ".getMessage('info_delete_object')." ".getClassName($_REQUEST['p_class_id']);
            $related_instances = $instances->checkDeleteInstance($params);
        }

        $viewData = array_merge($menu, [
            'title' => $title,
            'instances' => $instances,
            'related_instances' => $related_instances,
            'params' => $params
        ]);

        return response()->view('editora::pages.delete_instance', $viewData);
    }
}
