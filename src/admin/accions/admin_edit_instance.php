<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Templates\AttributesTemplate;

class AdminEditInstance extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params=get_params_info();

        if($_SESSION['rol_id']==1 || $security->getAccess('editable',$params)) {
            $instances = new Instances;
            $at=new attributes();
            $at_t=new attributesTemplate();

            $params['p_mode']='V';
            $params['p_acces_type']='A';
            $p_mode = 'U';
            if ($params['param1'] == '' or $params['param1']<0){
                $params['param1']=$params['param12'];
            }

            $instances->logAccess($params);
            $title=EDITORA_NAME." -> ".getMessage('info_view_object');

            $menu = $this->loadMenu($instances, $params);

            $javascript_attributes = $at_t->javascript_attributes;

            $instance = $at->getInstanceAttributes('U', $params);
        }

        $viewData = array_merge($menu, [
            'instance' => $instance['instance_info'],
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
            'instances' => $instances,
            'javascript_attributes' => $javascript_attributes
        ]);

        return response()->view('editora::pages.instance', $viewData);
    }
}
