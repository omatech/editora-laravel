<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\relations;
use Omatech\Editora\Admin\Templates\AttributesTemplate;
use Omatech\Editora\Admin\Templates\LayoutTemplate;

class AdminEditInstance2 extends AuthController
{

    public function render()
    {
        $security = new Security;
        $params=get_params_info();
        $message = null;

        if ($_SESSION['rol_id']==1 || $security->getAccess('editable',$params)) {
            $instances = new Instances;
            $at=new attributes();
            $ly_t=new LayoutTemplate();
            $at_t=new attributesTemplate();
            $re=new relations();

            $params['p_mode']= $p_mode = 'V';
            $params['p_acces_type']='A';
            $title=EDITORA_NAME." -> ".getMessage('info_create_object')." ".getClassName($params['param1']);

            $instances->logAccess($params);
            $res=$instances->insertAttributes($params);
            
            $menu = $this->loadMenu($instances, $params);

            $init = '';
            
            if (!$res || $res < 0) {
                $params['p_mode']='V';
                $params['p_acces_type']='A';
                $p_mode = 'U';
                if ($params['param1'] == '' or $params['param1']<0){
                    $params['param1']=$params['param12'];
                }
                $instance = $at->getInstanceAttributes('U', $params);

                if ($res==-1) {
                    $message=html_message_error(getMessage('error_param_mandatory'));
                }elseif ($res==-2) {
                    $message=html_message_error(getMessage('error_param_data'));
                }elseif ($res==-3) {
                    $message=html_message_error(getMessage('error_param_urlnice'));
                }
                
            } else { //sabem que s'han insertat be els atribs, peticio de refresc de cache
                $params['p_acces_type']='A';
                $instances->logAccess($params);
                $message=html_message_ok(getMessage('info_word_object').' '.$res.' '.getMessage('info_object_updated'));
                if (isset($_REQUEST['p_multiple'])) {
                    $p_multiple=$_REQUEST['p_multiple'];
                } else {
                    $p_multiple=null;
                }

                if ($p_multiple) {
                    $title=EDITORA_NAME." -> ".getMessage('info_view_object');
                    $res=$re->createRelation($params);
                    $instance = $at->getInstanceAttributes($p_mode, $params);
                } elseif ($params['param11']) { // Vengo del relacionar
                    $title=EDITORA_NAME." -> ".getMessage('info_view_object');
                    $res=$re->createRelation($params);
                    $instance = $at->getInstanceAttributes($p_mode, $params);
                    $init = 'edit_and_join';
                } else { // No vengo del relacionar
                    $params['param2'] = $res;
                    $instance = $at->getInstanceAttributes($p_mode, $params);
                    $init = 'edit_instance';
                }

                $inst_id=$params['param2'];
                $instances->instance_update_date_and_backup($inst_id);
            }

            $parents=$ly_t->paintParentsList($instances->getParents($params), $params);
        }

        switch ($init) {
            case 'edit_instance':
                return redirect(route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$params['param1'].'&p_inst_id='.$params['param2']));
                break;
            default:
                $instance['instance_info']['class_id']=$params['param1'];
                $viewData = array_merge($menu, [
                    'instance' => $instance['instance_info'],
                    'p_mode' => $p_mode,
                    'body_class' => 'edit-view',
                    'title' => $title,
                    'instances' => $instances,
                    'parents' => $parents,
                    'message' => $message
                ]);

                return response()->view('editora::pages.instance', $viewData);
                break;
        }
    }
}
