<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Templates\InstancesTemplate;
use Illuminate\Support\Facades\Session;

class AdminGetMain extends AuthController
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
        $params=get_params_info();
        $params['p_mode']='V';

        $title = EDITORA_NAME;
        $instances = $this->instances->instanceList($params);
        $menu = $this->loadMenu($this->instances, $params);

        $viewData = array_merge($menu, [
            'title' => $title,
            'instances' => $instances,
        ]);

        return response()->view('editora::pages.home', $viewData);
    }
}
