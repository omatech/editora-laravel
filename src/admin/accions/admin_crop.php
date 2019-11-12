<?php
session_start();

header("Cache-Control: no-cache, must-revalidate");
header("Cache-control: private");
require_once($_SERVER['DOCUMENT_ROOT'].'/conf/ompinfo.php');
// require_once(DIR_APLI_ADMIN.'/utils/upload_class.php');
require_once(DIR_APLI_ADMIN . '/models/Security.php');

$sc=new security();
if ($sc->testSession()==0) {
	$_SESSION['last_page']='';
	$arr=array('status'=>'ko', 'type'=>'Session');
	echo json_encode($arr);
	die;
}

$fileparts = explode('/', $_REQUEST['filename']);
$desti = str_replace($fileparts[count($fileparts)-1], '', $_REQUEST['filename']);

$upload = new UPLOAD_FILES();
$upload->set("name", $fileparts[count($fileparts)-1]); // Uploaded file name.
$upload->set("tmp_name", DIR_UPLOADS.$fileparts[count($fileparts)-2]."/".basename($_REQUEST['filename'])); // Uploaded tmp file name.
$upload->set("size",0); // Uploaded file size.
$upload->set("fld_name","fld_name"); // Uploaded file field name.
$upload->set("max_file_size",4096000000000); // Max size allowed for uploaded file in bytes = 40 KB.
$upload->set("randon_name",false); // Generate a unique name for uploaded file? bool(true/false).
$upload->set("replace",true); // Replace existent files or not? bool(true/false).
$upload->set("file_perm",0444); // Permission for uploaded file. 0444 (Read only).

$upload->set("width",$_REQUEST['width']);
$upload->set("height",$_REQUEST['height']);
$upload->set("s_width",$_REQUEST['x']);
$upload->set("s_height",$_REQUEST['y']);
if (isset($_REQUEST['p_width'])) $upload->set("p_width",$_REQUEST['p_width']);
else $upload->set("p_width",$_REQUEST['width']);
if (isset($_REQUEST['p_height'])) $upload->set("p_height",$_REQUEST['p_height']);
else $upload->set("p_height",$_REQUEST['height']);

$desti=DIR_UPLOADS.$fileparts[count($fileparts)-2];
$desti2=URL_UPLOADS.$fileparts[count($fileparts)-2];

$upload->set("dst_dir",$desti); // Destination directory for uploaded files.
$result = $upload->moveFileToDestination();	// $result = bool (true/false). Succeed or not.
$nom_fitxer=$desti2."/".$upload->get('name');
$arr=array('status'=>'ok', 'file'=>$nom_fitxer);
echo json_encode($arr);
die;
?>
