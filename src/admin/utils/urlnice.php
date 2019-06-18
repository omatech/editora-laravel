<?php
	function read_niceurl_from_atri($atri_id,$inst_id) {
		global $dbh;
		$sql_lan="select language from omp_attributes where id=".$atri_id."";
		$ret_lan=mysql_query($sql_lan,$dbh);
		$row_lan=mysql_fetch_array($ret_lan,MYSQL_ASSOC);
		
		$sql="select niceurl from omp_niceurl where inst_id=".$inst_id." and language='".$row_lan['language']."'";
		$ret=mysql_query($sql,$dbh);
		$row=mysql_fetch_array($ret,MYSQL_ASSOC);

		return $row['niceurl'];
	}