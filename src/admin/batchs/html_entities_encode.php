<?php
require_once('../conf/ompinfo.php');
require_once('../objects/MainObject.php');

$main_object = new MainObject();
$sql="select id, text_val
	from omp_values
	where text_val LIKE '%&%'";
$rows = $main_object->get_data($sql);

foreach($rows as $row){
	$sql2 = "update omp_values set text_val='".$main_object->escape(html_entity_decode($row['text_val'], ENT_QUOTES, 'UTF-8'))."' where id = ".$row['id'].";";
	$main_object->update_one($sql2);
	echo $row['id'].'##';
}