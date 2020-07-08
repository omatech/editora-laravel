<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Templates\InstancesTemplate;
use Illuminate\Support\Facades\Session;

class AdminDeleteInstance2 extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();

        if (Session::get('rol_id')==1 || $security->getAccess('deleteable', $params)) {
            $instances = new Instances;
            $in_t=new instancesTemplate();



            $params['p_mode']='V';
            $message=html_message_ok($instances->deleteInstance($params));
            $params['param1']='';

            $inst_count = $instances->instanceList_count($params);
            $instances = $instances->instanceList($params);
            $body=$in_t->instancesList_view($instances, $inst_count, $params);

            $_REQUEST['view']='container';
        }

        return redirect(route('editora.action', 'get_main'));
    }
}
