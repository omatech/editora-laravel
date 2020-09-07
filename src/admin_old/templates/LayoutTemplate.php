<?php

namespace Omatech\Editora\Admin\Templates;

use Omatech\Editora\Admin\Extras\Accions\AccionListTemplate;

class LayoutTemplate extends Template
{
	///////////////////////////////////////////////////////////////////////////////////////////
	function pinta_topMenu($arr_menu) {
		$html='<li><h2><a href="'.APP_BASE.'/get_main">'.__('editora_lang::messages.navigation_home').'</a></h2></li>';
        foreach($arr_menu as $am) {

			if ($am['list']){
			    $html.="<li><h2><span>".$am['lg_cap']."</span></h2><ul>";
            }
			if (is_array($am['list']) && count($am['list'])>0) {
				foreach($am['list'] as $sm) {
					if ($sm['grp_order']<100) $class='li1';
					elseif ($sm['grp_order']>100 && $sm['grp_order']<200) $class='li2';
					elseif ($sm['grp_order']>200 && $sm['grp_order']<300) $class='li3';
					elseif ($sm['grp_order']>300 && $sm['grp_order']<400) $class='li4';
					elseif ($sm['grp_order']>400 && $sm['grp_order']<500) $class='li3';
					elseif ($sm['grp_order']>500 && $sm['grp_order']<600) $class='li2';
					elseif ($sm['grp_order']>600 && $sm['grp_order']<700) $class='li1';
					else $class='li1';
					
					$no_arrow = 'style="background-image:none;"';
					if(array_key_exists($sm['id'], $GLOBALS['special_classes'])){
						$id = $sm['id'];
						$html.='
						<li class="'.$class.'">
						<h3><a '.$no_arrow.' href="'.ADMIN_URL.'/view_instance/?p_pagina=1&p_class_id='.$sm['id'].'&p_inst_id='.$GLOBALS['special_classes'][$id].'">'.$sm['lg_name'].'</a></h3>
						</li>';
					}else{
						$html.='<li class="'.$class.'"><h3><a '.$no_arrow.' href="'.ADMIN_URL.'/list_instances/?p_class_id='.$sm['id'].'">'.$sm['lg_name'].'</a></h3></li>';
					}
				}
			}
            if ($am['list']){
			    $html.="</ul></li>";
            }
		}


        if(file_exists(DIR_APLI_ADMIN.'/extras/accions/menulist.php')){
            $menulist = new menulist_template();
            $extra = true;
        }else{
            $extra = false;
        }

        if($extra){
            $html .= $menulist->getExtraMenu();
        }




	    return $html;
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function paintSearchForm($class_selector, $param_arr) {
		$p_search_query=$param_arr['param4'];
		$p_fecha_ini=str_replace("-", "/", $param_arr['param5']);
		$p_fecha_fin=str_replace("-", "/", $param_arr['param6']);
		$p_order_by=$param_arr['param7'];
		$p_search_state=$param_arr['param8'];
		
		$html = '<!-- CERCADOR GRAN -->
		<form method="post" action="'.APP_BASE.'/search" name="Form_search" class="form visible">
			<fieldset>
				<legend>'.__('editora_lang::messages.info_word_search').'</legend>
				<span class="split"></span>
				<div>
					<p>
						<label for="p_class_id">'.__('editora_lang::messages.info_word_type').'</label>'.$class_selector.'
						<input type="hidden" name="p_pagina" value="1" class="inputcerca"/>
						<input type="hidden" name="p_order_by" value="'.$p_order_by.'" class="inputcerca"/>
					</p>
					<p>
						<label for="p_search_query">'.__('editora_lang::messages.info_word_text').'</label>
						<input type="text" name="p_search_query" id="p_search_query" size="20" value="'.$p_search_query.'"/>
					</p>
					<p>
						<label for="p_search_state">'.__('editora_lang::messages.info_word_status').'</label>
						<select name="p_search_state" id="p_search_state">
							<option value="">&nbsp;</option>';
							if ($p_search_state == 'P') $html.='<option value="P" selected="selected">'.__('editora_lang::messages.info_word_status_pending').'</option>';
							else $html.='<option value="P">'.__('editora_lang::messages.info_word_status_pending').'</option>';
							if ($p_search_state == 'R') $html.='<option value="R" selected="selected">'.__('editora_lang::messages.info_word_status_reviewed').'</option>';
							else $html.='<option value="R">'.__('editora_lang::messages.info_word_status_reviewed').'</option>';
							if ($p_search_state == 'O') $html.='<option value="O" selected="selected">'.__('editora_lang::messages.info_word_status_published').'</option>';
							else $html.='<option value="O">'.__('editora_lang::messages.info_word_status_published').'</option>';
						$html.='</select>
					</p>
			   </div>
			</fieldset>
			<fieldset class="adv_search">
				<span class="split"></span>
				<h3 class="adv_show"> <a href="javascript://" onclick="$(\'#advsearch\').toggle();">  <i class="fa fa-search-plus"></i> '.__('editora_lang::messages.advanced_search').'</a> </h3>';
				if ($p_fecha_ini <> '' || $p_fecha_fin<>'') $html.='<div class="adv_show visible" id="advsearch">';
				else $html.='<div class="adv_hide visible"" id="advsearch"><!-- adv_hide -->';
					$html.='<div class="p">
						<p>
							<label for="">'.__('editora_lang::messages.info_word_initial_date').'</label>
							<input type="text" name="p_fecha_ini" size="10" value="'.$p_fecha_ini.'" class="inputcerca" id="date_s1"/>
						</p>
					</div>
					<div class="p">
						<p>
							<label for="date_s2">'.__('editora_lang::messages.info_word_final_date').'</label>
							<input type="text" name="p_fecha_fin" size="10" value="'.$p_fecha_fin.'" class="inputcerca" id="date_s2"/>
						</p>
					</div>
					<div class="clear"></div>
			  </div>
			 </fieldset>
		<p class="btn"><input type="submit" value="'.__('editora_lang::messages.info_word_search_button').'" title="'.__('editora_lang::messages.info_word_search_button').'" /></p>
		<div class="clear"></div>
		</form> ';

		return $html;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	function paintMiniCercador($class_selector, $p_search_query) {
		$html = '';

		$html.='
		<!-- Cercador -->
		<form method="post" action="'.APP_BASE.'/search" name="Form_search" class="form">
			<fieldset>
				<legend>'.__('editora_lang::messages.container_mini_cercador').'</legend>

				<span class="split"></span>
				<p>
					<label for="mini_p_class_id">'.__('editora_lang::messages.info_word_type').'</label>
					'.$class_selector.'
					<input type="hidden" name="p_pagina" value="1" class="inputcerca"/>
					<input type="hidden" name="mini_p_order_by" value="" />
				</p>
				<p>
					<label for="mini_p_search_query">'.__('editora_lang::messages.info_word_text').'</label>
					<input type="text" name="p_search_query" id="p_search_query" size="20" value="'.$p_search_query.'" />
				</p>
		   </fieldset>
		   <p class="btn"><input type="submit" value="'.__('editora_lang::messages.info_word_search_button').'" title="'.__('editora_lang::messages.info_word_search_button').'" /></p>
		</form>
		<!-- Fi codi del Cercador -->';

		return $html;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	function paintUserBoxInstances($inst_arr, $p_tipo, $param_arr) {
		$p_class_id= $param_arr['param1'];
		$p_inst_id= $param_arr['param2'];
		
		$l_cont=0;
		$html='<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr class="thead">
				<th scope="col"><span>Identificador</span></th>
				<th scope="col"><span>Títol</span></th>
				<th scope="col"><span>Editar</span></th>
				<th scope="col"><span>Preferit</span></th>
			</tr>';
			foreach ($inst_arr as $row) {
				$html.=' <tr>
					<th scope="row"><span>'.$row['id'].'</span></th>
					<td><a href="'.APP_BASE.'/view_instance/?p_pagina=1&amp;p_class_id='.$row['class_id'].'&amp;p_inst_id='.$row['id'].'" title="'.__('editora_lang::messages.info_word_view').' '.$row['key_fields'].'">';
						if (strlen($row['key_fields'])>25) $html.= mb_substr($row['key_fields'],0,20).'...';
						else $html.=$row['key_fields'];
					$html.='</a></td>';
					if ($p_tipo == 'F') {
						$html.='<td class="ico edi"><a href="'.APP_BASE.'/edit_instance/?p_pagina=1&amp;p_class_id='.$row['class_id'].'&amp;p_inst_id='.$row['id'].'">Editar '.$row['id'].'</a></td>';
						$tmp_url='';
						$html.='<td class="ico del"><a href="'.APP_BASE.'/delete_favorite/?p_pagina=1&amp;p_class_id='.$row['class_id'].'&amp;p_inst_id='.$row['id'].$tmp_url.'">Quitar de favoritos '.$row['id'].'</a></td>';
					}
					elseif ($p_tipo == 'A') {
						$html.='<td class="ico fav"><a href="'.APP_BASE.'/add_favorite/?p_pagina=1&amp;p_class_id='.$row['class_id'].'&amp;p_inst_id='.$row['id'].'">Añadir a favoritos '.$row['id'].'</a></td>';
						$html.='<td class="ico edi"><a href="'.APP_BASE.'/edit_instance/?p_pagina=1&amp;p_class_id='.$row['class_id'].'&amp;p_inst_id='.$row['id'].'">Editar '.$row['id'].'</a></td>';
					}
				$html.='</tr>';
				$l_cont=$l_cont+1;
			}
		$html.='</table>';
		if ($l_cont>0) return $html;
		else return '<span class="omp_field">'.  getMessage("no_found").'</span>';
	} 
	
	///////////////////////////////////////////////////////////////////////////////////////////
	function paintParentsList($inst_arr, $param_arr) {
		$html='';
		
		if (count($inst_arr)>0) {
			$html.='<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr class="thead">
					<th scope="col"><span>Identificador</span></th>
					<th scope="col"><span>Títol</span></th>
					<th scope="col"><span>Preferit</span></th>
					<th scope="col"><span>Editar</span></th>
				</tr>';
				foreach($inst_arr as $Row) {
					$html.='<tr>
						<th scope="row" id="parent_rel'.$Row['ri_id'].'"><span>'.$Row['id'].' </span></th>
						<td><a href="'.APP_BASE.'/view_instance/?p_pagina=1&amp;p_class_id='.$Row['class_id'].'&amp;p_inst_id='.$Row['id'].'" title="'.__('editora_lang::messages.info_word_view').' '.$Row['key_fields'].'">';
						if (strlen($Row['key_fields'])>25) $html.=mb_substr($Row['key_fields'],0,25).'...';
                        else $html.=$Row['key_fields'];
						$html.='</a></td>';
						$html.='<td class="ico fav"><a href="'.APP_BASE.'/add_favorite/?p_pagina=1&amp;p_class_id='.$Row['class_id'].'&amp;p_inst_id='.$Row['id'].'">Añadir a favoritos</a></td>';
						$html.='<td class="ico edi"><a href="'.APP_BASE.'/edit_instance/?p_pagina=1&amp;p_class_id='.$Row['class_id'].'&amp;p_inst_id='.$Row['id'].'">Editar</a></td>';
				   $html.='</tr>';
				}
			$html.='</table>';
		}

		return $html;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	function getSpecialFunctions() {
		if(file_exists(DIR_APLI_ADMIN.'/extras/accions/accionslist.php')){
			$accionslist = new AccionListTemplate();
			$extra = true;
		}else{
			$extra = false;
		}
		
		$p_class_id= $_GET['p_class_id'];
		$p_inst_id= $_GET['p_inst_id'];
		$html='';
	 	
		$html .= '<li><a href="'.APP_BASE.'/static_text">'.getMessage("static_text").'</a></li>';
		
		if($extra){ $html .= $accionslist->getExtraSpecialFunctions($p_class_id, $p_inst_id ); }
		
		if($html!=''){
			$html = '<ul>'.$html.'</ul>';
		}
		return $html;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	function pinta_CommonLayout( &$top_menu
	,&$buscador
	,&$last_accessed
	,&$favorites
	,&$special
	,$ly
	,$in
	,$lg
	,$params) {
		$top_menu=$this->pinta_topMenu($ly->get_topMenu($lg));
		
		require_once(DIR_APLI_ADMIN.'/utils/sanitize.php');
		if($buscador) {
			$mini_paraula = '';
			if(isset($_REQUEST['p_search_query']))
				$mini_paraula = sanitize($_REQUEST['p_search_query']);
			$buscador = $this->paintMiniCercador(parent::getClassList($params['param1']), $mini_paraula);
		}
		else {
			$buscador = $this->paintSearchForm(parent::getClassList($params['param1']), $params);
		}
			
		$last_accessed=$this->paintUserBoxInstances($in->getLastInstances(),'A', $params);
		$favorites=$this->paintUserBoxInstances($in->getFavorites(),'F', $params);
		$special=$this->getSpecialFunctions();
	}
}
