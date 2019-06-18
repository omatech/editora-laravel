<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Relations;

class AdminAjaxActions extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params=get_params_info();

        if(isset($_SESSION['rol_id'])) {
            $is_ajax=true;
            $sended = false;
            if(isset($_REQUEST['ajax'])) {
                if($_REQUEST['ajax'] == 'ajax_order') {
                    $r=new relations();

                    $values = array();
                    $ordered = explode(',', $_REQUEST['ordered']);
                    foreach($ordered as $value) {
                        $value = str_ireplace('id_', '', $value);
                        array_push($values, $value);
                    }
                    if ($r->order_relation($_REQUEST['instance_id'], $values)) {
                        $sended = true;
                    }
                }
            }

            if ($sended) {
                echo getMessage('saved');
            }else {
                echo getMessage('saved_wrong');
            }
        }

       die();
    }
}
