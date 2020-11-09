<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Templates\InstancesTemplate;

class AdminAddFavorite extends AuthController
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

        if (Session::get('rol_id') ==1 || $security->getAccess('browseable',$params)) {
            $params['p_mode']='V';
            $params['p_acces_type']='F';

            $this->instances->logAccess($params);
            $this->instances->instanceList($params);
        }

        return redirect(route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$params['param1'].'&p_inst_id='.$params['param2']));
    }
}
