<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Relations;
use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Templates\LayoutTemplate;
use Omatech\Editora\Admin\Templates\AttributesTemplate;

class AdminCloneInstance extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();

        if ( Session::get('rol_id')==1 ||  $security->getAccess('insertable', $params)) {
            $instances = new Instances;
            $at=new attributes();
            $ly_t= new LayoutTemplate();
            $at_t=new attributesTemplate();
            $re= new relations();

            $params['p_mode']='V';
            $params['p_acces_type']='F';


            $ret=$instances->cloneInstance($params['param2']);

            $menu = $this->loadMenu($instances, $params);
            if ($ret>0) {
                $message=html_message_ok("Clone OK");
                $params['param2']=$ret;
            } else {
                $message=html_message_warning("Unexpected error: ".$ret);
            }

            return redirect(route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$params['param1'].'&p_inst_id='.$params['param2']));
        }
    }
}
