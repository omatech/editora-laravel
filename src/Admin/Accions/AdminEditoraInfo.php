<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\EditoraInfo;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Instances;

use Illuminate\Support\Facades\Session;

class AdminEditoraInfo extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        $menu = $users = $roles = $roles_classes = [];

        if (INST_PERM || Session::get('rol_id')==1 || $security->buscaAccessTotal($params) ) {
            $instances = new Instances;
            
            $params=get_params_info();
            $params['p_mode']='V';
            $params['param1']=4;

            $p_mode = 'I';
            $title=EDITORA_NAME;

            $editoraInfo = new EditoraInfo;
            $users = $editoraInfo->getUsersInfo();
            $roles = $editoraInfo->getRoles();
            $roles_classes = $editoraInfo->getClassesRoles();

            $menu = $this->loadMenu($instances, $params);


        }


        $viewData = array_merge($menu, [
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
        ]);

        $viewData['users'] = $users;
        $viewData['roles'] = $roles;
        $viewData['roles_classes'] = $roles_classes;

        return response()->view('editora::pages.editora_info', $viewData);
    }
}
