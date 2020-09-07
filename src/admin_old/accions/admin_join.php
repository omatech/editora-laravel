<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Templates\AttributesTemplate;
use Omatech\Editora\Admin\Templates\LayoutTemplate;
use Omatech\Editora\Admin\Templates\InstancesTemplate;
use Illuminate\Support\Facades\Session;

class AdminJoin extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        $menu = [];

        if (Session::get('rol_id')==1 ||$security->getAccess('browseable', $params)) {
            $instances = new Instances;
            $at=new attributes();
            $ly_t=new LayoutTemplate();
            $at_t=new attributesTemplate();
            $in_t=new InstancesTemplate();

            $params['p_mode']= $p_mode = 'R';
            $params['p_acces_type']='A';
            $p_action = $params['p_action'];

            $params_relation['class_id'] = $params['param1'];
            $params_relation['inst_id'] = $params['param2'];
            $params_relation['relation_id'] = $params['param9'];
            $params_relation['parent_inst_id'] = $params['param11'];
            $params_relation['parent_class_id'] = $params['param10'];
            $params_relation['child_class_id'] = $params['param12'];


            if (isset($params['param12']) && $params['param12']!='') {
                $title=EDITORA_NAME." -> ".__('editora_lang::messages.info_joinsearch_object')." ".getClassName($params['param12'])." ".__('editora_lang::messages.info_word_joinwith')." ".getClassName($params['param10']);
            } else {
                $title=EDITORA_NAME." -> ".__('editora_lang::messages.info_joinsearch_object_lite')." ".getClassName($params['param12'])." ".__('editora_lang::messages.info_word_joinwith')." ".getClassName($params['param10']);
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
            'p_action' => $p_action,
            'parent' => $parent,
            'params_relation' => $params_relation
        ]);


        return response()->view('editora::pages.list_instances', $viewData);
    }
}
