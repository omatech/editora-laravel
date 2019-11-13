<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Illuminate\Support\Facades\Session;

class AdminViewGridImage extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params=get_params_info();
        if(Session::has('rol_id')) {
            $instances = new Instances;
            $at=new attributes();

            $params['p_mode']= $p_mode = 'V';
            $params['p_acces_type']='A';

            $instances->logAccess($params);
            $attribute = $at->getAttributeValues($params['param3'], $params['param2']);

        }

        $viewData = array_merge([
            'file' => $attribute[0]['text_val']
        ]);

        return response()->view('editora::pages.grid_image', $viewData);
    }
}
