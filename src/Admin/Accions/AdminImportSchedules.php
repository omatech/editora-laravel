<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;

use Illuminate\Support\Facades\Session;

class AdminImportSchedules extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        $menu = [];

        if (INST_PERM || Session::get('rol_id')==1 || $security->buscaAccessTotal($params) || $security->getAccess2($params)|| $security->getAccess('insertable', $params)) {
            $instances = new Instances;

            $params=get_params_info();
            $params['p_mode']='V';
            $params['param1']=4;

            $p_mode = 'I';
            $title=EDITORA_NAME;

            $menu = $this->loadMenu($instances, $params);
        }

        $viewData = array_merge($menu, [
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
        ]);

        return response()->view('editora::pages.import_schedules', $viewData);
    }
}
