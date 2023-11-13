<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Pagination\LengthAwarePaginator;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\EditoraModel;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Templates\InstancesTemplate;
use Illuminate\Support\Facades\Session;

class AdminSearch extends AuthController
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
        $params = get_params_info();
        $menu = [];

        if (Session::get('rol_id')==1 || Session::get('rol_id')==2 || $security->getAccess('browseable', $params)) {
            $editora = new EditoraModel();
            $params['p_mode']='V';

            $instances = $this->instances->instanceList($params);
            $count = $this->instances->instanceList_count($params);
            $menu = $this->loadMenu($this->instances, $params);
        }
        $page = $params['param3'];

        $searchTerm = $params['param4'];
        $viewData = array_merge($menu, [
            'title' => EDITORA_NAME,
            'instances' => $instances,
            'term' => $searchTerm,
            'status' => $params['param8'],
            'class_id' => $params['param1'],
            'paginator' => new LengthAwarePaginator($instances, $count, 40, $page,
                ['pageName' => 'p_pagina', 'path' => '/admin/search', 'query' => ['p_search_query' => $searchTerm]]),
        ]);
        return response()->view('editora::pages.search_instances', $viewData);
    }
}
