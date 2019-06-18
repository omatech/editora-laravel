<?php

/**
 * delete_instance
 *
 * @version $Id$
 * @copyright 2004 
 **/

function check_delete_instance ($p_inst_id)
{
	$l_cont=0;
	
	global $dbh;
	$sql = "select ip.id, ip.class_id, ip.key_fields, ip.status
		from omp_instances ip
		, omp_relation_instances rip
		where rip.child_inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id)).";
		and rip.parent_inst_id = ip.id";
		//echo $sql; 
		
	$ret = mysql_query($sql, $dbh);
	if(!$ret)
	{
		return "error:". mysql_error();
	}
	
	/*if(is_array(mysql_fetch_array($ret, MYSQL_ASSOC)))
	{*/
		$res='<div id="taula" class="col_item tbl_objects" style="width: 100%">';
		$res.='<div id="titol_taula">'.getMessage('related_objects').' '.$ins_title.':</div>';
		
		$res.='<div id="lasupertabla">';
		$res.='<table id="tabla-objects" width="100%">';
		$res.='<tr>
			<td class="header">'.getMessage('info_word_ID').'</td>
			<td class="header">'.getMessage('info_word_keyword').'</td>
			<td class="header">'.getMessage('info_word_type').'</td>
			<td class="header">'.getMessage('info_word_status').'</td>
		</tr>';
		$pijama=' class="even"';
		while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
		{
		 $res.='<tr'.$pijama.'>';
			if ($pijama==' class="even"') $pijama=' class="odd"';
			else $pijama=' class="even"';
			$res.='<td><a href="controller.php?p_action=view_instance&amp;p_inst_id='.$row['id'].'&amp;p_class_id='.$row['class_id'].'" title="'.getMessage('info_word_view').'"><strong>'.$row['id'].'</strong></a></td>
			<td><a href="controller.php?p_action=view_instance&amp;p_inst_id='.$row['id'].'&amp;p_class_id='.$row['class_id'].'" title="'.getMessage('info_word_view').'"><strong>'.$row['key_fields'].'</strong></a></td>
			<td><strong>'.getClassName($row['class_id']).'</strong></td>
			<td><strong>'.status_to_html($row['status']).'</strong></td>
		</tr>';
		$l_cont=$l_cont+1;
		}

		$sql = "select ic.id, ic.class_id, ic.key_fields, ic.status
			from omp_instances ic
			, omp_relation_instances ric
			where ric.parent_inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id))."
			and ric.child_inst_id = ic.id";
			//echo $sql; 

		$ret = mysql_query($sql, $dbh);
		if(!$ret)
		{
			return "Error al borrar: ".mysql_error();
		}

		while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
		{
			$res.='<tr><td class="omp_listelement">
			<a href="controller.php?p_action=view_instance&amp;p_inst_id='.$row['id'].'&amp;p_class_id='.$row['class_id'].'" title="'.getMessage('info_word_view').'">'.$row['id'].'</a></td>
			<td class="omp_listelement"><a href="controller.php?p_action=view_instance&amp;p_inst_id='.$row['id'].'&amp;p_class_id='.$row['class_id'].'" title="'.getMessage('info_word_view').'">'.$row['key_fields'].'</a></td>
			<td class="omp_list_element">'.getClassName($row['class_id']).'</td>
			<td class="omp_listelement">'.status_to_html($row['status']).'</td></tr>';
		$l_cont=$l_cont+1;
		}
		$res.='</table>';
	$res.='</div>';

	if ($l_cont >= 1)
	{
		$message='<div id="fill_ariadna2">'.html_message_error('<br>'.getMessage('error_object_delete').'&nbsp;<a href="javascript: history.go(-1)" class="omp_copyright">'.getMessage('navigation_back').'</a>').'</div>';
	}

	if ($l_cont == 0)
	{
		$message.='<div id="fill_ariadna2">'.html_message_warning('<br><span="omp_header">'.getMessage('info_word_areyousure').'	</span><a href="controller.php?p_action=delete_instance2&amp;p_inst_id='.$p_inst_id.'">'.getMessage('info_word_yes').'</a> &nbsp;&nbsp; <a href="javascript: history.go(-1)" class="omp_copyright">'.getMessage('info_word_no').'</a>').'</div>';
	}
	$res.='</div>';
	
	//}

	return $message.$res;
}


function html_delete_instance ($p_inst_id)
{
	global $dbh;

	$res="";

	$sql = "delete from omp_values where inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id)).";";
	$ret = mysql_query($sql, $dbh);
	if(!$ret)
	{
		return("Error:". mysql_error());
	}
	
	$sql = "delete from omp_niceurl where inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id)).";";
	$ret = mysql_query($sql, $dbh);
	if(!$ret)
	{
		return("Error:". mysql_error());
	}
	
	$sql = "delete from omp_relation_instances where parent_inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id)).";";
	$ret = mysql_query($sql, $dbh);
	if(!$ret)
	{
		return("Error:". mysql_error());
	}
	
	$sql = "delete from omp_relation_instances where child_inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id)).";";
	$ret = mysql_query($sql, $dbh);
	if(!$ret)
	{
		return("Error:". mysql_error());
	}

	$sql = "delete from omp_instances where id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id)).";";
	$ret = mysql_query($sql, $dbh);
	if(!$ret)
	{
		return("Error:". mysql_error());
	}

//echo $sql; 
 
	return getMessage('info_object_deleted');
}

function check_delete_instanceArr ($p_inst_arr) {
	$l_cont=0;
	
	global $dbh;
	
	$res = '<div id="taula" class="col_item tbl_objects" style="width: 100%">';
		//$res.='<div id="titol_taula">Objectes relacionats trobats '.$value['p_inst_id'].':</div>';
			$res.='<div id="lasupertabla">';
			$res.='<table id="tabla-objects" width="100%">';
			$res.='<tr>
					<td class="header">'.getMessage('info_word_ID').'</td>
					<td class="header">'.getMessage('info_word_keyword').'</td>
					<td class="header">'.getMessage('info_word_type').'</td>
					<td class="header">'.getMessage('info_word_status').'</td>
					<td class="header">'.getMessage('info_word_childs').'</td>
				</tr>';
				
	$to_eliminate = $res;
	$total_eliminate = 0;
	$total_res = 0;
	$insts = '';
	foreach($p_inst_arr as $value)
	{
		//Mirem el total de fills que té.
		$sql = "select count(*) as total 
				from omp_instances ic
				, omp_relation_instances ric
				, omp_instances i
				where ric.parent_inst_id = i.id
				and ric.child_inst_id = ic.id
				and i.id = ".str_replace("\"", "\\\"", str_replace("[\]","",$value['p_inst_id'])).";"; //echo $sql; 

		$ret = mysql_query($sql, $dbh);
		if(!$ret)
			return "Error al contar borrar: ".mysql_error();

		while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
			$total = $row['total'];
			
		$sql = "select ic.id id, key_fields as key_fields, name_".$_SESSION['u_lang']." as name, status, class_id
				from omp_instances ic
				, omp_classes oc
				where  ic.id = '".str_replace("\"", "\\\"", str_replace("[\]","",$value['p_inst_id']))."'
				and oc.id = ic.class_id;";
					//echo $sql; 
	
		$ret2 = mysql_query($sql, $dbh);
		if(!$ret2)
			return "Error al borrar: ".mysql_error();
		
		while ($row = mysql_fetch_array($ret2, MYSQL_ASSOC))
		{
			$sentence ='<tr><td class="omp_listelement">
			<a href="controller.php?p_action=view_instance&amp;p_inst_id='.$row['id'].'&amp;p_class_id='.$row['class_id'].'" title="'.getMessage('info_word_view').'">'.$row['id'].'</a></td>
			<td class="omp_listelement"><a href="controller.php?p_action=view_instance&amp;p_inst_id='.$row['id'].'&amp;p_class_id='.$row['class_id'].'" title="'.getMessage('info_word_view').'">'.$row['key_fields'].'</a></td>
			<td class="omp_list_element">'.getClassName($row['class_id']).'</td>
			<td class="omp_listelement">'.status_to_html($row['status']).'</td>
			<td class="omp_listelement">'.$total.'</td></tr>';
			
			$status = $row['status'];
			$inst = $row['id'].',';
		}

		if ($total == 0 and $status == 'P')
		{
			$to_eliminate .= $sentence;
			$total_eliminate++;
			$insts .= $inst;
		}
		else
			{
				$res .= $sentence;
				$total_res++;
			}
	}

	$res.='</table> </div> </div>';
		
	$to_eliminate.='</table> </div> </div>';

	$insts = substr($insts, 0 , strlen($insts)-1);

	if($total_eliminate >= 0 && $total_res == 0)
	{
		
		$message = '<div id="fill_ariadna2">'.html_message_warning('<br><span class="omp_header">'.getMessage('info_word_areyousure').'	</span><a href="controller.php?p_action=delete_instance2Arr&amp;p_inst_id='.$insts.'">'.getMessage('info_word_yes').'</a> &nbsp;&nbsp; <a href="javascript: history.go(-1)" class="omp_copyright">'.getMessage('info_word_no').'</a></span>').'</div>';
		return $message.$to_eliminate;
	}
	
	if($total_res >= 0 && $total_eliminate == 0)
	{
		$message = '<div id="fill_ariadna2">'.html_message_warning('<br><span class="omp_header">'.getMessage('info_word_not_eliminate').'</span> <a class="omp_copyright" href="javascript: history.go(-1)">Tornar</a>').'</div>';
		return $message.$res;
	}
	
	if($total_res > 0 && $total_eliminate > 0)
	{
		$message2 = '<div id="fill_ariadna2">'.html_message_warning('<br><span class="omp_header">'.getMessage('info_word_not_eliminate').'</span> <a class="omp_copyright" href="javascript: history.go(-1)">Tornar</a>').'</div>';
				
		$message = '<div id="fill_ariadna2">'.html_message_warning('<br><span class="omp_header">'.getMessage('info_word_areyousure_arr').'	</span><a href="controller.php?p_action=delete_instance2Arr&amp;p_inst_id='.$insts.'">'.getMessage('info_word_yes').'</a> &nbsp;&nbsp; <a href="javascript: history.go(-1)" class="omp_copyright">'.getMessage('info_word_no').'</a></span>').'</div>';
		return $message.$to_eliminate.$message2.$res;
	}
	
	/*
if ($total >= 1)
	{
		$message='<div id="fill_ariadna2">'.html_message_error('<br>'.getMessage('error_object_delete').'&nbsp;<a href="javascript: history.go(-1)" class="omp_copyright">'.getMessage('navigation_back').'</a>').'</div>';
	}

	if ($total == 0)
	{
		$message.='<div id="fill_ariadna2">'.html_message_warning('<br><span="omp_header">'.getMessage('info_word_areyousure').'	</span><a href="controller.php?p_action=delete_instance2&amp;p_inst_id='.$p_inst_id.'">'.getMessage('info_word_yes').'</a> &nbsp;&nbsp; <a href="javascript: history.go(-1)" class="omp_copyright">'.getMessage('info_word_no').'</a></span>').'</div>';
	}
	
*/
}


function html_delete_instanceArr ($p_inst_idArr)
{
	global $dbh;
	foreach($p_inst_idArr as $p_inst_id)
	{
		$sqlArr = array("delete from omp_values where inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id['p_inst_id'])).";",
				 "delete from omp_niceurl where inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id['p_inst_id'])).";",
				 "delete from omp_relation_instances where parent_inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id['p_inst_id'])).";",
				 "delete from omp_relation_instances where child_inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id['p_inst_id'])).";",
				 "delete from omp_instances where id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id['p_inst_id'])).";");
	
		foreach($sqlArr as $sql)
		{
			$ret = mysql_query($sql, $dbh);
			if(!$ret)
				return("Error:". mysql_error());
		}
	}
	 
	return getMessage('info_object_deleted_plural');
}

?>
