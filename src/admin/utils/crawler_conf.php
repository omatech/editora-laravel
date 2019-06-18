<?php

function get_all_classes(){

$link = mysql_connect( dbhost, dbuser, dbpass) or die('Could not connect to server.' );
  mysql_select_db(dbname, $link) or die('Could not select database.');
  if (!$link)    return 'Unable to connect to DB';

  $sql = "SET NAMES 'utf8'";
  $result = mysql_query($sql,$link);
  
  $sql="select id, name as classes from omp_classes";
  $result = mysql_query($sql,$link);
  if (!$result)    return 'Query Error';
  $index=0;
  while($row=mysql_fetch_array($result)){
		$name=$row['classes'];
		$id=$row['id'];

		$sql_cr="select id, priority, active, langs from omp_crawler where class_id='$id'";
		$result2 = mysql_query($sql_cr,$link);
		if (!$result2)    return 'Query2 Error';
		if (mysql_num_rows($result2) == 0){
			$mainarray[$index]=array('class'=>$name, 'id'=>$id, 'priority'=>'', 'active'=>'', 'langs'=>'');
		}
		if (mysql_num_rows($result2) == 1){
			$subrow = mysql_fetch_array($result2, MYSQL_ASSOC);
			$mainarray[$index]=array('class'=>$name, 'id'=>$id, 'priority'=>$subrow['priority'], 'active'=>$subrow['active'], 'langs'=>$subrow['langs']);		
		}
		$index++;
}
$html='	<form action="/controller.php?p_action=config_crawler" method="post">';
$html.='	<input type="hidden" name="factive" value="" />';
		//print_r($mainarray);
	foreach ($mainarray as $clase){
		$html.='
			<strong>'.$clase['class'].'</strong><br />
			<input type="hidden" name="vector['.$clase['id'].'][class_id]" value="'.$clase['id'].'" />
			<label>Prioritat</label>
			<select name="vector['.$clase['id'].'][priority]">
				<option '; if ($clase['priority']==''){     $html.='selected';} $html.=' value=""></option>
				<option '; if ($clase['priority']=='1.00'){ $html.='selected';} $html.=' value="1.00">1.00</option>
				<option '; if ($clase['priority']=='0.80'){ $html.='selected';} $html.=' value="0.80">0.80</option>
				<option '; if ($clase['priority']=='0.70'){ $html.='selected';} $html.=' value="0.70">0.70</option>
				<option '; if ($clase['priority']=='0.50'){ $html.='selected';} $html.=' value="0.50">0.50</option>
				<option '; if ($clase['priority']=='0.30'){ $html.='selected';} $html.=' value="0.30">0.30</option>
				<option '; if ($clase['priority']=='0.20'){ $html.='selected';} $html.=' value="0.20">0.20</option>
			</select>		
			<label>Actiu</label>
			<select name="vector['.$clase['id'].'][active]">
				<option '; if ($clase['active']==''){   $html.='selected';} $html.=' value=""></option>
				<option '; if ($clase['active']=='si'){ $html.='selected';} $html.=' value="si">Si</option>
				<option '; if ($clase['active']=='no'){ $html.='selected';} $html.=' value="no">No</option>
			</select>			
			<label>Llenguatges</label>
			<input type="text" name="vector['.$clase['id'].'][langs]" value="'.$clase['langs'].'" /> (ca;en;es)<br /><br />
			';
			
	}
		$html.='<input type="submit" value="Enviar" />
		</form>';

	return $html;
}

function update_clase($class_id, $priority, $active, $langs){
	
	$link = mysql_connect( dbhost, dbuser, dbpass) or die('Could not connect to server.' );
	mysql_select_db(dbname, $link) or die('Could not select database.');
	if (!$link)    return 'Unable to connect to DB';

	$sql="select id from omp_crawler where class_id='$class_id'";
	$result = mysql_query($sql,$link);
	if (!$result)    return 'Query Error';
	if (mysql_num_rows($result) == 0){
	$sqliu="insert into omp_crawler (class_id, priority, active, langs) values ('$class_id','$priority','$active','$langs')";
	}else{
		$sqliu="update omp_crawler set priority='$priority', active='$active',langs='$langs' where class_id='$class_id'";
	}
	//echo $sqliu;
	$resultiu = mysql_query($sqliu,$link);
	return $resultiu;
}
?>