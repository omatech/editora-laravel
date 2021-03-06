<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Templates\AttributesTemplate;
use Illuminate\Support\Facades\Session;

class AdminEditInstance extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params=get_params_info();
        $menu = [];

        if (Session::get('rol_id')==1 || $security->getAccess('editable', $params)) {
            $instances = new Instances;
            $at=new attributes();
            $at_t=new attributesTemplate();

            $params['p_mode']='V';
            $params['p_acces_type']='A';
            $p_mode = 'U';
            if ($params['param1'] == '' or $params['param1']<0) {
                $params['param1']=$params['param12'];
            }

            $instances->logAccess($params);
            $title=EDITORA_NAME." -> ".getMessage('info_view_object');

            $menu = $this->loadMenu($instances, $params);

            $javascript_attributes = $at_t->javascript_attributes;

            $instance = $at->getInstanceAttributes('U', $params);
            $view = 'editora::pages.instance';

        }else{
            $instance['instance_info']=null;
            $title = getMessage('error_role_privileges');
            $view ='editora::pages.permission_denied';
        }

        $viewData = array_merge($menu, [
            'instance' => $instance['instance_info'],
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
            'instances' => $instances,
            'javascript_attributes' => $javascript_attributes,
            'status_list' => isset($instance['status_list']) ? $instance['status_list'] : null
        ]);

        return response()->view($view, $viewData);
    }
}
