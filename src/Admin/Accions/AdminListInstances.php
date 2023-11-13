<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Pagination\LengthAwarePaginator;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\EditoraModel;
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
        $p_mode = '';
        $instances = [];
        $menu = [];
        $class_info = null;

        if (Session::get('rol_id')==1 || $security->getAccess('browseable', $params)) {
            $editora = new EditoraModel();

            $params = get_params_info();
            $params['p_mode'] = $p_mode = 'V';
            if ($params['param3']!="") {
                $page = $params['param3'];
            }

            if ($params['param1'] != "") {
                $class_info = $editora->get_class_info($params['param1']);
                //dd($class_info);
            }

            $instances = $this->instances->instanceList($params);
            $count = $this->instances->instanceList_count($params);

            $menu = $this->loadMenu($this->instances, $params);
        }
        $viewData = array_merge($menu, [
            'title' => EDITORA_NAME,
            'p_mode' => $p_mode,
            'instances' => $instances,
            'class' => $class_info,
            'count' => $count,
            'page' => $page,
            'paginator' => new LengthAwarePaginator($instances, $count, 40, $page, [
                'pageName' => 'p_pagina',
                    'path' => '/admin/list_instances',
                    'query' => ['p_class_id' => $class_info['id']],
                ]),
            ]);

        return response()->view('editora::pages.list_instances', $viewData);
    }
}
