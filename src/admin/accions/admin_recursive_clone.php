<?php

use Illuminate\Support\Facades\Session;
//à
	$sc=new security();
	if ($sc->testSession()==0) {
		$sc->endSession();
	}
	else {
		$params=get_params_info();
		if ($sc->getAccess('browseable',$params)) {
			$ly=new layout();
			$in=new instances();
			$at=new attributes();
			$ly_t=new layout_template();
			$at_t=new attributes_template();

			$params['p_mode']='V';
			$params['p_acces_type']='F';
			$params['prefixclone']=!empty($_REQUEST['preclone'])?$_REQUEST['preclone']:'CLONED';
			
			
			$ret=$in->recursive_clone($params['param2'], time(),$params['prefixclone']);
			//$dolly_id = recursive_clone($link, $instid, $session);

			if($ret>0) {
				$message=html_message_ok("Clone OK");
				$params['param2']=$ret;
			}
			else $message=html_message_warning("Unexpected error: ".$ret);

			Session::put('missatge', $message);
            $sc->redirect_url(APP_BASE.'/get_main');
			
			$title=EDITORA_NAME." -> ".getMessage('info_view_object');
			$ly_t->pinta_CommonLayout($top_menu, $buscador, $last_accessed, $favorites, $special, $ly, $in, $lg, $params);
			$body=$at_t->instanceAttributes_view($at->getInstanceAttributes('V', $params), $params);
			$parents=$ly_t->paintParentsList($in->getParents($params),$params);

			$_REQUEST['view']='container';
		}
		else {
			Session::put('missatge', html_message_error(getMessage('error_role_privileges')));
			$sc->redirect_url(APP_BASE.'/get_main');
		}
	}
?>