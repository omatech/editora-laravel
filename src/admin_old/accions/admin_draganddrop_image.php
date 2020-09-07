<?php

use Illuminate\Support\Facades\Session;

header("Cache-Control: no-cache, must-revalidate");
header("Cache-control: private");
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/ompinfo.php');
// require_once(DIR_APLI_ADMIN.'/utils/upload_class.php');
require_once(DIR_APLI_ADMIN . '/models/Security.php');

$sc=new security();
if ($sc->testSession()==0) {
    Session::put('last_page', route('editora.action', '/'));
    $arr=array('status'=>'ko', 'type'=>'Session');
    echo json_encode($arr);
    die;
}

$fileparts = explode('/', $_REQUEST['filename']);
$desti = str_replace($fileparts[count($fileparts)-1], '', $_REQUEST['filename']);

$upload = new UPLOAD_FILES();
$upload->set("name", $fileparts[count($fileparts)-1]); // Uploaded file name.
$upload->set("tmp_name", FRONT_END_DIR.$_REQUEST['filename']); // Uploaded tmp file name.
$upload->set("size", 0); // Uploaded file size.
$upload->set("fld_name", "fld_name"); // Uploaded file field name.
$upload->set("max_file_size", 4096000000000); // Max size allowed for uploaded file in bytes = 40 KB.
$upload->set("randon_name", false); // Generate a unique name for uploaded file? bool(true/false).
$upload->set("replace", false); // Replace existent files or not? bool(true/false).
$upload->set("file_perm", 0444); // Permission for uploaded file. 0444 (Read only).

$upload->set("force_move", true);

if (isset($_REQUEST['p_width']) && $_REQUEST['p_width']!=0 && isset($_REQUEST['p_height']) && $_REQUEST['p_height']!=0) {
    list($width, $height) = getimagesize($file['tmp_name']);
    if ($width > 1920 && $width >= $height) {
        $width=1920;
        $height=(1920*$height/$width);
        $upload->set("p_width", $width);
        $upload->set("p_height", $height);
    } elseif ($height > 1080) {
        $width=(1080*$width/$height);
        $height=1080;
        $upload->set("p_width", $width);
        $upload->set("p_height", $height);
    } else {
        $upload->set("p_width", $_REQUEST['o_width']);
        $upload->set("p_height", $_REQUEST['o_height']);
    }
} else {
    $upload->set("p_width", $_REQUEST['p_width']);
    $upload->set("p_height", $_REQUEST['p_height']);
}

$ymd=date('Ymd');
$desti=DIR_UPLOADS.$ymd;
$upload->createDateFolder($desti);

$desti=DIR_UPLOADS.$ymd;
;

$desti2=URL_UPLOADS.$ymd."/".$upload->checkZipType();
$upload->set("dst_dir", $desti); // Destination directory for uploaded files.
$upload->createDiretoryStructure();
$result = $upload->moveFileToDestination(); // $result = bool (true/false). Succeed or not.
$nom_fitxer=$desti2.$upload->get('name');


if (isset($_REQUEST['p_width']) && $_REQUEST['p_width'] && isset($_REQUEST['p_height']) && $_REQUEST['p_height']) {
    $html='<p class="btn_close"><a onclick="hideImgPopup();" href="javascript://">Cerrar</a></p>
	<input id="crop_p_id" name="crop_p_id" type="hidden" value="'.$nom_fitxer.'" />
	<input id="crop_p_file" name="crop_p_file" type="hidden" value="'.$nom_fitxer.'" />
	<h3>'.__('editora_lang::messages.crop_image').'</h3>';
    if ($_REQUEST['o_width'] < $_REQUEST['p_width'] || $_REQUEST['o_height'] < $_REQUEST['p_height']) {
        $html.='<p>'.$_REQUEST['o_width'].'x'.$_REQUEST['o_height'].' '.$_REQUEST['p_width'].'x'.$_REQUEST['p_height'].'</p>';
    }
    $html.='<img id="image_to_crop" src="'.$nom_fitxer.'" />
	<div id="global_preview">
		<div id="preview">
			<div class="preview-container">
				<img id="cropped" src="'.$nom_fitxer.'" />
			</div>
		</div>
		<div class="actions">
			<h3>'.__('editora_lang::messages.acciones').'</h3>
			<input id="action_leave_as" class="actions" value="'.__('editora_lang::messages.leave_as_is').'" type="submit" />
		<input id="action_resize" class="actions" value="'.__('editora_lang::messages.resize').'" type="submit" />
		<input id="action_crop" class="actions" value="'.__('editora_lang::messages.crop').'" type="submit" />
		</div>
	</div>
	<input type="hidden" id="x" />
	<input type="hidden" id="y" />
	<input type="hidden" id="width" />
	<input type="hidden" id="height" />

	<input type="hidden" id="p_width" value="'.$_REQUEST['p_width'].'" />
	<input type="hidden" id="p_height" value="'.$_REQUEST['p_height'].'" />
	<input type="hidden" id="input_name" value="'.$_REQUEST['input_name'].'" />
	<input type="hidden" id="crop_p_file" value="'.$nom_fitxer.'" />
	<div class="clear"></div>';
    $arr=array('status'=>'crop', 'file'=>$nom_fitxer, 'width'=>$_REQUEST['o_width'], 'height'=>$_REQUEST['o_height'], 'p_width'=>$_REQUEST['p_width'], 'p_height'=>$_REQUEST['p_height'], 'html'=>$html);
    echo json_encode($arr);
    die;
} else {
    $arr=array('status'=>'ok', 'file'=>$nom_fitxer);
    echo json_encode($arr);
    die;
}
