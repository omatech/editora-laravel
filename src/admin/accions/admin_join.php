<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Templates\AttributesTemplate;
use Omatech\Editora\Admin\Templates\LayoutTemplate;
use Omatech\Editora\Admin\Templates\InstancesTemplate;

class AdminJoin extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        if($_SESSION['rol_id']==1 ||$security->getAccess('browseable',$params)) {
            $instances = new Instances;
            $at=new attributes();
            $ly_t=new LayoutTemplate();
            $at_t=new attributesTemplate();
            $in_t=new InstancesTemplate();

            $params['p_mode']= $p_mode = 'R';
            $params['p_acces_type']='A';

            if (isset($params['param12']) && $params['param12']!=''){
            	$title=EDITORA_NAME." -> ".getMessage('info_joinsearch_object')." ".getClassName($params['param12'])." ".getMessage('info_word_joinwith')." ".getClassName($params['param10']);
            }else{
            	$title=EDITORA_NAME." -> ".getMessage('info_joinsearch_object_lite')." ".getClassName($params['param12'])." ".getMessage('info_word_joinwith')." ".getClassName($params['param10']);
            }
            $instances->logAccess($params);
            $menu = $this->loadMenu($instances, $params);

            $instances_list = $instances->instanceList($params);

            $parents = $instances->getParents($params);

            $parent['rel_id'] = $params['param9'];
            $parent['inst_id'] = $params['param11'];
            $parent['class_id'] = $params['param10'];

        }

        $viewData = array_merge($menu, [
            'title' => $title,
            'instances' => $instances_list,
            'parents' => $parents,
            'p_mode' => $p_mode,
            'parent' => $parent
        ]);


        return response()->view('editora::pages.list_instances', $viewData);
    }

}
