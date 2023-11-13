<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Pagination\LengthAwarePaginator;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Templates\InstancesTemplate;

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
        $params['p_mode'] = $p_mode = 'V';
        $page = 1;

        if ($params['param3']!="") {
            $page = $params['param3'];
        }

        $title = EDITORA_NAME;
        $instances = $this->instances->instanceList($params);

        $menu = $this->loadMenu($this->instances, $params);

        $viewData = array_merge($menu, compact('p_mode', 'title', 'instances', 'page'));

        return response()->view('editora::pages.home', $viewData);
    }
}
