<?php
//à

//////////////////////////////////////////////////////////////////////////////////////////
function pr($var) {
	echo '<pre>';
		print_r($var);
	echo '</pre>';
}

//////////////////////////////////////////////////////////////////////////////////////////
function xx(&$var = null) {
	if (!isset($var)) return false;
	if (is_null($var)) return false;
	if (empty($var)) return false;
	if (is_array($var) && sizeof($var) == 0) return false;
	if (is_object($var) && count(get_object_vars($var))==0) return false;
	if ($var === false) return false;
	
	return true;
}

//////////////////////////////////////////////////////////////////////////////////////////
function get_nice_from_id($id, $lg = 'ca') {
	global $dbh;
	if (!$dbh) return -1;

	$sql="select niceurl from omp_niceurl n, omp_instances i where i.id=inst_id and inst_id=".$id." and (language='".$lg."' or language='ALL')";

	if (isset($_REQUEST['req_info']) && $_REQUEST['req_info']==0) {
		$sql.=" and i.status = 'O'";
	}
	$result = mysql_query($sql,$dbh);
	if (!$result) return -2;

	if (mysql_num_rows($result) == 1) {
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row['niceurl'];
	}

	return $id;
}

//////////////////////////////////////////////////////////////////////////////////////////
function date_to_mysql($p_date) { // Transforma de dd/mm/yyyy i opcionalment dd/mm/yyyy a yyyy-mm-dd hh24:mi:ss
	$p_date = trim($p_date);
	$dia = strtok($p_date,'/');
	$mes = strtok('/');
	$any = strtok('/');
	if (strlen($any)>4) {
		$any=substr($any,0, 4);
	}
	$res = $any.'-'.$mes.'-'.$dia;

	$tehora=strpos($p_date,':');
	if ($tehora) {
		$hores=substr(strtok($p_date, ':'),-2);
		$minuts=strtok(':');
		$segons=strtok(':');
		$res.= ' '.$hores.':'.$minuts.':'.$segons;
	}
	return $res;
 }

//////////////////////////////////////////////////////////////////////////////////////////
function mysql_to_date($p_date,$hora = 0) {// reb yyyy-mm-dd hh24:mi:ss i retorna data normal
	$res='';
	$separate=explode(' ',$p_date);
	$dates=explode('-',$separate[0]);

	$res.=$dates[2].'/'.$dates[1].'/'.$dates[0];

	if ($hora) {
		$res.=' '.$separate[1];
	}

	return $res;
}

//////////////////////////////////////////////////////////////////////////////////////////
function control_idioma($lg) {
	global $array_langs;
	if (in_array ($lg,$array_langs,TRUE)) {
		return $lg;
	}else{
		return $array_langs[0];
	}
}

//////////////////////////////////////////////////////////////////////////////////////////
function comproba_idioma($lg) {
	global $array_langs;
	if (in_array ($lg,$array_langs,TRUE)) {
		return TRUE;
	}
	return FALSE;
}

//////////////////////////////////////////////////////////////////////////////////////////
function get_title_from_id ($id, $lg) {
	global $dbh;
	if (!$dbh) return -1;

	$sql="select v.text_val as id from omp_instances i, omp_attributes a, omp_values v
	where i.id = ".$id." and a.name = 'titol_".$lg."' and v.atri_id = a.id and v.inst_id = i.id";
	if (xx($_REQUEST['req_info']) || $_REQUEST['req_info']==0) {
		$sql.=" and i.status = 'O'";
	}

	$result = mysql_query($sql,$dbh);
	if (!$result) return -2;
	if (mysql_num_rows($result) == 1) {
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row['id'];
	}
	
	return false;
}

//////////////////////////////////////////////////////////////////////////////////////////
function get_tag_from_id($p_instance_id) {
	$link = mysql_connect( dbhost, dbuser, dbpass) or die('Could not connect to server.' );
	mysql_select_db(dbname, $link) or die('Could not select database.');
	if (!$link) return -1;

	$sql="select tag
	from omp_instances i
	,omp_classes c
	where i.class_id=c.id
	and i.id=".$p_instance_id.";";

	$result = mysql_query($sql,$link);
	if (!$result) return -2;

	if (mysql_num_rows($result) == 1) {
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row['tag'];
	}
	
	return '';
}

//////////////////////////////////////////////////////////////////////////////////////////
function get_value($p_lookup_id, $p_lang = 'ALL') {
	global $dbh;
	if (!$dbh) return "Error connect: ";

	$res = "";
	$sql = "select lv.value_".$p_lang." label from omp_lookups_values lv where lv.id = ".$p_lookup_id.";";

	$result = mysql_query($sql,$dbh);
	if ($result) {
		while ($un_valor = mysql_fetch_array($result)) {
			$res.=$un_valor['label'];
		}
	}

	return $res;
}

//////////////////////////////////////////////////////////////////////////////////////////
function get_true_value($p_lookup_id, $p_lang = 'ALL') {
	global $dbh;
	if (!$dbh) {
		return "Error connect: ";
	}

	$sql = "select lv.value from omp_lookups_values lv where lv.id = ".$p_lookup_id.";";

	$result = mysql_query($sql,$dbh);
	if ($result) $un_valor =mysql_fetch_array($result);

	return $un_valor['value'];
}

//////////////////////////////////////////////////////////////////////////////////////////
function extract_default($p_valor) {
	$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $p_valor);
	return $string;
}