<?php

function deleteUser($p_user_id) {
	global $dbh;
	$sql = 'delete from omp_users where id='.$p_user_id;

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		return "Error a l'eliminar: ".mysql_error();
	}
	else {
		return "Usuario eliminado satisfactoriamente";
	}
}

function deleteRol($p_rol_id) {
	global $dbh;
	if ($p_rol_id==1) {
		return "No puede eliminarse el rol de administrador!!";
	}

	$sql = 'delete from omp_users where rol_id='.$p_rol_id;
	$ret = mysql_query($sql, $dbh);

	$sql = 'delete from omp_roles_classes where rol_id='.$p_rol_id;
	$ret = mysql_query($sql, $dbh);

	$sql = 'delete from omp_roles where id='.$p_rol_id;
	$ret = mysql_query($sql, $dbh);

	if (!$ret) {
		return "Error al eliminar: ".mysql_error();
	}
	else {
		return "Rol eliminado satisfactoriamente";
	}
}


function addRol1()
{
  $res='';
  $res.='<table width="400" border="0" align="center">
  <form action="controller.php" method="post">
  <input type="hidden" name="p_action" value="add_rol2">
  <tr><td class="omp_field">Nombre:</td><td class="omp_field"><input type="text" name="p_name"></td></tr>
  <tr>
    <td class="omp_field">Acceso por defecto:</td>
	<td class="omp_field">
      Total:&nbsp;<input type="radio" name="p_acceso" value="Y">
      &nbsp;&nbsp;
      Parcial:&nbsp;<input type="radio" name="p_acceso" value="P">
      &nbsp;&nbsp;
	  Nulo:&nbsp;<input type="radio" name="p_acceso" value="N" CHECKED>	  
	</td>
  <tr><td><input type="submit" value="Guardar" class="omp_button"/></td></tr>
  </form>
  </table>
  ';
  return $res;
}

function addRol2($p_name, $p_acceso) {
	global $dbh;
  $sql = 'insert into omp_roles (rol_name, enabled) values
  ("'.$p_name.'", "Y")';
  $ret = mysql_query($sql, $dbh);
  if(!$ret)
  {
    return("Error al insertar:". mysql_error());
  }
  else
  {    
     $sql='select last_insert_id() last;
     ';
   
     //$res.=$sql;
     $result = mysql_query($sql, $dbh);
	 $result_row = mysql_fetch_array($result, MYSQL_ASSOC);
     //$result = $db->query($sql);
     //$result_row = $result->fetchRow(DB_FETCHMODE_OBJECT);
   
     $new_rol_id = $result_row['last'];
	 
     $sql = "select id from omp_classes";
//    echo $sql;

     if ($p_acceso=='Y')
	 {
	   $add_string='"Y", "Y", "Y", "Y", "Y", "Y", "Y", "Y", "Y", "Y"';
	 }
     elseif ($p_acceso=='P')
	 {
	   $add_string='"P", "P", "P", "P", "P", "P", "P", "P", "P", "P"';
	 }

	 else
	 {
	   $add_string='"N", "N", "N", "N", "N", "N", "N", "N", "N", "N"';
	 }

     $ret2 = mysql_query($sql, $dbh);
     if(!$ret2)
     {
       return "Error al insertar: ". mysql_error();
     }

    while ($row2 = mysql_fetch_array($ret2, MYSQL_ASSOC))
    {
      $class_id = $row2['id'];
	  $sql2 = 'insert into omp_roles_classes (class_id, rol_id, browseable, insertable, editable, deleteable, permisos, status1, status2, status3, status4, status5) values
      ('.$class_id.', '.$new_rol_id.', '.$add_string.')';
//echo $sql2;
      //$ret3 = $db->query($sql2);
	  $ret3 = mysql_query($sql2, $dbh);
    }
   
    return "Rol creado satisfactoriamente";
  }
}

function addUser1($p_rol_id)
{
  $res='';
  $res.='<table width="400" border="0" align="center">
  <form action="controller.php" method="post">
  <input type="hidden" name="p_action" value="add_user2">
  <input type="hidden" name="p_rol_id" value="'.$p_rol_id.'">
  <tr><td class="omp_field">Username:</td><td class="omp_field"><input type="text" name="p_username"></td></tr>
  <tr><td class="omp_field">Password:</td><td class="omp_field"><input type="password" name="p_password1"></td></tr>
  <tr><td class="omp_field">Repita el password:</td><td class="omp_field"><input type="password" name="p_password2"></td></tr>
  <tr><td class="omp_field">Nombre completo:</td><td class="omp_field"><input type="text" name="p_complete"></td></tr>
  <tr><td><input type="submit" value="Guardar" class="omp_button"/></td></tr>
  </form>
  </table>
  ';
  return $res;
}

function addUser2($p_rol_id, $p_username, $p_password, $p_complete) {
	global $dbh;
	$sql = 'insert into omp_users (username, password, complete_name, rol_id) values
	("'.$p_username.'", "'.$p_password.'", "'.$p_complete.'", '.$p_rol_id.')';
	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		return "Error al insertar: ".mysql_error();
	}
	else {
		return "Usuario creado satisfactoriamente";
	}
}

function editUser1($p_user_id) {
	global $dbh;
	$res='';
	$sql = 'select * from omp_users where id='.$p_user_id;
	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}
	$row = mysql_fetch_array($ret, MYSQL_ASSOC);

	$res.='<table width="400" border="0" align="center">
		<form action="controller.php" method="post">
			<input type="hidden" name="p_action" value="edit_user2">
			<input type="hidden" name="p_user_id" value="'.$row['id'].'">
			<input type="hidden" name="p_rol_id" value="'.$row['rol_id'].'">
			<tr><td class="omp_field">Username:</td><td class="omp_field">
			<input type="text" name="p_username" value="'.$row['username'].'"></td></tr>
			<tr><td class="omp_field">Password:</td><td class="omp_field">
				<input type="password" name="p_password1" value="'.$row['password'].'">
			</td></tr>
			<tr><td class="omp_field">Repita el password:</td><td class="omp_field">
				<input type="password" name="p_password2" value="'.$row['password'].'">
			</td></tr>
			<tr><td class="omp_field">Nombre completo:</td><td class="omp_field">
				<input type="text" name="p_complete" value="'.$row['complete_name'].'"></td></tr>
			<tr><td><input type="submit" value="Guardar" class="omp_button"/></td></tr>
		</form>
	</table>';

	return $res;
}

function editUser2($p_user_id, $p_rol_id, $p_username, $p_password, $p_complete) {
	global $dbh;
	$sql = 'update omp_users set
	username = "'.$p_username.'"
	, password = "'.$p_password.'"
	, complete_name = "'.$p_complete.'"
	, rol_id = '.$p_rol_id.'
	where id='.$p_user_id;
	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		return "Error al insertar: ".mysql_error();
	}
	else {
		return "Usuario editado correctamente";
	}
}


function getPrivField($p_field_name, $p_value)
{
  $res='';
  if ($p_field_name == 'p_rol_class_id')
  {// Cas especial, el id no es editable
    $res.='<input type="hidden" name="'.$p_field_name.'" value="'.$p_value.'">'.$p_value;
  }
  else
  {
    $res.='<select name="'.$p_field_name.'">';
	if ($p_value=='Y')
	{
	  $res.='<option value="Y" SELECTED>Sí</option>';
	  $res.='<option value="P">Parcial</option>';
	  $res.='<option value="N">No</option>';
	}
	elseif ($p_value=='P')
	{
	  $res.='<option value="Y">Sí</option>';
	  $res.='<option value="P" SELECTED>Parcial</option>';
	  $res.='<option value="N">No</option>';
	}
	else
	{
	  $res.='<option value="Y">Sí</option>';
	  $res.='<option value="P">Parcial</option>';
	  $res.='<option value="N" SELECTED>No</option>';
	}
	$res.='</select>';
  }
  return $res;
}

function getRoles() {
	global $dbh;
  $sql = "select * from omp_roles";
  $ret = mysql_query($sql, $dbh);
  if(!$ret)
  {
    die("error:". mysql_error());
  }
  
  $res='<center><table id="tabla-objects" width="600">';
  $res.='<thead><tr><td class="header" width="60" align="center"><b>Rol ID</b></td><td class="header" align="center" width="200"><b>Nombre del rol</b></td><td class="header" align="center" width="60"><b>'.getMessage('acciones').'</b></td><td class="header" align="center" width="60"><b>Activo</b></td></tr>';

  while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
  {
    $res.='</thead><tbody><tr valign="middle">
	  <td align="center" class="omp_listelement"><a href="controller.php?p_action=get_privileges&p_rol_id='.$row['id'].'">'.$row['id'].'</a></td>
	  <td align="center" class="omp_listelement"><a href="controller.php?p_action=get_privileges&p_rol_id='.$row['id'].'">'.$row['rol_name'].'</a></td>
	  <td align="center" class="omp_listelement">
	    <a href="controller.php?p_action=view_users&p_rol_id='.$row['id'].'"><img src="images/user.gif" border="0" title="Ver usuarios de este rol" alt="Ver usuarios de este rol"></a>
	    <a href="controller.php?p_action=delete_rol&p_rol_id='.$row['id'].'"><img src="images/eliminarmini.gif" border="0" title="Eliminar rol"></a>
	  </td>
	  <td align="center" class="omp_listelement">'.getimg($row['enabled']).'</td></tr>';
  }
  $res.='</tbody></table>';
  $res.='<br><form name="form" action="controller.php" method="get"><input type="hidden" name="p_action" value="add_rol1"/><input type="submit" class="boto20" value="Añadir rol"/><br /></center>';
  return $res;
}

function viewUsers ($p_rol_id) {
	global $dbh;
	$sql = 'select * from omp_users where rol_id='.$p_rol_id.' and tipus="U"';
	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}


  $res='<center><table id="tabla-objects" width="600">';
  $res.='<thead><tr><td class="header" width="60" align="center"><b>User ID</b></td>
  <td class="header"><b>Username</b></td>
  <td class="header"><b>Password</b></td>
  <td class="header"><b>Nombre Completo</b></td>
  <td class="header"><b>'.getMessage('acciones').'</b></td>
  </tr></thead><tbody>';

  while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
  {
    $res.='<tr><td class="omp_listelement">
	  <a href="controller.php?p_action=edit_user&p_user_id='.$row['id'].'">'.$row['id'].'</a></td>
	  <td class="omp_listelement">'.$row['username'].'</td>
	  <td class="omp_listelement">'.$row['password'].'</td>
	  <td class="omp_listelement">'.$row['complete_name'].'</td>
	  <td class="omp_listelement">
	    <a href="controller.php?p_action=delete_user&p_user_id='.$row['id'].'"><img src="images/eliminarmini.gif" border="0" title="Eliminar usuario"></a>
	    <a href="controller.php?p_action=edit_user1&p_user_id='.$row['id'].'"><img src="images/editarmini.gif" border="0" title="Editar usuario"></a>
	  </td>
	  <td class="omp_listelement">'.$row['enabled'].'</td></tr>';
  }
  $res.='</tbody></table>';
  $res.='<br /><a class="omp_listelement" href="controller.php?p_action=add_user1&p_rol_id='.$p_rol_id.'"><img src="images/nova.gif" border="0"> Añadir usuario a este rol</a><br /></center>';
  return $res;
}


function getPrivileges ($p_rol_id) {
	global $dbh;
	$sql = "select * from omp_roles_classes rc where rc.rol_id = ".$p_rol_id;

  $ret = mysql_query($sql, $dbh);
  if(!$ret)
  {
    die("error:". mysql_error());
  }
  $res='<center><table id="tabla-objects" width="600">';
  $res.='<thead><tr><td class="header" width="40"></td>'.
	'<td class="header" width="80"><b>Class</b>'.
    '</td><td class="header" width="40"><b>Visible</b>'.
	'</td><td class="header" width="40"><b>Insertable</b>'.
	'</td><td class="header" width="40"><b>Editable</b>'.
	'</td><td class="header" width="40"><b>Borrable</b>'.
	'</td><td class="header" width="40"><b>Permisos</b>'.
	'</td><td class="header" width="40"><b>Pendiente</b>'.
	'</td><td class="header" width="40"><b>Revisar</b>'.
	'</td><td class="header" width="40"><b>Publicar</b>'.
	'</tr></thead><tbody>';
	
  while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
  {
    $res.='<tr><td class="omp_listelement" align="center"><a href="controller.php?p_action=edit_privilege1&p_rol_class_id='.$row['id'].'"><img src="images/editarmini.gif" alt="Editar" title="Editar" border="0"/></a></td>
	<td class="omp_listelement"><b>'.getClassName($row['class_id']).
	'</b></td><td class="omp_listelement" align="center"><b>'.getimg($row['browseable']).'</b></td>'.
	'<td class="omp_listelement" align="center"><b>'.getimg($row['insertable']).'</b></td>'.
	'<td class="omp_listelement" align="center"><b>'.getimg($row['editable']).'</b></td>'.
	'<td class="omp_listelement" align="center"><b>'.getimg($row['deleteable']).'</b></td>'.
	'<td class="omp_listelement" align="center"><b>'.getimg($row['permisos']).'</b></td>'.
	'<td class="omp_listelement" align="center"><b>'.getimg($row['status1']).'</b></td>'.
	'<td class="omp_listelement" align="center"><b>'.getimg($row['status2']).'</b></td>'.
	'<td class="omp_listelement" align="center"><b>'.getimg($row['status3']).'</b></td>'.
	'</tr>';
  }
  $res.='</tbody></table></center>';
  return $res;
}

function getimg($var)
{
  if($var=="Y")
  {
	return '<img src="images/validmini.gif" alt="Si" title="Si"/>';
  }
  else
  {
	return '<img src="images/eliminarmini.gif" alt="No" title="No"/>';
  }
}

function editPrivilege1 ($p_rol_class_id) {
	global $dbh;
  $sql = "select * from omp_roles_classes rc where rc.id = ".$p_rol_class_id;

  $ret = mysql_query($sql, $dbh);
  if(!$ret)
  {
    die("error:". mysql_error());
  }

  $res='<form action="controller.php">
  <input type="hidden" name="p_action" value="edit_privilege2">
  <center><table width="600">';
  $res.='<tr><td class="header"><b>ID</b></td><td class="header"><b>Class</b>'.
    '</td><td class="header"><b>Visible?</b></td>'.
	'<td class="header"><b>Insertable?</b></td>'.
	'<td class="header"><b>Editable?</b></td>'.
	'<td class="header"><b>Borrable?</b></td>'.
	'<td class="header"><b>Permisos?</b></td>'.
	'<td class="header"><b>Pendiente?</b></td>'.
	'<td class="header"><b>Revisar?</b></td>'.
	'<td class="header"><b>Publicar?</b></td>'.
	'</tr>';
	
  while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
  {
    $p_rol_id = $row->rol_id;
    $res.='<tr><td class="omp_listelement">'.getPrivField('p_rol_class_id', $row['id']).'</td>
	<td class="omp_listelement">'.getClassName($row['class_id']).
	'</td><td class="omp_listelement">'.getPrivField('p_browseable', $row['browseable']).'</td>'.
	'</td><td class="omp_listelement">'.getPrivField('p_insertable', $row['insertable']).'</td>'.
	'</td><td class="omp_listelement">'.getPrivField('p_editable', $row['editable']).'</td>'.
	'</td><td class="omp_listelement">'.getPrivField('p_deleteable', $row['deleteable']).'</td>'.
	'</td><td class="omp_listelement">'.getPrivField('p_permisos', $row['permisos']).'</td>'.
	'</td><td class="omp_listelement">'.getPrivField('p_status1', $row['status1']).'</td>'.
	'</td><td class="omp_listelement">'.getPrivField('p_status2', $row['status2']).'</td>'.
	'</td><td class="omp_listelement">'.getPrivField('p_status3', $row['status3']).'</td>'.
	'</tr>';
  }
  $res.='</table>
  <input type="hidden" name="p_rol_id" value="'.$p_rol_id.'">
  <input type="submit" value="Guardar" class="omp_button"/></center>
  </form>';
  return $res;
}

function editPrivilege2($p_rol_class_id, $p_browseable, $p_insertable, $p_editable, $p_deleteable, $p_permisos, $p_status1, $p_status2, $p_status3) {
	global $dbh;
  $sql = 'update omp_roles_classes set 
  browseable="'.$p_browseable.'" 
  , insertable="'.$p_insertable.'" 
  , editable="'.$p_editable.'" 
  , deleteable="'.$p_deleteable.'" 
  , permisos="'.$p_permisos.'" 
  , status1="'.$p_status1.'" 
  , status2="'.$p_status2.'" 
  , status3="'.$p_status3.'" 
  where id = '.$p_rol_class_id;

  $ret = mysql_query($sql, $dbh);
  if(!$ret)
  {
    die("error:". mysql_error());
  }
}

function LogAccess ($p_inst_id, $p_tipo)
{
  LogAccessUser($_SESSION["user_id"], $p_inst_id, $p_tipo);
}

function DeleteLogAccess ($p_inst_id, $p_tipo)
{
  DeleteLogAccessUser($_SESSION["user_id"], $p_inst_id, $p_tipo);
}

function LogAccessUser ($p_user_id, $p_inst_id, $p_tipo) {
	global $dbh;
	$sql = "insert into omp_user_instances (user_id, inst_id, tipo_acceso) values (".$p_user_id.", ".$p_inst_id.", '".$p_tipo."');";

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}
}

function DeleteLogAccessUser ($p_user_id, $p_inst_id, $p_tipo) {
	global $dbh;
	$sql = "delete from omp_user_instances where user_id = ".$p_user_id." and inst_id = ".$p_inst_id." and tipo_acceso = '".$p_tipo."';";

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}
}


function getStatus1 ($p_class_id, $p_rol_id) {
	global $dbh;
	$sql = "select status1 st from omp_roles_classes where class_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_class_id))." and rol_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_rol_id))." ;";

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}

	while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
		if ($row['st']=="Y") {
			return 1;
		}
	}
	return 0;
}

function getStatus2 ($p_class_id, $p_rol_id) {
	global $dbh;
	$sql = "select status2 st from omp_roles_classes where class_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_class_id))." and rol_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_rol_id))." ;";

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}

	while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
		if ($row['st']=="Y") {
			return 1;
		}
	}

	return 0;
}

function getStatus3 ($p_class_id, $p_rol_id) {
	global $dbh;
	$sql = "select status3 st from omp_roles_classes where class_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_class_id))." and rol_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_rol_id))." ;";

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}

	while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
		if ($row['st']=="Y") {
			return 1;
		}
	}
	return 0;
}

function getStatus4 ($p_class_id, $p_rol_id) {
	global $dbh;
	$sql = "select status4 st from omp_roles_classes where class_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_class_id))." and rol_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_rol_id))." ;";

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}

	while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
		if ($row['st']=="Y") {
			return 1;
		}
	}

	return 0;
}

function getStatus5 ($p_class_id, $p_rol_id) {
	global $dbh;
	$sql = "select status5 st from omp_roles_classes where class_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_class_id))." and rol_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_rol_id))." ;";

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}

	while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
		if ($row['st']=="Y") {
			return 1;
		}
	}

	return 0;
}

function getStatusAccess ($p_inst_id, $p_rol_id) {
	global $dbh;
	$sql = "select i.status st, rc.status1 st1, rc.status2 st2, rc.status3 st3
	from omp_instances i, omp_roles_classes rc
	where i.id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id))."
	and rc.rol_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_rol_id))."
	and rc.class_id = i.class_id;";

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}

	while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
		if ($row['st']=="P" && $row['st1']=="Y") return 1;
		if ($row['st']=="V" && $row['st2']=="Y") return 1;
		if ($row['st']=="O" && $row['st3']=="Y") return 1;
	}
	return 0;
}


function getInstanceRoles ($p_inst_id, $p_class_id) {
	global $dbh;
  $sql = "select ir.* from omp_instances_roles ir 
  where ir.inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id)).";";

  $ret = mysql_query($sql, $dbh);
  if(!$ret)
  {
    die("error:". mysql_error());
  }

  $res='';
  $res.='<table>';
  $res.='<tr><td>Roles asignados</td></tr>';
  $res.='<tr><td>';
//Part de rols assignats
  $res.='<table border="1">';
  while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
  {
    $res.='<tr>';
	$res.='<td>';
	$res.=getRolName($row['rol_id']);
    $res.='</td>';
	$res.='<td>';
	$res.='<a href="controller.php?p_action=del_instrol&assign_id='.$row['Id'].'&amp;p_class_id='.$p_class_id.'&amp;p_inst_id='.$p_inst_id.'">desasigna</a>';
    $res.='</td>';
	$res.='</tr>';
  }
  $res.='</table>';
  $res.='</td></tr>';
  
  
  
  $res.='<tr><td><br/><br/><br/></td></tr>';
//Part de rols no assignats

  $sql = "select ir.rol_id from omp_instances_roles ir where ir.inst_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id)).";";
  $ret = mysql_query($sql, $dbh);
  $cont = 0;
  $flag = FALSE;
  $str_sql="";
  while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
  {
    if ($flag)
	  $str_sql.=', ';
	$str_sql.=$row['rol_id'];
	$flag=TRUE;
	$cont=$cont+1;
  }

  if ($cont == 0)
  {
    $sql = "select r.* from omp_roles r;";
  }
  else
  {
    $sql = "select r.id from omp_roles r where r.id not in (".$str_sql.");";
  }
  $ret = mysql_query($sql, $dbh);
  $res.='<tr><td>Roles disponibles</td></tr>';
  $res.='<tr><td>';
  $res.='<table border="1">';
  while ($row = mysql_fetch_array($ret, MYSQL_ASSOC))
  {
    $res.='<tr>';
	$res.='<td>';
	$res.=getRolName($row['id']);
    $res.='</td>';
	$res.='<td>';
	$res.='<a href="controller.php?p_action=add_instrol&p_rol_id='.$row['id'].'&amp;p_class_id='.$p_class_id.'&amp;p_inst_id='.$p_inst_id.'">asigna</a>';
    $res.='</td>';
	$res.='</tr>';
  }
  $res.='</table>';

  $res.='</td></tr>';

  
  
  $res.='</td></tr>';
  $res.='</table>';
  
  return $res;
}


function deleteInstanceRol($p_assign_id) {
	global $dbh;
	$sql = 'delete from omp_instances_roles where id='.$p_assign_id;

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		return "Error a l'eliminar: ".mysql_error();
	}
	else {
		return "Asignació eliminada satisfactoriamente";
	}
}


function addInstanceRol($p_inst_id, $p_rol_id) {
	global $dbh;
	$sql = 'insert into omp_instances_roles (inst_id, rol_id, creation_date) values  ('.$p_inst_id.', '.$p_rol_id.', now())';

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		return "Error a al crear l'assignació".mysql_error();
	}
	else {
		return "Asignació creada satisfactoriamente";
	}
}





function getAccess_PY ($p_camp_nom, $p_class_id, $p_rol_id) {
	global $dbh;
	$sql = "select ".str_replace("\"", "\\\"", str_replace("[\]","",$p_camp_nom))." x_able from omp_roles_classes where class_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_class_id))." and rol_id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_rol_id))." ;";

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}

	while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
		if ($row['x_able']=="Y") {
			return 1;
		}
	}
	return 0;
}
?>