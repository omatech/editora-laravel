<?php

/**
 * delete_instance
 *
 * @version $Id$
 * @copyright 2004 
 **/

function html_delete_relation_instance ($p_relinst_id) {
	global $dbh;
	$res="";

	$sql3 = "select ri.rel_id, ri.parent_inst_id, ri.weight from omp_relation_instances ri where ri.id =".$p_relinst_id.";";
	$ret3 = mysql_query($sql3, $dbh);
	$row3 = mysql_fetch_array($ret3, MYSQL_ASSOC);

	$sql = "delete from omp_relation_instances where id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_relinst_id)).";";
	$ret = mysql_query($sql, $dbh);
	if (!$ret) {
		return "Error a l'esborrar: ".mysql_error();
	}

	$sql4 = "update omp_relation_instances set weight = (weight + 10) where rel_id = ".$row3['rel_id']." and parent_inst_id = ".$row3['parent_inst_id']." and weight < ".$row3['weight'].";";
	$ret4 = mysql_query($sql4, $dbh);

	return html_message_ok(getMessage('info_word_deletejoin'));
}

function html_delete_relation_instance_all ($p_inst_id,$p_rel_id) {
	global $dbh;
	$res="";
  
	$sql="select child_inst_id id
	from omp_relation_instances where parent_inst_id = ".$p_inst_id." and rel_id=".$p_rel_id.";";
	$ret = mysql_query($sql, $dbh);
	if (!$ret) {
		return html_message_error("Error a l'esborrar: ".mysql_error());
	}

	require_once(DIR_APLI_ADMIN.'/'.DIR_UTILS.'delete_instance.php');

	while ($Row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
		html_delete_instance ($Row['id']);
	}

	$sql = "delete from omp_relation_instances where parent_inst_id = ".$p_inst_id." and rel_id=".$p_rel_id.";";
	$ret = mysql_query($sql, $dbh);
	if (!$ret) {
		return html_message_error("Error a l'esborrar: ".mysql_error());
	}

	return html_message_ok(getMessage('info_word_deletejoin'));
}

function delete_relation_instance ($p_relinst_id) {
	global $dbh;
	$res="";

	$sql3 = "select ri.rel_id, ri.parent_inst_id, ri.weight from omp_relation_instances ri where ri.id =".$p_relinst_id.";";
	//echo $sql3;
	$ret3 = mysql_query($sql3, $dbh);
	$row3 = mysql_fetch_array($ret3, MYSQL_ASSOC);

	$sql = "delete from omp_relation_instances where id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_relinst_id)).";";
	$ret=mysql_query($sql, $dbh);
	if (!$ret) {
		return false;
	}

	$sql4 = "update omp_relation_instances set weight = (weight + 10) where rel_id = ".$row3['rel_id']." and parent_inst_id = ".$row3['parent_inst_id']." and weight < ".$row3['weight'].";";
	$ret4 = mysql_query($sql4, $dbh);

	return $sql;
}
?>
