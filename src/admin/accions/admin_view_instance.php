<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\attributes;

class AdminViewInstance extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params=get_params_info();
        $menu = [];

        if (Session::get('rol_id') == 1 || $security->getAccess('browseable', $params)) {
            $instances = new Instances;
            $at=new attributes();

            $params['p_mode']= $p_mode = 'V';
            $params['p_acces_type']='A';

            $title=EDITORA_NAME." -> ".getMessage('info_view_object');

            $instances->logAccess($params);
            $menu = $this->loadMenu($instances, $params);
            $instance = $at->getInstanceAttributes($p_mode, $params);

            $parents = $instances->getDistinctParents($params);
        }

        $viewData = array_merge($menu, [
            'instance' => $instance['instance_info'],
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
            'parents' => $parents,
        ]);

        return response()->view('editora::pages.instance', $viewData);
    }
}
