<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\editoraModel;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Templates\InstancesTemplate;


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

        if($_SESSION['rol_id']==1 || $security->getAccess('browseable',$params)) {
            $editora = new editoraModel();

            $params['p_mode']='V';

            $instances = $this->instances->instanceList($params);

            $menu = $this->loadMenu($this->instances, $params);
        }

        $viewData = array_merge($menu, [
            'title' => EDITORA_NAME,
            'instances' => $instances,
            'term' => $params['param4']
        ]);
        return response()->view('editora::pages.search_instances', $viewData);
    }

}
