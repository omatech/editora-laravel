<?php

namespace Omatech\Editora\Admin\Accions;


use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\statictext;

use Omatech\Editora\Loader\Loader;

class AdminListClass extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();

        if(INST_PERM || $_SESSION['rol_id']==1 || $security->buscaAccessTotal($params) || $security->getAccess2($params)|| $security->getAccess('insertable',$params)) {
            $instances = new Instances;
            //$st = new statictext();
            $params=get_params_info();
            $params['p_mode']='V';
            $params['param1']=4;

            $p_mode = 'I';
            $title=EDITORA_NAME;


            //Cambiar por security -> db
            $conn = config("database.connections.mysql");
            $connection_params = array(
                'dbname' => $conn['database'],
                'user' => $conn['username'],
                'password' => (isset($conn['password']) ? $conn['password'] : ''),
                'host' => $conn['host'],
                'driver' => 'pdo_mysql',
                'charset' => 'utf8'
            );
            $conn_to = \Doctrine\DBAL\DriverManager::getConnection($connection_params);

            $loader = new Loader($conn_to);
            $classes = $loader->getAllClasses();

            $menu = $this->loadMenu($instances, $params);


            //$static_texts = $st->get_static_text_keys();

            //$languages = $st->get_static_text_languages();
/*
            if(isset($_GET['text_lang'])){
                $selected_language = $_GET['text_lang'];
                $static_texts = $st->get_static_text_lang($_GET['text_lang']);
            }else{
                $selected_language = 'ALL';
                $static_texts = $st->get_static_text_lang(current($languages));
            }
*/
        }


        $viewData = array_merge($menu, [
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
        ]);

        $viewData['classes'] = $classes;

        return response()->view('editora::pages.list_classes_export', $viewData);
    }

}
