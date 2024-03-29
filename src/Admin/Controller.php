<?php

namespace Omatech\Editora\Admin;

use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Omatech\Editora\Admin\Util\Urls;

class Controller extends LaravelController
{
    private $action;

    public function __construct()
    {
        if (env('EDITORA_MAINTENANCE_MODE') && env('EDITORA_MAINTENANCE_MODE') === true) {
            if (env('EDITORA_MAINTENANCE_MESSAGE')) {
                die(env('EDITORA_MAINTENANCE_MESSAGE'));
            } else {
                die('En mantenimiento');
            }
        }

        $urls = new Urls();
        $_SERVER['DOCUMENT_ROOT'] = __DIR__;
        require_once($_SERVER['DOCUMENT_ROOT'].'/conf/ompinfo.php');
        if (file_exists(DIR_APLI_ADMIN.'/Util/ips.php')) {
            require_once(DIR_APLI_ADMIN.'/Util/ips.php');
            $ip = get_real_ip();
            if (!allowed_ip($ip)) {
                header('HTTP/1.0 403 Forbidden');
                echo "<!-- $ip -->";
                die();
            }
        }
        require_once(DIR_APLI_ADMIN.'Util/omputils.php');
        require_once(DIR_APLI_ADMIN.'Util/utils.php');
        require_once(DIR_APLI_ADMIN.'Util/validator.php');
        require_once(DIR_APLI_ADMIN.'Util/parameters.php');
        require_once(DIR_APLI_ADMIN.'Util/BcryptHasher.php');

        $_REQUEST['header'] = 'header';
        $_REQUEST['footer'] = 'footer';
        $googlemaps = false;
        $pag_num = null;
        $urls->extract_url_info();

        require_once(DIR_APLI_ADMIN.'Models/Security.php');
        require_once(DIR_APLI_ADMIN.'Util/redirect.php');
        require_once(DIR_APLI_ADMIN.'Util/message_utils.php');
        require_once(DIR_APLI_ADMIN.'Models/EditoraModel.php');
        require_once(DIR_APLI_ADMIN.'Models/attributes.php');
        require_once(DIR_APLI_ADMIN.'Models/Instances.php');
        require_once(DIR_APLI_ADMIN.'Models/layout.php');
        require_once(DIR_APLI_ADMIN.'Models/Relations.php');
        require_once(DIR_APLI_ADMIN.'Models/statictext.php');
        require_once(DIR_APLI_ADMIN.'Templates/AttributesTemplate.php');
        require_once(DIR_APLI_ADMIN.'Templates/InstancesTemplate.php');
        require_once(DIR_APLI_ADMIN.'Templates/LayoutTemplate.php');

        if (file_exists(DIR_APLI_ADMIN.'/extras/Models')) {
            $directorio = opendir(DIR_APLI_ADMIN.'/extras/Models');
            while ($archivo = readdir($directorio)) {
                $exploded = explode(".", $archivo);
                $extension = end($exploded);
                if ($extension == 'php') {
                    require_once(DIR_APLI_ADMIN.'/extras/Models/'.$archivo);
                }
            }
        }

        $accion_name = 'admin_'.$_REQUEST['action'].'.php';
        $accion_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $accion_name)));
        $action = $_REQUEST['action'];

        if (Session::has('error_login') && Session::get('error_login') != '') {
            Session::put('error_login', Session::get('error_login'));
        } else {
            Session::remove('error_login');
        }
        $mini_actions = array(
            'view_instance', 'edit_instance', 'join', 'join_all', 'delete_instance', 'edit_instance2',
            'new_instance', 'new_instance2', 'add_favorite', 'delete_favorite', 'order_up_top', 'order_down_bottom',
            'order_up', 'order_down', 'join2', 'delete_relation_instance', 'clone_instance', 'add_and_join'
        );

        $minibuscador_bool = false;
        $buscador = false;
        if (array_search($action, $mini_actions) !== false) {
            $minibuscador_bool = true;
            $buscador = true;
        }
        if (file_exists(DIR_APLI_ADMIN.'Accions/'.$accion_name)) {
            $action = ucfirst(Str::camel(str_replace('.php', '', $accion_name)));
            $action = 'Omatech\\Editora\\Admin\\Accions\\'.$action;
            $this->action = new $action();
            $this->middleware = $this->action->getMiddleware();
        } elseif (file_exists(DIR_APLI_ADMIN.'Extras/Accions/'.$accion_name)) {
            eval('require_once(DIR_APLI_ADMIN."Extras/Accions/$accion_name");');
        } elseif (file_exists(FRONT_END_DIR.'/app/Http/Controllers/Editora/Extras/'.$accion_name)) {
            $params = [];
            for ($i = 1; $i < 10; $i++) {
                if (isset($_REQUEST['p'.$i])) {
                    $params[$i] = $_REQUEST['p'.$i];
                }
            }
            $action = str_replace('.php', '', $accion_name);
            $action = 'App\\Http\\Controllers\\Editora\\Extras\\'.$action;
            $this->action = new $action();
        } else {
            header('HTTP/1.1 404 Not Found');
            eval('require_once(DIR_APLI_ADMIN."Accions/AdminGetMain.php");');
            $_REQUEST['view'] = 'notfound';
        }
    }

    public function init()
    {
        global $array_langs;
        $array_langs = config('editora-admin.languages');
        global $lg;
        $lg = getDefaultLanguage();
        require_once(DIR_APLI_ADMIN . DIR_LANGS . $lg.'/messages.inc');
        return $this->action->render();
    }
}
