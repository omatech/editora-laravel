<?php

use Illuminate\Support\Facades\Session;
session_start();
error_reporting(1);
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/ompinfo.php');

$arr_images=array();
function mostrar_imatge($image) {
	$fileparts = explode('/', $image);
	echo '<li>
		<img src="'.FRONT_END_UPLOAD_URL.$fileparts[count($fileparts)-2]."/".$fileparts[count($fileparts)-1].'" width="100"/>
		<span><!--a href="/'.FRONT_END_UPLOAD_URL.$fileparts[count($fileparts)-2]."/".$fileparts[count($fileparts)-1].'" target="_blank"-->'.FRONT_END_UPLOAD_URL.$fileparts[count($fileparts)-2]."/".$fileparts[count($fileparts)-1].'<!--/a--></span>
		<a class="ico attach" href="javascript://" onclick="setNewImgValue(\''.Session::get('arr_images_id').'\', \''.FRONT_END_UPLOAD_URL.$fileparts[count($fileparts)-2]."/".$fileparts[count($fileparts)-1].'\')">Attach</a>
	</li>';
}

function tractar_imatge($file_path) {
	global $arr_images;
 	$url_image=str_replace(DIR_APLI."/", "", $file_path);
	array_push($arr_images, $url_image);
}

function check_dir ($dir_name, $recursive) {
	$handle=opendir($dir_name);
	if ($handle) {
		while (false!==($file=readdir($handle))) {
			if (is_file($dir_name.'/'.$file)) {// Fitxer
				if ((strtolower(substr($file, -3))=='svg' || strtolower(substr($file, -3))=='jpg' || strtolower(substr($file, -3))=='gif' || strtolower(substr($file, -3))=='png') && substr($file, 0, 1)!='.') {// Deteccio de les imatges
					tractar_imatge($dir_name.'/'.$file);
				}
			}
		}

		if ($recursive) {
			closedir($handle);
			$handle=opendir($dir_name);

			while (false!==($file=readdir($handle))) {
				if (is_dir($dir_name.'/'.$file)) {// Directori
					if ($file!='.' && $file!='..') {
						check_dir($dir_name.'/'.$file, $recursive);
					}
				}
			}
		}
		closedir($handle);
	}
	else {
		echo 'No puc obrir el directori '.$dir_name;
		echo chr(13).chr(10).'<br />';
	}
}

if (isset($_REQUEST['arr_images_id']) && $_REQUEST['arr_images_id']!='') {
	Session::put('arr_images_id', $_REQUEST['arr_images_id']);
}

if (!Session::has('arr_images_id') || Session::get('arr_images_id')=='') {
	echo 'Error al obtener el id del atributo';
	return;
}

if (!Session::has('arr_images') || $_REQUEST['reload_arr_images']=='true') {
	check_dir(FRONT_END_UPLOAD_DIR, true);
	Session::put('arr_images', $arr_images);
}
else {
	echo '<a href="javascript://" onclick="reload_arr_images();"><img id="actualizar" src="'.APP_BASE.'/images/actualizar.gif" border="0"/></a><br />';
	$arr_images=Session::get('arr_images');
}

echo '<script type="text/javascript">
	function search_arr_images() {
		$("#form_arr_images").prepend("<img id=\"bigloading\" src=\"'.APP_BASE.'/images/big-ajax-loader.gif\" />");
		$.post($("#form_arr_images").attr("action"), $("#form_arr_images input").serialize(), function(data) {
			$("#image_content").html(data);
			$("#bigloading").remove();
		});
	}

	function reload_arr_images() {
		$("#form_arr_images").prepend("<img id=\"bigloading\" src=\"'.APP_BASE.'/images/big-ajax-loader.gif\" />");
		$.get("'.APP_BASE.'/file_browser?reload_arr_images=true", function(data) {
			$("#image_content").html(data);
			$("#bigloading").remove();
		});
	}
</script>

<form class="wrap" name="form_arr_images" id="form_arr_images" method="post" action="'.APP_BASE.'/file_browser" onsubmit="search_arr_images();return false;">
	<p class="ico">
		<label for="">Buscar fitxer:</label>
		<input type="text" name="arr_images_texto" value="';
		if (isset($_REQUEST['arr_images_texto']) && $_REQUEST['arr_images_texto']!='') echo $_REQUEST['arr_images_texto'];
		echo '" />
		<span id="button_search_arr" class="ico search"><input type="image" value="buscar" /></span>
		<span class="clear"></span>
	</p>
	<div class="scroll_box">
		<ul>';
			rsort($arr_images);
			foreach ($arr_images as $key => $val) {
				if (isset($_REQUEST['arr_images_texto']) && $_REQUEST['arr_images_texto']!='') {
					if (stripos($val, $_REQUEST['arr_images_texto'])!==false) {
						mostrar_imatge($val);
					}
				}
				else {
					mostrar_imatge($val);
				}
			}
		echo '</ul>
	</div>
</form>';
die;
?>
