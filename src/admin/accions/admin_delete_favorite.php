<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Illuminate\Support\Facades\Session;

class AdminDeleteFavorite extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params=get_params_info();
        if(Session::get('rol_id')==1 || $security->getAccess('browseable',$params)) {
            $instances = new Instances;

            $params['p_mode']='V';
            $params['p_acces_type']='F';

            $instances->deleteLogAccess($params);

            $menu = $this->loadMenu($instances, $params);
        }

        $viewData = array_merge($menu);

        return response()->view('editora::templates.favorites_menu', $viewData);
    }
}
