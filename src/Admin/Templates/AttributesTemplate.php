<?php

namespace Omatech\Editora\Admin\Templates;

use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Util\Urls;
use Illuminate\Support\Facades\Session;

class AttributesTemplate extends Template
{
	var $javascript_attributes = '';

	function instanceAttributes_view($instance_arr, $param_arr) {
		return $this->paintAttributes($instance_arr, $param_arr['param1'], 'V', $param_arr['param2'], NULL, $param_arr);
	}

	function instanceAttributes_edit($instance_arr, $param_arr) {
		return $this->paintAttributes($instance_arr, $param_arr['param1'], 'U', $param_arr['param2'], NULL, $param_arr);
	}

	function instanceAttributes_insert($instance_arr, $param_arr) {
		return $this->paintAttributes($instance_arr, $param_arr['param1'], 'I', NULL, NULL, $param_arr);
	}

	function instanceAttributes_insert_relation($instance_arr, $param_arr) {
		$include_form='<input type="hidden" name="p_parent_class_id" value="'.$param_arr['param10'].'">
		<input type="hidden" id="p_tab" name="p_tab" class="input_tabs" value="'.$param_arr['param14'].'">
		<input type="hidden" name="p_relation_id" value="'.$param_arr['param9'].'">
		<input type="hidden" name="p_parent_inst_id" value="'.$param_arr['param2'].'">';

		return $this->paintAttributes($instance_arr, $param_arr['param1'], 'I', NULL, $include_form, $param_arr);
	}

	private function paintAttributes($instance_arr, $p_class_id, $p_mode, $p_inst_id, $p_include_form, $param_arr) {
		// p_mode indica si estem en Insert, Update o View
		$fila=-1;
		$columna=-1;
		$k=0;

		$res='<!-- CAIXA PESTANYES -->
		<div class="col_item tab_box">';
			$num_tabs=count($instance_arr);
			$tab_array=array();
			$res.='<!-- PESTANYES -->
			<div class="tabs">
				<ul id="rowtab">';
					foreach($instance_arr['instance_info']['instance_tabs'] as $Row) {
						$tab_array[]=$Row['id'];
						if ($num_tabs<7) $tab_notab='';
						else $tab_notab='2';
						/* PINTA TABS */
						if ($Row['id'] >= 0) {
							if(!isset($param_arr['param14']) || $param_arr['param14']=="") {
								if($k==0) {
									$res.='<li class="selected'.$tab_notab.'" onClick="changeTab(\''.$Row['name'].'\', \''.$tab_notab.'\', $(this), \''.$Row['id'].'\');"><a href="javascript://" title="'.$Row['caption'].'">'.$Row['caption'].'</a></li>';
									$a_tab=$Row['id'];
									$default_tab=$Row['id'];
								}
								else {
									$res.='<li class="'.$tab_notab.'" onClick="changeTab(\''.$Row['id'].'\', \''.$tab_notab.'\', $(this), \''.$Row['id'].'\');" title="'.$Row['caption'].'"><a href="javascript://">'.$Row['caption'].'</a></li>';
								}
							}
							elseif($Row['id']==$param_arr['param14']) { // El tab es el que hem seleccionat
								$res.='<li class="selected'.$tab_notab.'" onClick="changeTab(\''.$Row['id'].'\', \''.$tab_notab.'\', $(this), \''.$Row['id'].'\');" title="'.$Row['name'].'"><a href="javascript://">'.$Row['caption'].'</a></li>';
							}
							else { // Tab que no hem seleccionat
								$res.='<li class="'.$tab_notab.'" onClick="changeTab(\''.$Row['id'].'\', \''.$tab_notab.'\', $(this), \''.$Row['id'].'\');" title="'.$Row['name'].'"><a href="javascript://">'.$Row['caption'].'</a></li>';
							}

							$k++;
						}
					}
				$res.='</ul>
			<div class="clear"></div>
		</div><!-- /end PESTANYES -->';

		$res.='<!-- CAIXA -->
		<div class="edi_box" id="taulatab">';
			$res.=$this->getInstanceToolbar($instance_arr, $p_class_id, $p_inst_id, $p_mode, $p_include_form, $param_arr);
			$res_tabs="";
			$mandatory_hidden="";
			foreach($instance_arr['instance_info']['instance_tabs'] as $Row) {
				$fila=-1;
				$columna=-1;
				$res_tabs.='<!-- inicio div tab -->
				<div id="'.$Row['id'].'" class="amagable">';
					$cont = count($Row['elsatribs']);
					$t=1;
					$res_attributes="";
					foreach($Row['elsatribs'] as $Row2) {
						//if (($Row2['type']=='R' && $p_mode=='V') || $Row2['type']!='R') {
							$res_attributes.=$this->itemStackPrefix($fila, $columna, $Row2['fila'], $Row2['columna'], $p_mode, $cont);
							$fila=$Row2['fila'];
							$columna=$Row2['columna'];
							$atr_values=NULL;
							$res_attributes.=$this->getAttributeInner($Row2, $p_mode, $p_inst_id, $Row['id'], $t, $param_arr);
							$mandatory_hidden.=$this->attributeMandatory($Row2, $p_mode);

							$cont--;
							$t++;
						//}
					}
					$res_tabs.=$res_attributes;
					//$res_tabs.=$this->itemStackPostfix($p_mode);
				$res_tabs.='</div></div></div></div>
				<!-- Fin div tab-->';
			}
		$res.=$res_tabs;

		$Row=$instance_arr['instance_info']['instance_tabs'][0];
		if ($Row['id'] >= 0) {
			if (isset($param_arr['param14']) && $param_arr['param14']<>'') {
				$a_tab = $param_arr['param14'];
			}

			if((!isset($_REQUEST['tab']) || $_REQUEST['tab']=="") && in_array($a_tab,$tab_array)) {
				$res.='<script type="text/javascript">$(\'div#taulatab div.amagable\').hide();$(\'div#'.$a_tab.'\').show();</script>';
			}
			elseif(in_array($_REQUEST['tab'],$tab_array)) {
				$res.='<script type="text/javascript">$(\'div#taulatab div.amagable\').hide();$(\'div#'.$_REQUEST['tab'].'\').show();</script>';
			}
			else {
				$res.='<script type="text/javascript">$(\'div#taulatab div.amagable\').hide();$(\'div#'.$default_tab.'\').show();</script>';
			}
		}

			//AQUI VAN LOS BOTONES del PIE
			$res.='  <div class="row btns_row">';
				$res.='<input type="hidden" name="p_mandatories" value="'.substr($mandatory_hidden,0,strlen($mandatory_hidden)-1).'"/>';
				if ($p_mode=='I' || $p_mode=='U') $res.='<input type="hidden" name="enviat" value="1"/>';
				$res.='<div class="column">
					<p class="btn_back"><a href="javascript://" onclick="history.go(-1)" title="'.getMessage('navigation_back').'">'.getMessage('navigation_back').'</a></p>';
				$res.='</div>';
				$res.='<div class="column">';
					if ($p_mode=='I') {
						$res.='<p class="btn"><input type="submit" value="'.getMessage('info_word_add_button').'" class="boto20" /></p>'.csrf_field();
					}
					elseif ($p_mode=='U') {
						$res.='<p class="btn"><input type="submit" value="'.getMessage('info_word_update_button').'" class="boto20" /></p>'.csrf_field();
					}
					elseif ($p_mode=='V') {
						$res.='<p class="btn"><a href="'.APP_BASE.'/edit_instance?p_pagina=1&amp;p_class_id='.$p_class_id.'&amp;p_inst_id='.$p_inst_id.'&p_tab='.$param_arr['param14'].'" title="'.getMessage('info_word_edit_button').'" class="boto20 link_tabs">'.getMessage('info_word_edit_button').'</a></p>';
					}
				$res.='</div>
			</div>
		</div><!-- FIN BOTONS --></form>';
		$res.='</div><!-- FIN TAULTAB -->';
		$res.=' </div><!-- FIN COL_ITEM TAB_BOX -->';

		return $res;
	}

	private function getInstanceToolbar($row, $p_class_id, $p_inst_id, $p_mode, $p_include_form = "", $param_arr) {
		$res = '';

		if ($p_mode=='I') {//objecte nou
			$res.='<div class="edi_tit wrap"><h2>'.getMessage('creando_objeto').'  '.getMessage('viendo_objeto2').': <span>'.getClassName($p_class_id).'</span></h2></div>';

			$res.= '<div class="edi_panel wrap">';

			$res.= '<form class="form" method="post" ENCTYPE="multipart/form-data" name="Form1" action="'.APP_BASE.'/new_instance2">
			<input type="hidden" name="p_class_id" value="'.$p_class_id.'"/>';
			$res .= $p_include_form;
						$res.= '
						<!-- FILA 1 -->
								<div class="row">
									<!-- COLUMNA ESQUERRA -->
									<div class="column">
										<div class="col_item">';
			$res.= '<p><label for="">'.getMessage('info_word_status').'</label>';
			$res.= '<span class="ico_field">

			<select name="p_status" id="p_status" onchange="changestatusimg(this);">';
			if ($row['status_list']['status1']==1) $res.= '<option value="P">'.getMessage('info_word_status_pending').'</option>';
			if ($row['status_list']['status2']==1) $res.='<option value="V">'.getMessage('info_word_status_reviewed').'</option>';
			if ($row['status_list']['status3']==1) $res.='<option value="O">'.getMessage('info_word_status_published').'</option>';
			$res.='</select><span class="status pending" title="Pendiente" id="statusimg">Pendiente</span>
							</span></p><div class="clear"></div>';
						$res.='</div>';
					   $res.='
							 </div>
							 <!-- /end COLUMNA ESQUERRA -->';
						$res.='<!-- COLUMNA DRETA -->
									<div class="column">
										<div class="col_item">
											<div class="p">
												<p>';

			$res.= '<label for="">'.getMessage('info_word_publishing_begins').'</label>

			<span class="field">';
			if (isset($row['instance_info']['publishing_begins'])) $res.='<input type="text" length="35" name="p_publishing_begins" value="'.$row['instance_info']['publishing_begins'].'" id="date1" class="datepicker"/>';
			else $res.='<input type="text" length="35" name="p_publishing_begins" id="date1" class="datepicker"/>';
			$res.='</span>
							</p>
							</div>
					   </div>';

			$res.='<div class="col_item">
											<div class="p">
											<p>';
			$res.='
			 <label for="date_s2">'.getMessage('info_word_publishing_ends').':</label>';
			$res.='<span class="ico_field">';
			if (isset($row['instance_info']['publishing_ends'])) $res.='<input type="text" class="w_100" size="10" name="p_publishing_ends" value="'.$row['instance_info']['publishing_ends'].'" id="date2" class="datepicker"/></span>';
			else $res.='<input type="text" length="35" name="p_publishing_ends" id="date2" class="datepicker"/></span></td></tr>';

			$res.='';
			$res.='</div>
							</div>
									</div>
									<!-- /end COLUMNA DRETA -->
										<div class="clear"></div>';
						$res.='</div>
								<!-- /end FILA 1 -->';
		}
		elseif ($p_mode=='V') { //view
			if ($row['instance_info']['status']!="O") $html_delete='<li><a href="'.APP_BASE.'/delete_instance?p_pagina=1&amp;p_class_id='.$p_class_id.'&amp;p_inst_id='.$p_inst_id.'" title="Eliminar"><i class="fa fa-trash-o fa-2x"></i> </a></li>';
			else $html_delete='';

			$html_edit='<li class="ico edi"><a href="'.APP_BASE.'/edit_instance?p_pagina=1&amp;p_class_id='.$p_class_id.'&amp;p_inst_id='.$p_inst_id.'&p_tab='.$param_arr['param14'].'" title="Editar" class="link_tabs">Editar</a></li>';

			$res.='<!-- TITOL -->
                            <div class="edi_tit wrap">';
			$res.='<h2>'.getMessage('viendo_objeto').'<span> "'.$row['instance_info']['key_fields'].'"</span>'.getMessage('viendo_objeto2').': <span>'.getClassName($p_class_id).'</span></h2>';

			$res.='<ul>
				<li class="ico fav"><a href="'.APP_BASE.'/add_favorite?p_pagina=1&amp;p_class_id='.$p_class_id.'&amp;p_inst_id='.$p_inst_id.'" title="Afegir a favorits">Afegir a favorits</a></li>'.$html_edit.$html_delete;

            if ( (defined('ACTIVATE_CLONE') && ACTIVATE_CLONE) || Session::get('user_type')=='O' ) {
                $html_clone = '<li class="ico clon"><a href="'.APP_BASE.'/clone_instance?p_pagina=1&amp;p_class_id='.$p_class_id.'&amp;p_inst_id='.$p_inst_id.'&p_tab='.$param_arr['param14'].'" title="Clonar" class="link_tabs">Clonar</a></li>';
                $res.=$html_clone;
            }
            if ( (defined('ACTIVATE_RECURSIVE_CLONE') && ACTIVATE_RECURSIVE_CLONE) || Session::get('user_type')=='O' ) {
                $html_rec_clone='<li class="ico clon_rec"><a title="Clonar recursivament" href="'.APP_BASE.'/recursive_clone?p_class_id='.$p_class_id.'&amp;p_inst_id='.$p_inst_id.'">Clonar recursivament</a></li>';

                $res.=$html_rec_clone;
            }

			$res.='</ul>
			</div>
			<!-- /end TITOL -->';

			$res.='<!-- PLAFO EDICIO -->
			<div class="edi_panel wrap">';
				$res.='
				<!-- FILA 1 -->
				<div class="row">
				<!-- COLUMNA ESQUERRA -->
					<div class="column">
						<div class="col_item">
							<p>
								<label for="">'.getMessage('info_word_status').'</label>
								<span class="ico_field">
									'.parent::status_to_html2($row['instance_info']['status']).'
								</span>
							</p><div class="clear"></div>
						</div>';
		$res .='</div>
				<!-- /end COLUMNA ESQUERRA -->';

		$res .='<!-- COLUMNA DRETA -->
					<div class="column">
						<div class="col_item">
							<p>
								<label for="">'.getMessage('info_word_publishing_begins').'</label>
								<input type="text" id="date_s1" value="'.$row['instance_info']['publishing_begins'].'" size="10" name="p_fecha_ini" class="w_100 disabled" readonly="readonly">
							</p>
						</div>
						<div class="col_item">
							<div class="p">
								<p>
									<label for="date_s2">'.getMessage('info_word_publishing_ends').'</label>
									<input type="text" id="date_s2" value="'.$row['instance_info']['publishing_ends'].'" size="10" name="p_fecha_fin" disabled="disabled" readonly="readonly" class="w_100 disabled" />
								</p>
						   </div>
						</div>
					 </div>
					 <!-- /end COLUMNA DRETA -->
					 <div class="clear"></div>
					</div>
					<!-- /end FILA 1 -->';

		}
		elseif ($p_mode=='U') {  //edit
			$res.='<div class="edi_tit wrap">';
			$res.='<h2>'.getMessage('info_edit_object').' <span>"'.$row['instance_info']['key_fields'].'"</span> '.getMessage('info_word_typeof').' : <span>'.getClassName($p_class_id).'</span></h2>';
			$res .= '</div>
                            <div class="edi_panel wrap">';
			$res.= '<form class="form" method="post" ENCTYPE="multipart/form-data" name="Form1" action="'.APP_BASE.'/edit_instance2">
					<input type="hidden" name="p_class_id" value="'.$p_class_id.'"/>
					<input type="hidden" name="p_inst_id" value="'.$p_inst_id.'"/>';
				$res.='<!-- FILA 1 -->
					<div class="row">
					<!-- COLUMNA ESQUERRA -->
						<div class="column">
							<div class="col_item">
							<p>
							<label for="">'.getMessage('info_word_status').'</label>
							<span class="ico_field">';

			$res .='<select name="p_status" name="id" onchange="changestatusimg(this);">';
			if ($row['status_list']['status1']==1) {
				if ($row['instance_info']['status']=='P') {
					$res.='<option value="P" SELECTED>'.getMessage('info_word_status_pending').'</option>';
					$img='<span id="statusimg" title="'.getMessage('info_word_status_pending').'" class="status pending">'.getMessage('info_word_status_pending').'</span>';
				}
				else {
					$res.='<option value="P">'.getMessage('info_word_status_pending').'</option>';
				}
			}

			if ($row['status_list']['status2']==1) {
				if ($row['instance_info']['status']=='V') {
					$res.='<option value="V" SELECTED>'.getMessage('info_word_status_reviewed').'</option>';
					$img='<span id="statusimg" title="'.getMessage('info_word_status_reviewed').'" class="status revised">'.getMessage('info_word_status_reviewed').'</span>';
				}
				else {
					$res.='<option value="V">'.getMessage('info_word_status_reviewed').'</option>';
				}
			}

			if ($row['status_list']['status3']==1) {
				if ($row['instance_info']['status']=='O') {
					$res.='<option value="O" SELECTED>'.getMessage('info_word_status_published').'</option>';
					$img='<span id="statusimg" title="'.getMessage('info_word_status_published').'" class="status publish">'.getMessage('info_word_status_published').'</span>';
				}
				else {
					$res.='<option value="O">'.getMessage('info_word_status_published').'</option>';
				}
			}
			$res .='</select>'.$img;


			$res .= '</span></p>
                            </div>';
						 	$res .= '
									</div>
									<!-- /end COLUMNA ESQUERRA -->


			<!-- COLUMNA DRETA -->
			<div class="column">
				<div class="col_item">
					<div class="p">
						<p>

						<label for="date_s1">'.getMessage('info_word_publishing_begins').'</label>
						<input type="text" length="35" name="p_publishing_begins" value="'.$row['instance_info']['publishing_begins'].'" id="date1" class="datepicker" />

						</p>
					</div>
				</div>
				<div class="col_item">
					<div class="p">
						<p>
						<label for="date_s2">'.getMessage('info_word_publishing_ends').'</label>
						<input type="text" length="35" name="p_publishing_ends" value="'.$row['instance_info']['publishing_ends'].'" id="date2" class="datepicker"/>
						</p>
					</div>
				</div>
			 </div>
			<!-- /end COLUMNA DRETA -->
				<div class="clear"></div>
		  </div>
		<!-- /end FILA 1 -->';
		}

		return $res;
	}

	private function get_inst_toolbar_special ($p_class_id, $p_inst_id, $p_mode) {
		$res='';
		if ($p_mode=='I') {//objecte nou
			$res.='<div class="filacap"><h3>Creando objeto de tipo '.getClassName($p_class_id).':</h3></div>';
		}

		return $res;
	}


	private function attributeMandatory($row, $p_mode) {
		if ($row['mandatory']=='Y') return($row['id'].',');
		else return '';
	}

	private function itemStackPrefix($p_fila_ant, $p_columna_ant, $p_fila, $p_columna, $p_mode, $p_last = 1) {
		$ret="";

		// Primera fila
		if ($p_fila_ant==-1) $ret.= "<div class='row' rel='". $p_fila." fila'>";
		// Primera fila i columna
		if ($p_columna_ant==-1) $ret.= "<div class='column' rel='". $p_columna."'>
			<div class='col_item'>";

		if ($p_columna_ant!=-1) {
			if ($p_columna_ant!=$p_columna) { // Cambio de columna
				if ($p_fila_ant==$p_fila) { // En la misma fila
						$ret.= "</div>
					</div>
					<div class='column' rel='". $p_columna."'>
						<div class='col_item'>";
				}
				elseif ($p_last == 0) { // En la misma fila
					$ret.= $p_last."plast</div>";
				}
				else { // Canvi de columna i de fila
						$ret.= "</div>
							</div>
						<div class='clear'></div>
					</div>
					<div class='row' rel='". $p_fila."'>
						<div class='column' rel='". $p_columna."'>
							<div class='col_item'>";
				}
			}
			elseif ($p_fila_ant!=$p_fila && $p_columna_ant==$p_columna) { //Misma columna pero distinta fila
				$ret.= "</div>
						</div>
					<div class='clear'></div>
				</div>
				<div class='row' rel='". $p_fila."'>
					<div class='column' rel='". $p_columna."'>
						<div class='col_item'>";
			}
			else {// Estamos en la misma fila
				if ($p_fila_ant!=$p_fila && $p_last != 0) // Canvi nomes de columna
					$ret.= "</div></div><div class='column' rel='". $p_columna."'><div class='col_item'>";
			}
		}

		return $ret;
	}

	private function itemStackPostfix($p_mode) {
		return "</div></div></div>";
	}

	/* ESTA FUNCION PINTA LOS ATRIBUTOS Y RELACIONES DE CADA PESTAÑA DE CONTENIDO */
	private function getAttributeInner($row, $p_mode, $p_inst_id, $tab_id='',$i, $param_arr) {
	    $url = new Urls;
		$ret='';
		global $googlemaps;
		global $autocomplete_str;
		global $autocomplete_header_str;
		$prefix="";
		$postfix="";

		$valor_simple=array();
		if ($row['mandatory']=='Y') $prefix="atr_M".$row['type'].'_';
		else $prefix="atr_O".$row['type'].'_';

		if ($row['ordre_key']) $postfix="_".$row['ordre_key'];
		if ($p_mode != 'I') $valor_simple=$row;

		$max_height=$row['max_height'];
		$max_width=$row['max_width'];
		$img_w=$row['img_w'];
		$img_h=$row['img_h'];

		// Pintem el camp
		$ret .= '<div class="icos_list tit_rel">
			<h3 class="label">'.$row["caption"].' '.html_mandatory_chunk($row['mandatory']).':</h3>
			<ul class="icos_list">
				'.$this->getDescription($row);
				if ($row['type']=="R" && $p_mode=='V') {
					if ($row['join_icon']=='Y') {
						$params_url=$url->generate_url_params($param_arr['param4'],str_replace("-", "/", $param_arr['param5']),str_replace("-", "/", $param_arr['param6']),$param_arr['param7'],$param_arr['param8'],$row['id'],$param_arr['param2'],$row['lookup_id'],$row['max_length'],$param_arr['param13']);
						$ret.='<li class="ico link"><a title="'.getMessage('info_word_join').'" href="'.APP_BASE.'/join?p_pagina=1&amp;p_class_id='.$row['max_length'].'&amp;p_inst_id='.$param_arr['param2'].$params_url.'">'.getMessage('info_word_join').'</a></li>';
					}
                    if ($row['create_icon']=='Y' && $row['max_length']!=0) {
                        $params_url=$url->generate_url_params($param_arr['param4'],str_replace("-", "/", $param_arr['param5']),str_replace("-", "/", $param_arr['param6']),$param_arr['param7'],$param_arr['param8'],$row['id'],null,$row['lookup_id'],$row['max_length'],$param_arr['param13']);
                        $ret.='<li class="ico add"><a title="'.getMessage('info_word_addjoin').'" href="'.APP_BASE.'/add_and_join?p_pagina=1&amp;p_relation_id='.$row['id'].'&amp;p_inst_id='.$p_inst_id.'&amp;p_parent_class_id='.$row['lookup_id'].'&amp;p_child_class_id='.$row['max_length'].'&amp;p_tab='.$tab_id.'">'.getMessage('info_word_addjoin').'</a></li>';
                    }

                }
				// CLONAR IMATGES
				if ($row['type']=="I" && $p_mode=='V') {
					$ret.='<li class="ico clon2"><a title="'.getMessage('info_word_clone').'" href="javascript://" onclick="cloneImage('.$row['class_id'].','.$p_inst_id.','.$row['id'].',\''.$tab_id.'\');">'.getMessage('info_word_clone').'</a></li>';
				}
				if ($row['join_massive']=='Y' && $row['max_length']!=0 && $p_mode=='V') {
						$ret.='<a href="controller.php?p_action=add_massive&amp;p_relation_id='.$row['id'].'&amp;p_inst_id='.$p_inst_id.'&amp;p_parent_class_id='.$row['lookup_id'].'&amp;p_child_class_id='.$row['max_length'].'&amp;p_tab='.$tab_id.'" class="omp_field ico lupa"><img class="icon" src="'.APP_BASE.'/images/plus_nova.gif" border="0" title="'.getMessage('info_word_addmassive').'"/></a>';
						$ret.='<a href="controller.php?p_action=export_massive&amp;p_relation_id='.$row['id'].'&amp;p_inst_id='.$p_inst_id.'&amp;p_parent_class_id='.$row['lookup_id'].'&amp;p_child_class_id='.$row['max_length'].'&amp;p_tab='.$tab_id.'" class="omp_field ico lupa"><img class="icon" src="'.APP_BASE.'/images/export_massiu.gif" border="0" title="'.getMessage('info_word_exportmassive').'"/></a>';
						$ret.='<a onclick="function dr(){if (confirm(\''.getMessage('delete_massive_confirmation').'\')) return true; else return false;}return dr();"href="controller.php?p_action=delete_relation_instance_all&amp;p_relation_id='.$row['rel_id'].'&amp;p_class_id='.$row['lookup_id'].'&amp;p_inst_id='.$p_inst_id.'&amp;p_tab='.$tab_id.'" class="omp_field ico lupa"><img class="icon" src="'.APP_BASE.'/images/plus_delete.gif" border="0" title="'.getMessage('info_word_unjoin_all').'"/></a>';
				}elseif ($row['create_icon']=='Y' && $row['max_length']==0 && $row['related_instances']['info']['multiple_child_class_id']!=null && $p_mode=='V'){
                    $child_classes = explode(',', $row['related_instances']['info']['multiple_child_class_id']);
                    $join = "<ul>";
                    foreach ($child_classes as $child){
                        $join .= "<li><a style='font-size: 14px; text-decoration: none; color: #000;' href='".APP_BASE."/add_and_join?p_pagina=1&amp;p_relation_id=".$row['id']."&amp;p_inst_id=".$p_inst_id."&amp;p_parent_class_id=".$row['lookup_id']."&amp;p_child_class_id=".$child."&amp;p_tab=1' >+ ".getClassName($child)."</a></li> ";
                    }
                    $join .= "</ul>";

                    $ret.='<li class="ico add"><a title="'.getMessage('info_word_addjoin').'" href="#" data-featherlight="'.$join.'">'.getMessage('info_word_addjoin').'</a></li>';
                }

			$ret.='</ul>';
			$ret .= '<div class="clear"></div>
		</div>';
		if ($row['autocomplete']=='Y' && $p_mode=='V') {
			$ret.='<div class="icos_list tit_rel">
				<h3 class="label_autocomplete">Autocomplete&nbsp;</h3>';
				$ret.='<form id="autocomplete-form-'.$row['id'].'" method="post" action="'.APP_BASE.'/autocomplete_add">
				<input type="text" name="autocomplete-'.$row['id'].'" id="autocomplete-'.$row['id'].'" />
				<input type="hidden" name="p_child_inst_id" id="autocomplete-hidden-'.$row['id'].'" value="" />
				<input type="hidden" name="p_rel_id" value="'.$row['id'].'" />
				<input type="hidden" name="p_parent_inst_id" value="'.$p_inst_id.'" />
				<input type="hidden" name="p_tab" value="'.$tab_id.'" class="input_tabs" />
				</form>';
				$autocomplete_str.='<script type="text/javascript">
					$("#autocomplete-'.$row['id'].'").autocomplete({
						minLength: 3,';
						if ($row['max_length']!=0) $classes_id = $row['max_length'];
						else {
							$instance=new instances();
							$classes_id = $instance->get_multiples_id($row['id']);
						}
						$autocomplete_str.='source: "'.APP_BASE.'/autocomplete?p_relation_id='.$row['id'].'&p_inst_id='.$p_inst_id.'&p_parent_class_id='.$row['lookup_id'].'&p_child_class_id='.$classes_id.'&p_tab='.$tab_id.'", //change here the source of your values
							focus: function( event, ui ) {
								$("#autocomplete-'.$row['id'].'").val(ui.item.label);
								$("#autocomplete-hidden-'.$row['id'].'").val(ui.item.id);
								return false;
							},
							select: function(event, ui) {
								$("#autocomplete-'.$row['id'].'").val(ui.item.label);
								$("#autocomplete-hidden-'.$row['id'].'").val(ui.item.id);
								return false;
							},
							})
						.data("ui-autocomplete")._renderItem = function(ul, item) {
							return $("<li>")
							.append(function() {
								var html = \'<a href="javascript://">\' +
									item.label;
									if (item.status == "O") html += \'<span class="status publish" title="'.getMessage('info_word_status_pending').'"></span>\';
									else if (item.status == "V") html += \'<span class="status revised" title="'.getMessage('info_word_status_reviewed').'"></span>\';
									else if (item.status == "P") html += \'<span class="status pending" title="'.getMessage('info_word_status_pending').'"></span>\';
									html += \' [\' + item.className + \']\'
								\'</a>\';
								return html;
							})
							.appendTo(ul);
						};
				</script>';
				$autocomplete_header_str.='$("#autocomplete-form-'.$row['id'].'").on("submit", function(event) {
					var link = $(this).attr("action");
					$.ajax({
						url: link,
						type: "POST",
						data: $(this).serialize(),
						dataType: "html",
							success: function(html) {
								$("#tabrel'.$row['id'].'").html(html);
								$("#autocomplete-"+'.$row['rel_id'].').val("");
							}
				});
				return false;
				});';
			$ret.='</div>';
		}

		if (isset($_REQUEST[$prefix.$row['id'].$postfix])) {
			$trueValue = $_REQUEST[$prefix.$row['id'].$postfix];
        }elseif (isset($valor_simple['atrib_values'][0]['text_val'])) {
			$trueValue = htmlspecialchars($valor_simple['atrib_values'][0]['text_val']);
        }else {
			$trueValue = '';
        }

		if (isset($_REQUEST[$prefix.$row['id'].$postfix])) {
			$trueCleanedValue = $_REQUEST[$prefix.$row['id'].$postfix];
        }elseif (isset($valor_simple['atrib_values'][0]['text_val'])) {
			$trueCleanedValue = htmlspecialchars($valor_simple['atrib_values'][0]['text_val']);
        }else {
			$trueCleanedValue = '';
        }

		if (isset($_REQUEST[$prefix.$row['id'].$postfix])) {
			$trueDateValue = $_REQUEST[$prefix.$row['id'].$postfix];
        }elseif (isset($valor_simple['atrib_values'][0]['date_val'])) {
			$trueDateValue = $valor_simple['atrib_values'][0]['date_val'];
        }else {
			$trueDateValue = '';
        }

		if (isset($_REQUEST[$prefix.$row['id'].$postfix])) {
			$trueNumValue = $_REQUEST[$prefix.$row['id'].$postfix];
        }elseif (isset($valor_simple['atrib_values'][0]['num_val'])) {
			$trueNumValue = $valor_simple['atrib_values'][0]['num_val'];
        }else {
			$trueNumValue = '';
        }


		switch ($row['type']){
			case "S":/* String d'una linea */
				$ret .= $this->attribute_string($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueCleanedValue );
				break;
			case "B":/* String d'una linea ordenable a l'extracció */
				$ret .= $this->attribute_order_string($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueCleanedValue );
				break;
			case "O":/* Selector color */
                $ret .= $this->attribute_color($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueCleanedValue );
                break;
			case "A":/* Text Area WYSIWYG */
                $ret .= $this->attribute_textArea_WYSIWYG($p_mode, $i, $max_height, $prefix, $row, $postfix, $trueCleanedValue );
                break;
            case "K":/* Text Area CKEDITOR */
                $ret .= $this->attribute_ckeditor($p_mode, $i, $max_height, $prefix, $row, $postfix, $trueCleanedValue );
                break;
            case "T":/* Text Area HTML */
                $ret .= $this->attribute_textArea_HTML($p_mode, $i, $max_height, $prefix, $row, $postfix, $trueCleanedValue);
                break;
			case "C":/* Text Area Code*/
                $ret .= $this->attribute_textArea($p_mode, $i, $max_height, $prefix, $row, $postfix, $trueCleanedValue);
                break;
			case "Y": /* Video Youtube - Vimeo */
                $ret .= $this->attribute_youtube($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueCleanedValue, $valor_simple);
                break;
			case "I": /* Imatge */
                $ret .= $this->attribute_image($p_mode, $i, $img_w, $img_h, $prefix, $row, $postfix, $max_width, $trueValue, $valor_simple);
                break;
			case "F": /* File */
                $ret .= $this->attribute_file($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueValue);
                break;
			case "P": /* Private File */
                $ret .= $this->attribute_privatefile($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueValue);
                break;
			case "G": /* Flash File */
                $ret .= $this->attribute_flash_file($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueValue);
                break;
			case "M": /* Geoposicionament amb google Maps */
                $googlemaps = true;
                $ret .= $this->attribute_GeoMap($p_mode, $i, $prefix, $row, $postfix, $trueCleanedValue, $trueValue);
                break;
			case "U": /* URL */
                $ret .= $this->attribute_URL($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueCleanedValue);
                break;
			case "X": /* XML */
                $ret .= $this->attribute_XML($p_mode, $i, $max_height, $prefix, $row, $postfix, $trueCleanedValue);
                break;
			case "D": /* Date */
                $ret .= $this->attribute_Date($p_mode, $i, $prefix, $row, $postfix, $trueDateValue);
                break;
            case "E": /* Date ordenable a l'extracció*/
                $ret .= $this->attribute_order_Date($p_mode, $i, $prefix, $row, $postfix, $trueDateValue);
                break;
			case "N": /* Numeric */
                $ret .= $this->attribute_Numeric($p_mode, $i, $prefix, $row, $postfix, $max_width, $trueNumValue);
                break;
			case "R": /* Relation */
                $ret .= $this->attribute_Relation($p_mode, $p_inst_id, $tab_id, $row);
                break;
			case "L": /* Lookup */
                $ret .= $this->getAttributeLookup($row, isset($valor_simple['atrib_values'][0]['num_val']) ? $valor_simple['atrib_values'][0]['num_val'] : '' , $prefix, $postfix, $p_mode, $p_inst_id, $i, $row['description']);
                break;
			case "Z": /* niceurl */
                $ret .= $this->attribute_niceurl($p_mode, $i, $prefix, $row, $postfix, $max_width);
                break;
			case "W": /* Type APP */
                $ret .= $this->attribute_APP($p_mode, $i, $prefix, $row, $postfix, $valor_simple);
                break;

		}

		$ret.='<div class="clear"></div>';

		return $ret;
	}

	public function getRelationInstances($p_row, $p_parent_inst_id, $num=1000, $tab='', $rel_id='') {
		if (!isset($p_row['instances']) || (!is_array($p_row['instances']))) return '';

		$num_rows = count($p_row['instances']);
		$order_type = $p_row['info']['order_type'];
		$current_row = 0;
		$res="";

		foreach($p_row['instances'] as $row) {
			$res.='<tr id="'.$row['inst_id'].'">';
				if (count($p_row['instances'])>1 && $p_row['info']['order_type']=='M') {
					$res.='<td class="move_item">';
						$res.='<ul class="sortableitem" id="relid'.($row["id"]+1).'">';
							if ($order_type=='M') {//Pintem les fletxetes d'ordenacio manual
								if ($current_row==0) { //Es el primer
									$res.='<li class="mov_begin"><span>'.getMessage('info_word_ordertop').'</span></li>';
									$res.='<li class="mov_final"><a title="'.getMessage('info_word_orderbottom').'" href="'.APP_BASE.'/order_down_bottom?p_pagina=1&amp;p_relation_id='.$row['id'].'&amp;p_parent_inst_id='.$p_parent_inst_id.'&amp;p_parent_class_id='.$row['parent_class_id'].'&amp;p_tab='.$tab.'">'.getMessage('info_word_orderbottom').'</a></li>';
									$res.='<li class="mov_up sep"><span>'.getMessage('info_word_orderup').'</span></li>';
									$res.='<li class="mov_down"><a title="'.getMessage('info_word_orderdown').'" href="'.APP_BASE.'/order_down?p_pagina=1&amp;p_relation_id='.$row['id'].'&amp;p_parent_inst_id='.$p_parent_inst_id.'&amp;p_parent_class_id='.$row['parent_class_id'].'&amp;p_tab='.$tab.'">'.getMessage('info_word_orderdown').'</a></li>';
								}
								else {
									if($current_row==($num_rows-1)) { // Es l'ultim
										$res.='<li class="mov_begin"><a href="'.APP_BASE.'/order_up_top?p_pagina=1&amp;p_relation_id='.$row['id'].'&amp;p_parent_inst_id='.$p_parent_inst_id.'&amp;p_parent_class_id='.$row['parent_class_id'].'&amp;p_tab='.$tab.'" title="'.getMessage('info_word_ordertop').'">'.getMessage('info_word_ordertop').'</a></li>';
										$res.='<li class="mov_final"><span>'.getMessage('info_word_orderbottom').'</span></li>';
										$res.='<li class="mov_up sep"><a title="'.getMessage('info_word_orderup').'" href="'.APP_BASE.'/order_up?p_pagina=1&amp;p_relation_id='.$row['id'].'&amp;p_parent_inst_id='.$p_parent_inst_id.'&amp;p_parent_class_id='.$row['parent_class_id'].'&amp;p_tab='.$tab.'">'.getMessage('info_word_orderup').'</a></li>';
										$res.='<li class="mov_down"><span>'.getMessage('info_word_orderdown').'</span></li>';
									}
									else { // Cas normal, element que no es el primer ni l'ultim
										$res.='<li class="mov_begin"><a href="'.APP_BASE.'/order_up_top?p_pagina=1&amp;p_relation_id='.$row['id'].'&amp;p_parent_inst_id='.$p_parent_inst_id.'&amp;p_parent_class_id='.$row['parent_class_id'].'&amp;p_tab='.$tab.'" title="'.getMessage('info_word_ordertop').'">'.getMessage('info_word_ordertop').'</a></li>';
										$res.='<li class="mov_final"><a title="'.getMessage('info_word_orderbottom').'" href="'.APP_BASE.'/order_down_bottom?p_pagina=1&amp;p_relation_id='.$row['id'].'&amp;p_parent_inst_id='.$p_parent_inst_id.'&amp;p_parent_class_id='.$row['parent_class_id'].'&amp;p_tab='.$tab.'">'.getMessage('info_word_orderbottom').'</a></li>';
										$res.='<li class="mov_up sep"><a title="'.getMessage('info_word_orderup').'" href="'.APP_BASE.'/order_up?p_pagina=1&amp;p_relation_id='.$row['id'].'&amp;p_parent_inst_id='.$p_parent_inst_id.'&amp;p_parent_class_id='.$row['parent_class_id'].'&amp;p_tab='.$tab.'">'.getMessage('info_word_orderup').'</a></li>';
										$res.='<li class="mov_down"><a title="'.getMessage('info_word_orderdown').'" href="'.APP_BASE.'/order_down?p_pagina=1&amp;p_relation_id='.$row['id'].'&amp;p_parent_inst_id='.$p_parent_inst_id.'&amp;p_parent_class_id='.$row['parent_class_id'].'&amp;p_tab='.$tab.'">'.getMessage('info_word_orderdown').'</a></li>';
									}
								}
							}
						$res.='</ul>
					</td>';
				}
                $res.='<td>';
				$res.=parent::status_to_html($row['status']);
				$res.='</td>';
				$res.='<th scope="row"><span>'.$row['inst_id'].'</span></th>';
				$res.='<td class="instance"><a href="'.APP_BASE.'/view_instance?p_pagina=1&amp;p_class_id='.$row['child_class_id'].'&amp;p_inst_id='.$row['inst_id'].'" title="Ver '.$row['key_fields'].'">'.$row['key_fields'].'</a></td>';
				$res.='<td class="ico del"><a class="reldelete" href="'.APP_BASE.'/delete_relation_instance?p_pagina=1&amp;p_relation_id='.$row['id'].'&amp;p_class_id='.$row['parent_class_id'].'&amp;p_inst_id='.$p_parent_inst_id.'&amp;p_tab='.$tab.'&amp;p_rel_id='.$rel_id.'" title="'.getMessage('info_word_unjoin').'">'.getMessage('info_word_unjoin').'</a></td>';
				$res.='<td class="ico edi"><a href="'.APP_BASE.'/edit_instance?p_pagina=1&amp;p_class_id='.$row['child_class_id'].'&amp;p_inst_id='.$row['inst_id'].'" title="'.getMessage('info_word_edit').'">'.getMessage('info_word_edit').'</a></td>';
			$res.='</tr>';
			$current_row++;
		}

		return $res;
	}

	private function getDescription($row) {
		$ret = "";
        /* PINTA LA DIV AMB INFO PELS DESENVOLUPADORS */
		if (Session::get('user_type')=='O') {
			if ($row['type']=="R") {       //Informació del usuari OMATECH
				$ret='<li class="ico info_admin"><span class="info_alert alert_admin"><a href="#">Informació</a> <span class="info_bubble"><span class="arrow"></span><span class="text">ID-> '.$row["id"].'<br/>Type-> '.$row['type'].'<br/>Tag-> '.$row["tag"].'<br/>Name-> '.$row["name"].'<br/>Lang-> '.$row["language"].'<br/>Detail-> '.$row['cadetail'].'</span></span></span></li>';
			}
			else {
				if ($row['type']=='I') $ret='<li class="ico info_admin"><span class="info_alert alert_admin"><a href="#">Informació</a> <span class="info_bubble"><span class="arrow"></span><span class="text">ID-> '.$row["id"].'<br/>Type-> '.$row['type'].'<br/>Tag-> '.$row["tag"].'<br/>Lang-> '.$row["language"].'<br/>Detail-> '.$row['cadetail'].'<br/>Width-> '.$row["ai_width"].'<br/>Height-> '.$row["ai_height"].'</span></span></span></li>';
				else $ret='<li class="ico info_admin"><span class="info_alert alert_admin"><a href="#">Informació</a> <span class="info_bubble"><span class="arrow"></span><span class="text">ID-> '.$row["id"].'<br/>Type-> '.$row['type'].'<br/>Tag-> '.$row["tag"].'<br/>Lang-> '.$row["language"].'<br/>Detail-> '.$row['cadetail'].'</span></span></span></li>';
			}

			/*
			$ret.='<br/>Desc-> '.$row["caption"];
			$ret.='</div>';
                        */
		}
		/* FI PINTA LA DIV AMB INFO PELS DESENVOLUPADORS */

		/* PINTA LA DIV AMB INFO PELS USUSARIS */
		if (isset($row['description']) && $row['description']!='') {
			$ret.='<li class="ico info"><span class="info_alert alert_user"><a href="#">Información</a> <span class="info_bubble"><span class="arrow"></span><span class="text">'.nl2br($row['description']).'</span></span></span></li>';
		}
        /* FI PINTA LA DIV AMB INFO PELS USUSARIS */
		return $ret;
	}

	private function has_rec_cloner($p_class_id) {
		$sql = 'SELECT recursive_clone from omp_classes where id='.$p_class_id;
		$dbh=get_db_handler();
		$ret = mysql_query($sql, $dbh);

		if($ret){
			$row = mysql_fetch_array($ret, MYSQL_ASSOC);
			if($row['recursive_clone'] == 'Y')
				return true;
			else
				return false;
		}
	}



	/* ATTRIBUTES */

	/* String d'una linea */
	private function attribute_string($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueCleanedValue ){
        $ret = '';
        if ($p_mode=='I') {
            $ret ='<input class="w_250" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'"/>';
        }
		elseif($p_mode=='U') {
            $ret ='<input class="w_250" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'"/>';
        }
		elseif($p_mode=='V') {
            $ret ='<span class="label">'.$trueCleanedValue.'</span>';
        }

        return $ret;
	}

    /* String d'una linea ordenable a l'extracció */
    private function attribute_order_string($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueCleanedValue ){
        $ret = '';
        if ($p_mode=='I') {
            $ret ='<input class="w_250" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'"/>';
        }
		elseif($p_mode=='U') {
            $ret ='<input class="w_250" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'"/>';
        }
		elseif($p_mode=='V') {
            $ret ='<span class="label">'.$trueCleanedValue.'</span>';
        }

        return $ret;
    }


	/* Selector color */
	private function attribute_color($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueCleanedValue ){
        $ret = '';

		if ($p_mode=='I') {
            $ret = '<input class="w_250 color" tabindex="'.$i.'" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'"/>';
        }
		elseif($p_mode=='U') {
            $ret = '<input class="w_250 color" tabindex="'.$i.'" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'"/>';
        }
		elseif($p_mode=='V') {
            $ret = '<input class="w_200" tabindex="'.$i.'" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'" disabled="true" style="background-color: '.$trueCleanedValue.'" />';
        }

        return $ret;
	}

    /* Text Area WYSIWYG */
    private function attribute_textArea_WYSIWYG($p_mode, $i, $max_height, $prefix, $row, $postfix, $trueCleanedValue){
        $ret = '';

        $max_width=TEXTAREA_DEFAULT_LENGTH;
        if ($p_mode=='I') {
            $ret ='<textarea tabindex="'.$i.'" class="w_600" cols="'.$max_width.'" rows="'.$max_height.'" id="'.$prefix.$row['id'].$postfix.'" name="'.$prefix.$row['id'].$postfix.'">'.$trueCleanedValue.'</textarea>
            	<script type="text/javascript">$(\'#'.$prefix.$row['id'].$postfix.'\').markItUp(mySettings);</script>';
        }
		elseif($p_mode=='U') {
            $ret ='<textarea tabindex="'.$i.'" class="w_600" cols="'.$max_width.'" rows="'.$max_height.'" id="'.$prefix.$row['id'].$postfix.'" name="'.$prefix.$row['id'].$postfix.'">'.$trueCleanedValue.'</textarea>
            	<script type="text/javascript">$(\'#'.$prefix.$row['id'].$postfix.'\').markItUp(mySettings);</script>';
        }
		elseif($p_mode=='V') {
            $ret ='<textarea tabindex="'.$i.'" cols="'.$max_width.'" rows="'.$max_height.'" name="'.$prefix.$row['id'].$postfix.'" disabled="true">'.$trueCleanedValue.'</textarea>';
        }

        return $ret;
    }

    /* Text Area CKEDITOR */
    private function attribute_ckeditor($p_mode, $i, $max_height, $prefix, $row, $postfix, $trueCleanedValue ){
        $ret = '';

        if (file_exists(DIR_APLI_ADMIN.'/extras/ckeditor_config.js')) {
            $ckeditor_config = ", { customConfig: '/extras/ckeditor_config.js'}";
        }else{
            $ckeditor_config = "";
        }

        $max_width=TEXTAREA_CKEDITOR_DEFAULT_LENGTH;
        if ($p_mode=='I') {
            $ret ='<div style="width: 650px !important;"><textarea tabindex="'.$i.'" class="w_600" cols="'.$max_width.'" rows="'.$max_height.'" id="'.$prefix.$row['id'].$postfix.'" name="'.$prefix.$row['id'].$postfix.'">'.$trueCleanedValue.'</textarea></div>
            	<script type="text/javascript">CKEDITOR.replace( \''.$prefix.$row['id'].$postfix.'\''.$ckeditor_config.');</script>';
        }
		elseif($p_mode=='U') {
            $ret ='<div style="width: 650px !important;"><textarea tabindex="'.$i.'" class="w_600" cols="'.$max_width.'" rows="'.$max_height.'" id="'.$prefix.$row['id'].$postfix.'" name="'.$prefix.$row['id'].$postfix.'">'.$trueCleanedValue.'</textarea></div>
            	<script type="text/javascript">CKEDITOR.replace( \''.$prefix.$row['id'].$postfix.'\''.$ckeditor_config.');</script>';
        }
		elseif($p_mode=='V') {
            $ret ='<div style="width: 650px !important;"><textarea tabindex="'.$i.'" cols="'.$max_width.'" rows="'.$max_height.'" name="'.$prefix.$row['id'].$postfix.'" disabled="true">'.$trueCleanedValue.'</textarea></div>
            	<script type="text/javascript">CKEDITOR.replace( \''.$prefix.$row['id'].$postfix.'\''.$ckeditor_config.');				</script>';
        }

        return $ret;
    }

    /* Text Area HTML */
    private function attribute_textArea_HTML($p_mode, $i, $max_height, $prefix, $row, $postfix, $trueCleanedValue){
        $ret = '';

        $max_width=TEXTAREA_DEFAULT_LENGTH;
        if ($p_mode=='I') {
            $ret ='<textarea tabindex="'.$i.'" class="w_650" cols="'.$max_width.'" rows="'.$max_height.'" id="'.$prefix.$row['id'].$postfix.'" name="'.$prefix.$row['id'].$postfix.'">'.$trueCleanedValue.'</textarea>';
        }
		elseif($p_mode=='U') {
            $ret ='<textarea tabindex="'.$i.'" class="w_650" cols="'.$max_width.'" rows="'.$max_height.'" id="'.$prefix.$row['id'].$postfix.'" name="'.$prefix.$row['id'].$postfix.'">'.$trueCleanedValue.'</textarea>';
        }
		elseif($p_mode=='V') {
            $ret ='<textarea tabindex="'.$i.'" cols="120" rows="'.$max_height.'" name="'.$prefix.$row['id'].$postfix.'" disabled="true">'.$trueCleanedValue.'</textarea>';
        }

        return $ret;
    }

    /* Text Area */
    private function attribute_textArea($p_mode, $i, $max_height, $prefix, $row, $postfix, $trueCleanedValue){
        $ret = '';

        $max_width=TEXTAREA_DEFAULT_LENGTH;
        if ($p_mode=='I') {
            $ret ='<textarea tabindex="'.$i.'" cols="'.$max_width.'" rows="'.$max_height.'" id="'.$prefix.$row['id'].$postfix.'" name="'.$prefix.$row['id'].$postfix.'">'.$trueCleanedValue.'</textarea>
            <script type="text/javascript">$(\'#'.$prefix.$row['id'].$postfix.'\').markItUp(mySettings);</script>';
        }
		elseif($p_mode=='U') {
            $ret ='<textarea tabindex="'.$i.'" cols="'.$max_width.'" rows="'.$max_height.'" id="'.$prefix.$row['id'].$postfix.'" name="'.$prefix.$row['id'].$postfix.'">'.$trueCleanedValue.'</textarea>
            	<script type="text/javascript">$(\'#'.$prefix.$row['id'].$postfix.'\').markItUp(mySettings);</script>';
        }
		elseif($p_mode=='V') {
            $ret ='<textarea tabindex="'.$i.'" cols="'.$max_width.'" rows="'.$max_height.'" name="'.$prefix.$row['id'].$postfix.'" disabled="true">'.$trueCleanedValue.'</textarea>';
        }

        return $ret;
    }

    /* Video Youtube - Vimeo */
    private function attribute_youtube($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueCleanedValue, $valor_simple){
        $ret = '';

        if ($p_mode=='I') {
            $ret ='<input class="w_250" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'"/>';
        }
		elseif($p_mode=='U') {
            $ret ='<input class="w_250" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'"/>';
        }
		elseif($p_mode=='V') {
            if (isset($valor_simple['atrib_values'][0])) {
                $arg_vid = explode(':', $trueCleanedValue);
                $id_video = $arg_vid[1];

                if(($arg_vid[0]=='vimeo')){
                    $video_url =  "https://player.vimeo.com/video/".$id_video;
                }else if(($arg_vid[0]=='youtube')){
                    $video_url =  "https://www.youtube.com/embed/".$id_video;
                }else {
                    $video_url =  $trueCleanedValue;
                }

                $ret ='<span class="label">'.$trueCleanedValue.'</span> <a href="'.$video_url.'" data-featherlight="iframe" data-featherlight-iframe-height="450px" data-featherlight-iframe-width="800px"><i class="fa fa-eye fa-lg" aria-hidden="true"></i>
</a>';
            }
        }

        return $ret;
    }

    /* Imatge */
    private function attribute_image($p_mode, $i, $img_w, $img_h, $prefix, $row, $postfix, $max_width, $trueValue, $valor_simple){
        $ret = '';

        if ($p_mode=='I') {
            $ret ='<div>';
            $ret.='<input class="w_200 float_left" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" id="'.$prefix.$row['id'].$postfix.'" value="'.$trueValue.'" />';
            $ret.='<div id="div'.$prefix.$row['id'].$postfix.'"></div>';
            $this->javascript_attributes .= '$("#div'.$prefix.$row['id'].$postfix.'").uploadFile({
						url:"'.APP_BASE.'/upload_crop",
						fileName:"myfile",
						multiple:false,
						autoSubmit:true,
						formData: {"p_width":"'.$img_w.'","p_height":"'.$img_h.'","input_name":"'.$prefix.$row['id'].$postfix.'"},
						maxFileSize:1024*1024*1024*100 /* 100GB */,
						returnType:"json",
						showFileCounter:false,
						showCancel:true,
						showAbort:true,
						showDone:false,
						showStatusAfterSuccess:false,
						onSuccess:function(files, data, xhr) {
							var id = "'.$prefix.$row['id'].$postfix.'";
							crop_actions(id, data, \'image\');
						}
					});';
            $ret.='<a class="ico lupa" href="javascript://" onclick="browseImage(\''.$prefix.$row['id'].$postfix.'\');"></a>';
            $ret.='<div class="clear"></div>
				</div>';

            $ret.='<div class="photo">';
            $ret.='<img id="img'.$prefix.$row['id'].$postfix.'" class="clone_drag_img" alt="" src="'.APP_BASE.'/images/noimage.png"/>';
            $ret.='<dl>
						<dt>'.getMessage('theoric_size').':</dt>
							<dd><span id="wt'.$prefix.$row['id'].$postfix.'">'.$img_w.'</span>x<span id="ht'.$prefix.$row['id'].$postfix.'">'.$img_h.'</span></dd>
						<dt>'.getMessage('real_size').':</dt>
							<dd><span id="w'.$prefix.$row['id'].$postfix.'"></span>x<span id="h'.$prefix.$row['id'].$postfix.'"></span></dd>';
            $ret.='</dl>';
            $ret.='</div>';
        }
        if ($p_mode=='U') {
            $ret ='<div>';
            $ret.='<input  class="w_200 float_left" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" id="'.$prefix.$row['id'].$postfix.'" value="'.$trueValue.'"/>';
            $ret.='<div id="div'.$prefix.$row['id'].$postfix.'"></div>';
            $this->javascript_attributes .= '$("#div'.$prefix.$row['id'].$postfix.'").uploadFile({
						url:"'.APP_BASE.'/upload_crop",
						fileName:"myfile",
						multiple:false,
						autoSubmit:true,
						formData: {"p_width":"'.$img_w.'","p_height":"'.$img_h.'","input_name":"'.$prefix.$row['id'].$postfix.'"},
						maxFileSize:1024*1024*1024*100 /* 100GB */,
						returnType:"json",
						showFileCounter:false,
						showCancel:true,
						showAbort:true,
						showDone:false,
						showStatusAfterSuccess:false,
						onSuccess:function(files, data, xhr) {
							var id = "'.$prefix.$row['id'].$postfix.'";
							crop_actions(id, data, \'image\');
						}
					});';
            if (Session::get('user_type')=='O'){
                $ret.='<a class="ico lupa" href="javascript://" onclick="browseImage(\''.$prefix.$row['id'].$postfix.'\');"></a>';
            }
            $ret.='<div class="clear"></div>
				</div>';

            if (!empty($trueValue)) {
                $mida_real=explode('.',$valor_simple['atrib_values'][0]['img_info']);
                $mides=explode('(',$row['caption']);
            }

            $ret.='<div class="photo">';
            $ret.='<img id="img'.$prefix.$row['id'].$postfix.'" class="clone_drag_img" alt="" src="';
            if (isset($trueValue) && $trueValue!='') $ret.=$trueValue.'?'.time();
            else $ret.=APP_BASE.'/images/noimage.png';
            $ret.='"/>';
            $ret.='<dl>
						<dt>'.getMessage('theoric_size').':</dt>
							<dd><span id="wt'.$prefix.$row['id'].$postfix.'">'.$img_w.'</span>x<span id="ht'.$prefix.$row['id'].$postfix.'">'.$img_h.'</span></dd>
						<dt>'.getMessage('real_size').':</dt>';
            if (($img_w!=$mida_real[0] && $img_w!=null) || ($img_h!=$mida_real[1] && $img_h!=null)) $ret.='<dd class="mida_ko"><span id="w'.$prefix.$row['id'].$postfix.'">'.$mida_real[0].'</span>x<span id="h'.$prefix.$row['id'].$postfix.'">'.$mida_real[1].'</span></dd>';
            else $ret.='<dd class="real"><span id="w'.$prefix.$row['id'].$postfix.'">'.$mida_real[0].'</span>x<span id="h'.$prefix.$row['id'].$postfix.'">'.$mida_real[1].'</span></dd>';
            $ret.='</dl>';
            $ret.='</div>';
        }
        if ($p_mode=='V') {
            $ret = '<span class="label">';
            if (isset($trueValue) && $trueValue!=''){
                if(substr($trueValue, 0,1)!='/'){$trueValue = $trueValue;}
                $ret.=$trueValue;
            }else{
                $ret.='&nbsp;';
            }
            $ret.='</span>';

            $mida_real=explode('.',$valor_simple['atrib_values'][0]['img_info']);
            $mides=explode('(',$row['caption']);
            $ret.= ' <div class="photo">';
            if (isset($trueValue) && $trueValue!=''){
                $ret.='<a href="'.$trueValue.'?'.time().'" data-featherlight="image"><img alt="" src="'.$trueValue.'?'.time().'" /></a>';
            }else{
                $ret.='<img alt="" src="'.APP_BASE.'/images/noimage.png"/>';
            }

            $ret.='<div>
						<dl>
							<dt>'.getMessage('theoric_size').':</dt>
								<dd>'.$img_w.'x'.$img_h.'</dd>
							<dt>'.getMessage('real_size').':</dt>';
            if (($img_w!=$mida_real[0] && $img_w!=null) || ($img_h!=$mida_real[1] && $img_h!=null)) $ret.='<dd class="mida_ko">'.$mida_real[0].'x'.$mida_real[1].'</dd>';
            else $ret.='<dd class="real">'.$mida_real[0].'x'.$mida_real[1].'</dd>';
            $ret.='</dl>
					</div>';
            $ret.='</div>';
        }

        return $ret;
    }

    /* File */
    private function attribute_file($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueValue){
        $ret = '';

        if ($p_mode=='I') {
            $ret.= '<input class="w_200 float_left" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" id="'.$prefix.$row['id'].$postfix.'" value="'.$trueValue.'" />';
            $ret.='<div id="div'.$prefix.$row['id'].$postfix.'"></div>';
            $this->javascript_attributes .= '$("#div'.$prefix.$row['id'].$postfix.'").uploadFile({
					url:"'.APP_BASE.'/upload_crop",
					fileName:"myfile",
					multiple:false,
					autoSubmit:true,
					formData: {"input_name":"'.$prefix.$row['id'].$postfix.'"},
					maxFileSize:1024*1024*1024*100 /* 100GB */,
					returnType:"json",
					showFileCounter:false,
					showCancel:true,
					showAbort:true,
					showDone:false,
					showStatusAfterSuccess:false,
					onSuccess:function(files, data, xhr) {
						var id = "'.$prefix.$row['id'].$postfix.'";
						crop_actions(id, data, \'file\');
					}
				});';
            //$ret.= '<a class="ico clip" href="javascript:show_upload(\'Form1.'.$prefix.$row['id'].$postfix.'\');"><!--img class="icon" src="'.ICONO_UPLOAD.'" border="0" title="Seleccionar archivo"--></a>';
        }
        if ($p_mode=='U') {
            $ret.= '<input class="w_200 float_left" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" id="'.$prefix.$row['id'].$postfix.'" value="'.$trueValue.'"/>';
            $ret.='<div id="div'.$prefix.$row['id'].$postfix.'"></div>';
            $this->javascript_attributes .= '$("#div'.$prefix.$row['id'].$postfix.'").uploadFile({
					url:"'.APP_BASE.'/upload_crop",
					fileName:"myfile",
					multiple:false,
					autoSubmit:true,
					formData: {"input_name":"'.$prefix.$row['id'].$postfix.'"},
					maxFileSize:1024*1024*1024*100 /* 100GB */,
					returnType:"json",
					showFileCounter:false,
					showCancel:true,
					showAbort:true,
					showDone:false,
					showStatusAfterSuccess:false,
					onSuccess:function(files, data, xhr) {
						var id = "'.$prefix.$row['id'].$postfix.'";
						crop_actions(id, data, \'file\');
					}
				});';
            //$ret.= '<a class="ico clip" href="javascript:show_upload(\'Form1.'.$prefix.$row['id'].$postfix.'\');"><!--img class="icon" src="'.ICONO_UPLOAD.'" border="0" title="Seleccionar archivo"--></a>';
        }
        if ($p_mode=='V') {
            if ($trueValue!='' && (strpos($trueValue,'http://')!==false || strpos($trueValue,'www')!==false)) {
                $ret.= '<span class="label">'.$trueValue.'</span>';
            }
			elseif ($trueValue!='') {
                $ret.= '<span class="label">'.$trueValue.'</span>';
            }
        }
        return $ret;
    }

	/* Private File */
	private function attribute_privatefile($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueValue){
		$ret = '';

		if ($p_mode=='I') {
			$ret.= '<input class="w_200 float_left" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" id="'.$prefix.$row['id'].$postfix.'" value="'.$trueValue.'" />';
			$ret.='<div id="div'.$prefix.$row['id'].$postfix.'"></div>';
			$this->javascript_attributes .= '$("#div'.$prefix.$row['id'].$postfix.'").uploadFile({
					url:"'.APP_BASE.'/upload_private_crop",
					fileName:"myfile",
					multiple:false,
					autoSubmit:true,
					formData: {"input_name":"'.$prefix.$row['id'].$postfix.'"},
					maxFileSize:1024*1024*1024*100 /* 100GB */,
					returnType:"json",
					showFileCounter:false,
					showCancel:true,
					showAbort:true,
					showDone:false,
					showStatusAfterSuccess:false,
					onSuccess:function(files, data, xhr) {
						var id = "'.$prefix.$row['id'].$postfix.'";
						crop_actions(id, data, \'file\');
					}
				});';
			//$ret.= '<a class="ico clip" href="javascript:show_upload(\'Form1.'.$prefix.$row['id'].$postfix.'\');"><!--img class="icon" src="'.ICONO_UPLOAD.'" border="0" title="Seleccionar archivo"--></a>';
		}
		if ($p_mode=='U') {
			$ret.= '<input class="w_200 float_left" tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" id="'.$prefix.$row['id'].$postfix.'" value="'.$trueValue.'"/>';
			$ret.='<div id="div'.$prefix.$row['id'].$postfix.'"></div>';
			$this->javascript_attributes .= '$("#div'.$prefix.$row['id'].$postfix.'").uploadFile({
					url:"'.APP_BASE.'/upload_private_crop",
					fileName:"myfile",
					multiple:false,
					autoSubmit:true,
					formData: {"input_name":"'.$prefix.$row['id'].$postfix.'"},
					maxFileSize:1024*1024*1024*100 /* 100GB */,
					returnType:"json",
					showFileCounter:false,
					showCancel:true,
					showAbort:true,
					showDone:false,
					showStatusAfterSuccess:false,
					onSuccess:function(files, data, xhr) {
						var id = "'.$prefix.$row['id'].$postfix.'";
						crop_actions(id, data, \'file\');
					}
				});';
			//$ret.= '<a class="ico clip" href="javascript:show_upload(\'Form1.'.$prefix.$row['id'].$postfix.'\');"><!--img class="icon" src="'.ICONO_UPLOAD.'" border="0" title="Seleccionar archivo"--></a>';
		}
		if ($p_mode=='V') {
			if ($trueValue!='' && (strpos($trueValue,'http://')!==false || strpos($trueValue,'www')!==false)) {
				$ret.= '<span class="label">'.$trueValue.'</span>';
			}
			elseif ($trueValue!='') {
				$ret.= '<span class="label">'.$trueValue.'</span>';
			}
		}
		return $ret;
	}

    /* Flash file */
    private function attribute_flash_file($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueValue){
        $ret = '';

        if ($p_mode=='I') {
            $ret.= '<input tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" id="'.$prefix.$row['id'].$postfix.'" value="'.$trueValue.'" />';
            $ret.='<div id="div'.$prefix.$row['id'].$postfix.'"></div>';
            $this->javascript_attributes .= '$("#div'.$prefix.$row['id'].$postfix.'").uploadFile({
					url:"'.APP_BASE.'/upload_crop",
					fileName:"myfile",
					multiple:false,
					autoSubmit:true,
					formData: {"input_name":"'.$prefix.$row['id'].$postfix.'"},
					maxFileSize:1024*1024*1024*100 /* 100GB */,
					returnType:"json",
					showFileCounter:false,
					showCancel:true,
					showAbort:true,
					showDone:false,
					showStatusAfterSuccess:false,
					onSuccess:function(files, data, xhr) {
						var id = "'.$prefix.$row['id'].$postfix.'";
						crop_actions(id, data, \'file\');
					}
				});';
            //$ret.= '<a class="ico clip" href="javascript:show_upload(\'Form1.'.$prefix.$row['id'].$postfix.'\');"><!--img class="icon" src="'.ICONO_UPLOAD.'" border="0" title="Seleccionar archivo"--></a>';
        }
        if ($p_mode=='U') {
            $ret .= '<input tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" id="'.$prefix.$row['id'].$postfix.'" value="'.$trueValue.'"/>';
            $ret.='<div id="div'.$prefix.$row['id'].$postfix.'"></div>';
            $this->javascript_attributes .= '$("#div'.$prefix.$row['id'].$postfix.'").uploadFile({
					url:"'.APP_BASE.'/upload_crop",
					fileName:"myfile",
					multiple:false,
					autoSubmit:true,
					formData: {"input_name":"'.$prefix.$row['id'].$postfix.'"},
					maxFileSize:1024*1024*1024*100 /* 100GB */,
					returnType:"json",
					showFileCounter:false,
					showCancel:true,
					showAbort:true,
					showDone:false,
					showStatusAfterSuccess:false,
					onSuccess:function(files, data, xhr) {
						var id = "'.$prefix.$row['id'].$postfix.'";
						crop_actions(id, data, \'file\');
					}
				});';
            //$ret.= '<a class="ico clip" href="javascript:show_upload(\'Form1.'.$prefix.$row['id'].$postfix.'\');"><!--img class="icon" src="'.ICONO_UPLOAD.'" border="0" title="Seleccionar archivo"--></a>';
        }
        if ($p_mode=='V') {
            if (isset($valor_simple['atrib_values'][0]['text_val'])) {
                $ret.= '<span class="label">'.$trueValue.'</span>';
                $ret.= '<embed src="'.URL_APLI.$trueValue.'" quality="high" width="100" height="100"></embed>';
            }
        }

        return $ret;
    }

    /* Geoposicionament amb google Maps */
    private function attribute_GeoMap($p_mode, $i, $prefix, $row, $postfix, $trueCleanedValue, $trueValue){
        $ret = '';
        if ($p_mode=='I') {
            $ret.= '<input tabindex="'.$i.'" type="text" id="cerca_posicio" size="40"/>
				<br />
				<input type="text" disabled="disabled" id="latitud" value=""/> <input type="text" disabled="disabled" id="longitud" value=""/>
				<input type="hidden" name="'.$prefix.$row['id'].$postfix.'" value="" id="position_lat_long"/>
				<p class="btn"><input type="button" value="Buscar" onclick="recalc_gmaps();"/></p>
                                <div id="map_canvas" style="width:310px;height:220px;margin-top:15px;"></div>';
            $ret.= '<script type="text/javascript">
					$(document).ready(function(){
						posicionar(41.387917, 2.1699187);
					});
				</script>';
        }
        if ($p_mode=='U') {
            $pos2=explode("@",$trueValue);
            $pos=explode(":",$pos2[0]);
            $ret .= '<input tabindex="'.$i.'" type="text" id="cerca_posicio" value="'.$pos2[1].'"/ size="40">
				<br />
				<input type="text" disabled="disabled" id="latitud" value="'.$pos[0].'" size="20" style="float:left; margin-right:5px;"/>
                                <input type="text" disabled="disabled" id="longitud" value="'.$pos[1].'" size="20" style="float:left;"/>
				<p class="btn"><input type="button" value="Buscar" onclick="recalc_gmaps();"/></p>
				<div id="map_canvas" style="width:310px;height:220px;margin-top:15px;"></div>
				<input type="hidden" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'" id="position_lat_long"/>';
            $ret .= '<script type="text/javascript">
					$(document).ready(function(){
						posicionar('.$pos[0].', '.$pos[1].');
					});
				</script>';
        }
        if ($p_mode=='V') {
            $pos2=explode("@",$trueValue);
            $pos=explode(":",$pos2[0]);
            $ret.= '<p>('.$pos[0].' , '.$pos[1].')';
            if($pos2[1]!="") $ret .= ' @ '.$pos2[1];
            $ret.= '</p><div id="map_canvas" style="width:310px;height:220px;margin-top:15px;"></div>';
            $ret.= '<script type="text/javascript">
					$(document).ready(function(){
						posicionar('.$pos[0].', '.$pos[1].');
					});
				</script>';
        }

        return $ret;
    }

    /* URL */
    private function attribute_URL($p_mode, $i, $max_width, $prefix, $row, $postfix, $trueCleanedValue){
        $ret = '';

        if ($p_mode=='I') {
            $ret .= '<input tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'" />';
        }
        if ($p_mode=='U') {
            $ret .= '<input tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueCleanedValue.'"/>';
        }
        if ($p_mode=='V') {
            if ($trueCleanedValue!='' && (strpos($trueCleanedValue,'http://')!==false || strpos($trueCleanedValue,'https://')!==false || strpos($trueCleanedValue,'www')!==false)) {
                $ret.= '<span class="label"><a title="'.$trueCleanedValue.'" href="'.$trueCleanedValue.'" target="_blank">'.$trueCleanedValue.'</a></span>';
            }
			elseif($trueCleanedValue!='') {
                $ret.= '<span class="label"><a title="'.$trueCleanedValue.'" href="'.URL_APLI.$trueCleanedValue.'" target="_blank">'.$trueCleanedValue.'</a></span>';
            }
        }

        return $ret;
    }

    /* XML */
    private function attribute_XML($p_mode, $i, $max_height, $prefix, $row, $postfix, $trueCleanedValue){
        $ret = '';

        $max_width=TEXTAREA_DEFAULT_LENGTH;
        if ($p_mode=='I') {
            $ret .= '<textarea tabindex="'.$i.'" cols="'.$max_width.'" rows="'.$max_height.'" name="'.$prefix.$row['id'].$postfix.'">'.$trueCleanedValue.'</textarea>';
        }
        if ($p_mode=='U') {
            $ret .= '<textarea tabindex="'.$i.'" cols="'.$max_width.'" rows="'.$max_height.'" name="'.$prefix.$row['id'].$postfix.'">'.$trueCleanedValue.'</textarea>';
        }
        if ($p_mode=='V') {
            $ret.='<span class="label">'.$trueCleanedValue.'</span>';
        }

        return $ret;
    }

    /* Date */
    private function attribute_Date($p_mode, $i, $prefix, $row, $postfix, $trueDateValue){
        $ret = '';

        if ($p_mode=='I') {
            $ret.='<input tabindex="'.$i.'" type="text" size="30" name="'.$prefix.$row['id'].$postfix.'" class="date3 datepicker" value="'.$trueDateValue.'" />';
        }
        if ($p_mode=='U') {
            $ret.='<input tabindex="'.$i.'" type="text" size="30" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueDateValue.'" class="date3 datepicker"/>';
        }
        if ($p_mode=='V') {
            $ret.='<span class="label">'.$trueDateValue.'</span>';
        }

        return $ret;
    }

    /* Date ordenable a l'extracció */
    private function attribute_order_Date($p_mode, $i, $prefix, $row, $postfix, $trueDateValue){
        $ret = '';

        if ($p_mode=='I') {
            $ret.='<input tabindex="'.$i.'" type="text" size="30" name="'.$prefix.$row['id'].$postfix.'" class="date3 datepicker" value="'.$trueDateValue.'" />';
        }
        if ($p_mode=='U') {
            $ret.='<input tabindex="'.$i.'" type="text" size="30" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueDateValue.'" class="date3 datepicker"/>';
        }
        if ($p_mode=='V') {
            $ret.='<span class="label">'.$trueDateValue.'</span>';
        }

        return $ret;
    }

    /* Numeric */
    private function attribute_Numeric($p_mode, $i, $prefix, $row, $postfix, $max_width, $trueNumValue){
        $ret = '';

        if ($p_mode=='I') {
            $ret.='<input tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueNumValue.'" />';
        }
        if ($p_mode=='U') {
            $ret.='<input tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$trueNumValue.'"/>';
        }
        if ($p_mode=='V') {
            $ret.='<span class="label">'.$trueNumValue.'</span>';
        }

        return $ret;
    }

    /* Relation */
    private function attribute_Relation($p_mode, $p_inst_id, $tab_id, $row){
        $ret = '';

        if ($p_mode=='V') {
            $ret .= '<div class="col_item alert list alert_right hidden">
				            <span></span>
				            <div><p></p></div>
				            <p class="btn_close"><a title="Tancar" class="close">Tancar</a></p>
				        </div>';
            $ret.='<div class="tbl_rel tbl_items" id="divrel'.$row['id'].'" inst_id="'.$p_inst_id.'">';
            $ret.='<table cellspacing="0" cellpadding="0" border="0" width="95%">';
            if (count($row['related_instances']['instances'])>1 && $row['related_instances']['info']['order_type']=='M') $ret.='<col class="w_70">';
            $ret.='<col class="w_20">
						<col class="w_20">
						<col>
						<col class="w_25">
						<col class="w_25">
						<thead>
							<tr class="thead">';
            if (count($row['related_instances']['instances'])>1 && $row['related_instances']['info']['order_type']=='M') $ret.='<th scope="col"><span>Moure ítem</span></th>';
            $ret.='<th scope="col"><span>Estat</span></th>
								<th scope="col"><span>Identificador</span></th>
								<th scope="col"><span>Títol</span></th>
								<th scope="col"><span>Editar</span></th>
								<th scope="col"><span>Esborrar</span></th>
							</tr>
						</thead>
						<tbody id="tabrel'.$row['id'].'">';
            $ret.=$this->getRelationInstances($row['related_instances'], $p_inst_id, 1000, $tab_id, $row['id']);
            $ret.='</tbody>
					</table>
				</div>';
        }
        else {
            $ret.='<div class="tbl_rel tbl_items" id="divrel'.$row['id'].'" inst_id="'.$p_inst_id.'">';
            $ret.='<table cellspacing="0" cellpadding="0" border="0" width="95%">';
            if (count($row['related_instances']['instances'])>1 && $row['related_instances']['info']['order_type']=='M') $ret.='<col class="w_70">';
            $ret.='<col class="w_20">
						<col class="w_20">
						<col>
						<col class="w_25">
						<col class="w_25">
						<thead>
							<tr class="thead">';
            if (count($row['related_instances']['instances'])>1 && $row['related_instances']['info']['order_type']=='M') $ret.='<th scope="col"><span>Moure ítem</span></th>';
            $ret.='<th scope="col"><span>Estat</span></th>
								<th scope="col"><span>Identificador</span></th>
								<th scope="col"><span>Títol</span></th>
								<th scope="col"><span>Editar</span></th>
								<th scope="col"><span>Esborrar</span></th>
							</tr>
						</thead>
						<tbody id="tabrel'.$row['id'].'">';
            $ret.='</tbody>
					</table>
				</div>';
        }

        return $ret;
    }

    /* Niceurl */
    private function attribute_niceurl($p_mode, $i, $prefix, $row, $postfix, $max_width){
        $ret = '';

        if ($p_mode=='I') {
            $urlnice = '';
            $ret.='<input tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$urlnice.'" />';
        }
		elseif($p_mode=='U') {
            $urlnice=$row['niceurl'];
            $ret .= '<input tabindex="'.$i.'" type="text" size="'.$max_width.'" name="'.$prefix.$row['id'].$postfix.'" value="'.$urlnice.'"/>';
        }
		elseif($p_mode=='V') {
            $urlnice=$row['niceurl'];
            $ret.='<p class="string_text">'.$urlnice.'</p>';
            if (isset($urlnice) && $urlnice!='') {
                if (defined('PREVIEW_LARAVEL') && PREVIEW_LARAVEL==true){
                    $url_preview = '/preview/'.$row['language'].'/'.$urlnice;
                }else{
                    $url_preview = '/'.$row['language'].'/'.$urlnice.'?req_info=1';
                }
                $ret.='<ul class="icos_list float_left"><li class="ico prev"><a title="Previsualizar" href="'.$url_preview.'" target="_blank">Previsualizar</a></li></ul>';
            }
        }

        return $ret;
    }

    /* Niceurl */
    private function attribute_APP($p_mode, $i, $prefix, $row, $postfix, $valor_simple){
        $ret = '';

        if ($p_mode=='I') {
            $ret.='<input tabindex="'.$i.'" type="text" id="store_url" class="w_200 float_left" name="'.$prefix.$row['id'].$postfix.'" /> ';
            $ret.="<a class='ico lupa' href='javascript://' onclick='Appbrowser(\"".$prefix.$row['id'].$postfix."\");'></a>";
        }
		elseif($p_mode=='U') {
            if(json_decode($valor_simple['atrib_values'][0]['text_val']) != null){
                $appdata = json_decode($valor_simple['atrib_values'][0]['text_val']);
                if(!empty($appdata->General[0])):
                    $appURL = $appdata->General[0]->app_store_url;
                else:
                    $appURL = $appdata->trackViewUrl;
                endif;
            }
            $ret.='<input tabindex="'.$i.'" type="text" id="store_url" class="w_200 float_left" name="'.$prefix.$row['id'].$postfix.'" value="'.$appURL.'"/> ';
            $ret.="<a class='ico lupa' href='javascript://' onclick='Appbrowser(\"".$prefix.$row['id'].$postfix."\");'></a>";
        }
		elseif($p_mode=='V') {
            if(json_decode($valor_simple['atrib_values'][0]['text_val']) != null) {
                $appdata = json_decode($valor_simple['atrib_values'][0]['text_val']);
                //---- App from google play ---
                if(!empty($appdata->General[0])) {
                    $ret.='<span class="label">'.$appdata->General[0]->app_store_url.'</span>';
                    $ret.= '<div class="thumbnail">
							<img  class="imgAppdesc"  src="'.$appdata->General[0]->banner_icon.'">
							<div class="caption">
								<h3>'.$appdata->General[0]->app_title.'</h3>
							</div>
						</div>';
                }
                //---- App form iTunes
                else {
                    $ret.='<span class="label">'.$appdata->trackViewUrl.'</span>';
                    $ret.= '<div class="thumbnail">
							<img class="imgAppdesc" style="width: 60px; float:left; margin-bottom: 10px; margin-right: 20px;" src="'.$appdata->artworkUrl512.'">
							<div class="caption">
								<h3>'.$appdata->trackName.'</h3>
							</div>
						</div>';
                }
            }
            else {
                print_r(json_last_error());
                echo "format ERROR";
            }
        }

        return $ret;
    }

    /* Lookup */
    private function getAttributeLookup($p_row, $p_valor, $p_prefix, $p_postfix, $p_mode, $p_inst_id, $i, $desc) {
        $ret="";

        if ($p_mode=='V') { // Mode view, no necessitem tots els valors possibles, nomes els que estan informats
            foreach ($p_row['lookup_info']['selected_values'] as $r) {
                if (isset($r)) {
                    $ret.='<input type="text" class="w_180 disabled" readonly="readonly" disabled="disabled" value="'.$r['label'].'" size="20" id="" name="">';
                    //$ret.='<span class="omp_field">'.$r['label'].'</span><br />';
                    $valors[]=$r['id'];
                }
            }
            return $ret;
        }
        else { // Mode insert o update
            //$ret=$this->getDescription($p_row);
            //$ret.="<br />";
            $lg = Session::get('u_lang');

            $lookup_rows = $p_row['lookup_info'];
            if ($lookup_rows['info']['type']=="L") {
                $ret.='<select tabindex="'.$i.'" class="omp_field" name="'.$p_prefix.$p_row['id'].$p_postfix.'">';
                //$ret .= '<option class="omp_field" value="">&nbsp;</option>';

                $default=0;
                foreach ($lookup_rows['lookup_all_values'] as $row) {
                    if ($p_mode=='I') {
                        if (isset ($_REQUEST[$p_prefix.$p_row['id'].$p_postfix]) && $_REQUEST[$p_prefix.$p_row['id'].$p_postfix]==$row['lookup_value_id']) $ret .= '<option class="omp_field" value="'.$row['lookup_value_id'].'" selected="selected">'.$row['label'].'</option>';
						elseif ($lookup_rows['info']['default_id']==$row['lookup_value_id']) $ret.='<option class="omp_field" value="'.$row['lookup_value_id'].'" selected="selected">'.$row['label'].'</option>';
                        else $ret .= '<option class="omp_field" value="'.$row['lookup_value_id'].'">'.$row['label'].'</option>';
                    }
                    if ($p_mode=='U') {
                        if (isset ($_REQUEST[$p_prefix.$p_row['id'].$p_postfix]) && $_REQUEST[$p_prefix.$p_row['id'].$p_postfix]==$row['lookup_value_id']) $ret .= '<option class="omp_field" value="'.$row['lookup_value_id'].'" selected="selected">'.$row['label'].'</option>';
						elseif ($p_valor==$row['lookup_value_id'] && !isset ($_REQUEST[$p_prefix.$p_row['id'].$p_postfix])) $ret .= '<option class="omp_field" value="'.$row['lookup_value_id'].'" selected="selected">'.$row['label'].'</option>';
						elseif ($lookup_rows['info']['default_id']==$row['lookup_value_id'] && !isset ($_REQUEST[$p_prefix.$p_row['id'].$p_postfix]) && (!isset($p_valor) || $p_valor=='')) $ret .= '<option class="omp_field" value="'.$row['lookup_value_id'].'" selected="selected">'.$row['label'].'</option>';
                        else $ret.= '<option class="omp_field" value="'.$row['lookup_value_id'].'">'.$row['label'].'</option>';
                    }
                }
                $ret.='</select>';
            }
            if ($lookup_rows['info']['type']=="R") {
                foreach ($lookup_rows['lookup_all_values'] as $row)
                {
                    if ($p_mode=='I') {
                        if (isset ($_REQUEST[$p_prefix.$p_row['id'].$p_postfix]) && $_REQUEST[$p_prefix.$p_row['id'].$p_postfix]==$row['lookup_value_id']) $ret .= '<input type="radio" name="'.$p_prefix.$p_row['id'].$p_postfix.'" value="'.$row['lookup_value_id'].'" checked="checked"/><span class="omp_field">'.$row['label'].'</span><br />';
                        else $ret .= '<input tabindex="'.$i.'" type="radio" name="'.$p_prefix.$p_row['id'].$p_postfix.'" value="'.$row['lookup_value_id'].'"/><span class="omp_field">'.$row['label'].'</span><br />';
                    }
                    if ($p_mode=='U') {
                        if (isset ($_REQUEST[$p_prefix.$p_row['id'].$p_postfix]) && $_REQUEST[$p_prefix.$p_row['id'].$p_postfix]==$row['lookup_value_id']) $ret .= '<input type="radio" name="'.$p_prefix.$p_row['id'].$p_postfix.'" value="'.$row['lookup_value_id'].'" checked="checked"/><span class="omp_field">'.$row['label'].'</span><br />';
                        //elseif (isset($valors) && in_array($row['lookup_value_id'], $valors) && !isset($_REQUEST['enviat'])) $ret .= '<input type="radio" name="'.$p_prefix.$p_row['id'].$p_postfix.'" value="'.$row['lookup_value_id'].'" checked="checked"/><span class="omp_field">'.$row['label'].'</span><br />';
						elseif (isset($valors) && in_array($row['lookup_value_id'], $valors)) $ret .= '<input type="radio" name="'.$p_prefix.$p_row['id'].$p_postfix.'" value="'.$row['lookup_value_id'].'" checked="checked"/><span class="omp_field">'.$row['label'].'</span><br />';
						elseif ($lookup_rows['info']['default_id']==$row['lookup_value_id'] && !isset ($_REQUEST[$p_prefix.$p_row['id'].$p_postfix])) $ret .= '<input type="radio" name="'.$p_prefix.$p_row['id'].$p_postfix.'" value="'.$row['lookup_value_id'].'" checked="checked"/><span class="omp_field">'.$row['label'].'</span><br />';
                        else $ret .= '<input type="radio" name="'.$p_prefix.$p_row['id'].$p_postfix.'" value="'.$row['lookup_value_id'].'"/><span class="omp_field">'.$row['label'].'</span><br />';
                    }
                }
            }
            if ($lookup_rows['info']['type']=="C") {
                foreach ($lookup_rows['lookup_all_values'] as $row)
                {

                    if ($p_mode=='I') {
                        $ret .= '<input tabindex="'.$i.'" type="checkbox" name="'.$p_prefix.$p_row['id'].$p_postfix.'[]" value="'.$row['lookup_value_id'].'"/><span class="omp_field">'.$row['label'].'</span><br />';
                    }
                    if ($p_mode=='U') {
                        if (isset ($_REQUEST[$p_prefix.$p_row['id'].$p_postfix]) && $_REQUEST[$p_prefix.$p_row['id'].$p_postfix]==$row['lookup_value_id']) {
                            $ret .= '<br />';
                            $ret .= '<input type="checkbox" name="'.$p_prefix.$p_row['id'].$p_postfix.'[]" value="'.$row['lookup_value_id'].'" checked="checked"/><span class="omp_field">'.$row['label'].'</span><br />';
                        }
                        //elseif (isset($valors) && in_array($row['lookup_value_id'], $valors) && !isset($_REQUEST['enviat'])) {
						elseif (isset($valors) && in_array($row['lookup_value_id'], $valors)) {
                            $ret .= '<br />';
                            $ret .= '<input type="checkbox" name="'.$p_prefix.$p_row['id'].$p_postfix.'[]" value="'.$row['lookup_value_id'].'" checked="checked"/><span class="omp_field">'.$row['label'].'</span><br />';
                        }
						elseif ($lookup_rows['info']['default_id']==$row['lookup_value_id'] && (!isset ($_REQUEST[$p_prefix.$p_row['id'].$p_postfix])))
                        {
                            $ret .= '<br />';
                            $ret .= '<input type="checkbox" name="'.$p_prefix.$p_row['id'].$p_postfix.'[]" value="'.$row['lookup_value_id'].'" checked="checked"/><span class="omp_field">'.$row['label'].'</span><br />';
                        }

                        else {
                            $ret .= '<br />';
                            $ret .= '<input type="checkbox" name="'.$p_prefix.$p_row['id'].$p_postfix.'[]" value="'.$row['lookup_value_id'].'"/><span class="omp_field">'.$row['label'].'</span><br />';
                        }
                    }
                }
            }
            //$ret.=$this->getDescription($row);

            return $ret;
        }
    }

}
?>
