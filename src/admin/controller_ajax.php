<?php
	session_start();
	require_once("Security.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/conf/ompinfo.php');

	$action=$_REQUEST['action'];

	if($action=="upload") {
		if(testSession()) {
			$file=$_REQUEST['xfile'];
			$instance_id=$_REQUEST['xinst'];
			$atr_id=$_REQUEST['xatri'];

			$sql='insert into omp_values (inst_id, atri_id, text_val, date_val, num_val)
				values("'.$instance_id.'", "'.$atr_id.'", "'.$_SESSION['uploadfile'].'");';
			echo $sql;
		}
		else {
			echo "KO";
		}
	}
	if($action=="actualizar") {
		/*$instance_id=$_REQUEST['instId'];
		$atr_id=$_REQUEST['atriId'];

		global $dbh;



		$sql='select text_val from omp_values where inst_id='.$instance_id.' and atri_id='.$atr_id.';';
		//echo $sql;
		$ret = mysql_query($sql, $dbh);
		$row = mysql_fetch_array($ret, MYSQL_ASSOC);*/
		echo $_SESSION['last_upload_file'];
	}
	else {
		echo "KO";
	}
?>
