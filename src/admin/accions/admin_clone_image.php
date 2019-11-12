<?php
//à
$_REQUEST['footer']='';

$sc=new security();
if ($sc->testSession()==0) {
	$sc->endSession();
}
else {
	$params=get_params_info();
	if($_SESSION['rol_id']==1 || $sc->getAccess('editable',$params) ) {
		$at=new attributes();
		$inst_id=$_REQUEST['p_inst_id'];
		$p_class_id=$params['param1'];
		$p_tab=$params['param14'];
		$images=$at->getImageAttributes($p_class_id);
		if (empty($_REQUEST['p_image_to_clone'])) { // primer cop que entrem
			$params['p_mode']='U';
			$params['p_acces_type']='A';

			$image_to_clone_atri_id=$_REQUEST['p_atri_id'];
			$image_to_clone=$at->getAttributeValues($image_to_clone_atri_id, $inst_id);
			$url_image_to_clone=$image_to_clone[0]['text_val'];
			$info_image_to_clone=explode('.', $image_to_clone[0]['img_info']);
			$width_image_to_clone=$info_image_to_clone[0];
			$height_image_to_clone=$info_image_to_clone[0];

			$body.='<div class="photo wrap">
				<img src="'.$url_image_to_clone.'" width="100px"/>
				<div id="clone-form">
					<form id="cloneimage" action="'.APP_BASE.'/clone_image" method="post">
						<input type="hidden" name="p_image_to_clone" value="'.$url_image_to_clone.'"/>
						<input type="hidden" name="p_inst_id" value="'.$inst_id.'"/>
						<input type="hidden" name="p_atri_id" value="'.$_REQUEST['p_atri_id'].'"/>
						<input type="hidden" name="p_class_id" value="'.$_REQUEST['p_class_id'].'"/>
						<input type="hidden" name="p_tab" value="'.$params['param14'].'" class="input_tabs"/>
						<ul>';
							$prev_tab='';
							foreach ($images as $image) {
								$previous_image='';
								$message='';
								if ($prev_tab!=$image['tab_name']) $body.='<label for="">'.getMessage('info_word_clone').'. '.getMessage('tab').': '.$image['tab_name'].'</label><span class="clear"></span>';
								$prev_tab=$image['tab_name'];
								$previous_image_value=$at->getAttributeValues($image['id'], $inst_id);
								if (!empty($previous_image_value)) {
									$url_previous_image=$previous_image_value[0]['text_val'];
									$info_previous_image=explode('.', $image_to_clone[0]['img_info']);
									$width_previous_image=$info_image_to_clone[0];
									$height_previous_image=$info_image_to_clone[0];

									if ($image['id']!=$image_to_clone_atri_id) { // no es el mateix id
										if (empty($url_previous_image)) { // no teniem imatge previament
											if ((empty($image['ai_width']) || $image['ai_width']<=$width_image_to_clone)
											&& (empty($image['ai_height']) || $image['ai_height']<=$height_image_to_clone)) {
												$checked=' checked="checked"';
											}
											else {
												$message=' -- ['.getMessage('small_original').']';
											}
										}
										else {
											$message=' -- ['.getMessage('not_empty').']';
										}
									}
									else { // es la mateixa imatge
										$message=' -- ['.getMessage('original').']';
									}
								}
								else { // no teniem imatge previament
									if ((empty($image['ai_width']) || $image['ai_width']<=$width_image_to_clone)
									&& (empty($image['ai_height']) || $image['ai_height']<=$height_image_to_clone)) {
										$checked=' checked="checked"';
									}
									else {
										$message=' -- <span class="red">['.getMessage('small_original').']</span>';
									}
								}
								$check='<input type="checkbox" name="p_clone[]" value="'.$image['id'].'" '.$checked.'/>';
								$body.='<li>'.$check.' <span>'.$image['caption'].' <span class="float_right">'.$image['ai_width'].'x'.$image['ai_height'].$message.'</span></span></li>';
							}
						$body.='</ul>
						<p class="btn"><input class="boto20" type="submit" value="Clonar" /></p>
					</form>
				</div>
			</div>';

			echo $body;
			$_REQUEST['header']=false;
			$_REQUEST['footer']=false;
		}
		else { // ja estem llançant el form
			// require_once(DIR_APLI_ADMIN.'utils/upload_class.php');
			$in=new instances();
			$cloned = 0;
			if (!empty($_REQUEST['p_clone'])) {
				foreach ($_REQUEST['p_clone'] as $atri_id) {
					foreach ($images as $key=>$image_atr) {
						if ($image_atr['id']==$atri_id) {
							$image_attributes=$images[$key];
							break;
						}
					}
					$upload = new UPLOAD_FILES();
					$fileparts = explode ('/',$_REQUEST['p_image_to_clone']);

					$upload->set("name", $fileparts[count($fileparts)-1]); // Uploaded file name.
					$upload->set("tmp_name", DIR_APLI.$_REQUEST['p_image_to_clone']); // Uploaded tmp file name.
					$upload->set("size",0); // Uploaded file size.
					$upload->set("fld_name","fld_name"); // Uploaded file field name.
					$upload->set("max_file_size",4096000000000); // Max size allowed for uploaded file in bytes = 40 KB.
					$upload->set("randon_name",false); // Generate a unique name for uploaded file? bool(true/false).
					$upload->set("replace",false); // Replace existent files or not? bool(true/false).
					$upload->set("file_perm",0444); // Permission for uploaded file. 0444 (Read only).
					$upload->set("force_move",true);

					if (!$image_attributes['ai_width'] && !$image_attributes['ai_height']) {
                        if (defined('FRONT_END_DIR'))
                        {
                            list($width, $height) = getimagesize(FRONT_END_DIR.'/public'.$_REQUEST['p_image_to_clone']);
                        }
                        else {
                            list($width, $height) = getimagesize(DIR_APLI.$_REQUEST['p_image_to_clone']);
                        }
						$upload->set("p_width",$width);
						$upload->set("p_height",$height);
					}
					else {
						$upload->set("p_width",$image_attributes['ai_width']);
						$upload->set("p_height",$image_attributes['ai_height']);
					}


					$ymd=date('Ymd');
					$desti=DIR_UPLOADS.$ymd;
					$desti2=URL_UPLOADS.$ymd."/".$upload->checkZipType();
					$upload->set("dst_dir",$desti);
					$upload->moveFileToDestination();
					$new_atr_value=$desti2.$upload->get('name');

					$in->save_cloneimage_attribute($inst_id, $atri_id, $new_atr_value, $upload->width_thumb, $upload->height_thumb);
					$cloned++;
				}
			}
			if ($cloned > 0) { // Actualitzem la data
				$in->instance_update_date_and_backup($inst_id);
				$_SESSION['missatge']=html_message_ok($cloned.' imágenes clonadas con éxito');
			}
			else {
				$_SESSION['missatge']=html_message_error('No se ha clonado ninguna imagen');
			}

			$sc->redirect_url(APP_BASE."/view_instance/?p_class_id=$p_class_id&p_inst_id=$inst_id&p_tab=$p_tab");
			$_REQUEST['view']='container';
		}
	}
	else {
		$_SESSION['missatge']=html_message_error(getMessage('error_role_privileges'));
		$sc->redirect_url(APP_BASE.'/get_main');
	}
}
?>
