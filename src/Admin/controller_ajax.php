<?php
use Illuminate\Support\Facades\Session;

	require_once("Security.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/conf/ompinfo.php');

	$action=$_REQUEST['action'];

	if($action=="upload") {
		if(testSession()) {
			$file=$_REQUEST['xfile'];
			$instance_id=$_REQUEST['xinst'];
			$atr_id=$_REQUEST['xatri'];
			$sql='insert into omp_values (inst_id, atri_id, text_val, date_val, num_val)
				values("'.$instance_id.'", "'.$atr_id.'", "'.Session::get('uploadfile').'");';
			echo $sql;
		}
		else {
			echo "KO";
		}
	}
	if($action=="actualizar") {
		echo Session::get('last_upload_file');
	}
	else {
		echo "KO";
	}
