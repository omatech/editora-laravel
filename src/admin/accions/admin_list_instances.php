<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\editoraModel;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Templates\InstancesTemplate;
use Illuminate\Support\Facades\Session;

class AdminListInstances extends AuthController
{
    private $instances;
    private $instacesTemplate;

    public function __construct()
    {
        parent::__construct();

        $this->instances = new Instances;
        $this->instacesTemplate = new InstancesTemplate;
    }

    public function render()
    {
        $security = new Security;
        $params=get_params_info();

        $count = 0;
        $page = 1;
        if(Session::get('rol_id')==1 || $security->getAccess('browseable',$params)) {
            $editora = new editoraModel();

            $params = get_params_info();
            $params['p_mode'] = 'V';
            if ($params['param3']!=""){
                $page = $params['param3'];
            }
            $class_info = $editora->get_class_info($params['param1']);

            $instances = $this->instances->instanceList($params);
            $count = $this->instances->instanceList_count($params);

            $menu = $this->loadMenu($this->instances, $params);
        }
        $viewData = array_merge($menu, [
            'title' => EDITORA_NAME,
            'instances' => $instances,
            'class' => $class_info,
            'count' => $count,
            'page' => $page
        ]);
        return response()->view('editora::pages.list_instances', $viewData);
    }
}

