<?php

use Illuminate\Support\Facades\Session;
//à
	$sc=new security();	
	if ($sc->testSession()==0) {
		$sc->endSession();
	}
	else {
		$params=get_params_info();

		$inst_to_delete = explode(',', $params['param2']);
		$inst_arr = array();
		$x_able = 0;
		if(!empty($inst_to_delete)) {
			
			$in=new instances();
			foreach ($inst_to_delete as $value)
			{		
				$p_class_id=$in->getClassID_from_Instance($value);
				$val['param2'] = $value;
				$val['param1'] = $p_class_id;
				$val['x_able'] = $sc->getAccess('deleteable',$val);
				if($val['x_able'] == 1)
					$x_able++;
				array_push($inst_arr, $val);
				$class_names .= getClassName($value['param1']).', ';
			}

			if($x_able == count($inst_to_delete)) {
				$ly=new layout();
				$at=new attributes();
				$ly_t=new layout_template();
				$in_t=new instances_template();

				$params['p_mode']='V';

				$title=EDITORA_NAME." -> ".getMessage('info_delete_multiple_object').": ".substr($class_names,0, count($class_names)- 2 /*eliminem el ', ' del final del str*/);
				$message=html_message_ok($in->deleteInstanceArr($inst_arr));
				
				$ly_t->pinta_CommonLayout($top_menu, $buscador, $last_accessed, $favorites, $special, $ly, $in, $lg, $params);
				$instances = $in->instanceList($params);
				$body=$in_t->instancesList_view($instances, $in->instanceList_count($params));

				$_REQUEST['view']='container';
			}
			else
			{
				Session::put('missatge', html_message_error(getMessage('error_role_privileges')));
				$sc->redirect_url(APP_BASE.'/get_main');
			}
		}
		else //JORDI !!!! canviar missatge
		{
			Session::put('missatge', html_message_error(getMessage('NO HAS SELECCIOAT CAP INSTANCIA')));
			$sc->redirect_url(APP_BASE.'/get_main');
		}
	}

?>
