<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Relations;
use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Templates\LayoutTemplate;
use Omatech\Editora\Admin\Templates\AttributesTemplate;

class AdminNewInstance2 extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        $message = null;
        $menu = [];
        if (Session::get('rol_id') == 1 || $security->getAccess('insertable', $params)) {
            $instances = new Instances;
            $at = new attributes();
            $ly_t = new LayoutTemplate();
            $at_t = new attributesTemplate();
            $re = new relations();

            $params['p_mode'] = $p_mode = 'V';
            $menu = $this->loadMenu($instances, $params);
            $title = EDITORA_NAME . " -> " . getMessage('info_create_object') . " " . getClassName($params['param1']);
            $res = $instances->insertAttributes($params);
            $parents = [];
            if (!$res || $res < 0) {
                $instance = $at->getInstanceAttributes('I', $params);
                $p_mode = 'I';
                if ($res == -1) {
                    $message = html_message_error(getMessage('error_param_mandatory'));
                } elseif ($res == -2) {
                    $message = html_message_error(getMessage('error_param_data'));
                } elseif ($res == -3) {
                    $message = html_message_error(getMessage('error_param_urlnice'));
                }

                $_REQUEST['view'] = 'container';
            } else {
                $params['p_acces_type'] = 'A';
                $params['param2'] = $res;
                $instances->logAccess($params);
                $message = html_message_ok(getMessage('info_word_object').' '.$res.' '.getMessage('info_object_created'));

                $p_multiple = false;
                if (isset($_REQUEST['p_multiple'])) {
                    $p_multiple = $_REQUEST['p_multiple'];
                }

                if ($p_multiple) {
                    $title = EDITORA_NAME . " -> " . getMessage('info_view_object');
                    $res2 = $re->createRelation($params);
                    $instance = $at->getInstanceAttributes($p_mode, $params);
                } elseif ($params['param11']) { // Vengo del relacionar
                    $params['param2'] = $params['param11'];
                    //                    $instances->refreshCache($params);
                    $params['param13'] = $res;
                    $params['param2'] = $params['param13'];
                    $title = EDITORA_NAME . " -> " . getMessage('info_view_object');
                    $res2 = $re->createRelation($params);

                    $params_redirect = array(
                        'param1' => $params['param10'],
                        'param2' => $params['param11'],
                        'param3' => '',
                        'param4' => '',
                        'param5' => '',
                        'param6' => '',
                        'param7' => '',
                        'param8' => '',
                        'param9' => '',
                        'param10' => '',
                        'param11' => '',
                        'param12' => '',
                        'param13' => '',
                        'param14' => '',
                        'p_mode' => 'V',
                        'p_acces_type' => 'A'
                    );

                    $instance = $at->getInstanceAttributes($p_mode, $params_redirect);
                    $parents = $ly_t->paintParentsList($instances->getParents($params_redirect), $params_redirect);
                    $redirect_class_id = $params_redirect['param1'];
                    $redirect_inst_id = $params_redirect['param2'];
                } else { // No vengo del relacionar
                    $params['param2'] = $res;
                    $instance = $at->getInstanceAttributes($p_mode, $params);
                    $parents = $ly_t->paintParentsList($instances->getParents($params), $params);
                    $redirect_class_id = $params['param1'];
                    $redirect_inst_id = $params['param2'];
                }
                $class_id = $params['param1'];
                $inst_id = $params['param2'];
                $instances->instance_update_date_and_backup($inst_id);
                $_REQUEST['view'] = 'container';
                return redirect(route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$redirect_class_id.'&p_inst_id='.$redirect_inst_id));
            }
        }
        $instance['instance_info']['class_id'] = $params['param1'];
        $viewData = array_merge($menu, [
            'instance' => $instance['instance_info'],
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
            'instances' => $instances,
            'parents' => $parents,
            'message' => $message,
            'status_list' => isset($instance['status_list']) ? $instance['status_list'] : null
        ]);

        return response()->view('editora::pages.instance', $viewData);
    }
}
