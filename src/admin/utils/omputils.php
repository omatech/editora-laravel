<?php
/**
 * Meta portal utils
 *
 * @version $Id$
 * @copyright 2004 
 **/



///////////////////////////////////////////////////////////////////////////////////////////
function random_string($length) {
	srand((double)microtime() * 1000000);
	$possible_charactors = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$string = "";
	while (strlen($string)<$length) {
		$string .= substr($possible_charactors, (rand()%(strlen($possible_charactors))),1);
	}
	return($string);
}

///////////////////////////////////////////////////////////////////////////////////////////
function makeShortURL($URLToConvert) { // CONVERT TO TINY URL
	$shortURL= file_get_contents("http://tinyurl.com/api-create.php?url=" . $URLToConvert);
	return $shortURL;
}

///////////////////////////////////////////////////////////////////////////////////////////
function get_add_to_twitter($p_inst_id, $p_class_id) {
	$html='';	
	$html.='<form>
		<textarea id="example1" rows="5" cols="35" class="word_count"></textarea>
		<div id="example1_count" style="display:none"></div>
		<br />
		<input id="enviar_twitter" name="enviar_twitter" value="Enviar" type="button" onclick="do_twit()" />
	</form>';

	$javascript = "<script language=\"javascript\" type=\"text/javascript\">
		$(document).ready(function() {
			$('.word_count').each(function() {
				var input = '#' + this.id;
				var count = input + '_count';
				$(count).show();
				word_count(input, count, 140);
				$(this).keyup(function() { word_count(input, count, 140) });
			});
		});

		function word_count(field, count, maxcount) {
			var number = 0;
			var matches = $(field).val();
			if(matches) {
				number = parseInt(maxcount) - matches.length;
			}
			$(count).text( number + ' ".getMessage('caracters')."');
		}

		function do_twit() {
			var singleValues = $('textarea#example1').val();
			if(singleValues.length <= 140) {
				$.get ('".URL_APLI."/utils/twit.php', {message : singleValues}, function(data){
					$('div#example1_count').text(data);
				});
			}
			else {
				$('div#example1_count').text('".getMessage('too_long')."');
			}
		}

		function get_tiny() {
			$.get ('".URL_APLI."/utils/tinyurl.php', {inst : '".$p_inst_id."'}, function(data){
				$('textarea#example1').text(data);
			});
		}

		get_tiny();
	</script>";

	return $html.$javascript;
}

///////////////////////////////////////////////////////////////////////////////////////////
function getClassName($p_id) {
    $editora = new Omatech\Editora\Admin\Models\editoraModel;
    $class_info = $editora->get_class_info($p_id);
    return $class_info['class_name'];
}

///////////////////////////////////////////////////////////////////////////////////////////
function getTag ($p_inst_id) {
	global $dbh;

	$sql = "select tag from omp_classes where id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_inst_id)).";";
	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}

	while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
		return $row['tag'];
	}
	return 0;
}

///////////////////////////////////////////////////////////////////////////////////////////
function check_mandatories() {
	reset($_REQUEST);
	while (key($_REQUEST)) {
		$valor=current($_REQUEST);
		$nom=key($_REQUEST);

		if ($nom=='p_mandatories' && $valor!='') {
			$mandatories=explode(',', $valor);
		}

		if (isset($valor) && $valor<>'') {
			$part=strtok($nom, '_');
			if ($part=='atr') { // Estic escanejant un atribut, comprovo els flags
				$part=strtok('_');
				$flags=$part;
				$type=substr($flags,1,1);
				// Ara busquem el id del atribut
				$part=strtok('_');
				$atr_id=$part;
				$informats[]=$atr_id;
			}
		}
		next($_REQUEST);
	}
	if ($mandatories) { // Tenim algun mandatory, continuem mirant
		if ($informats) { // Tenim algun informat, comprovem si es un subconjunt dels mandatories
			$diff=array_diff($mandatories, $informats);
			return (count($diff)==0);
		}
		else { // Tenim mandatories xo no informats, error directe
			return false;
		}
	}  
	else { // No tenim cap mandatory, no te sentit fer el check !
		return true;
	}
}

///////////////////////////////////////////////////////////////////////////////////////////
function check_urlnice_back($p_inst_id) {
	global $dbh;
	reset($_REQUEST);
	while (key($_REQUEST)) {
		$valor=current($_REQUEST);
		$nom=key($_REQUEST);

		if (isset($valor) && $valor<>'') {
			$part=strtok($nom, '_');
			if ($part=='atr') {// Estic escanejant un atribut, comprovo els flags
				$part=strtok('_');
				$flags=$part;
				$type=substr($flags,1,1);
				// Ara busquem el id del atribut
				$part=strtok('_');
				$atr_id=$part;
				if ($type=='Z') {// url nice, check the uniqueness
					//Primer busquem el idioma del atribut que estem mirant per buscar-ho a omp_niceurl
					$sql='select language from omp_attributes where id="'.$atr_id.'"';
					$ret=mysql_query($sql,$dbh);
					$row=mysql_fetch_array($ret,MYSQL_ASSOC);

					$sql_add='';
					if ($p_inst_id!=null) {
						$sql_add='and inst_id!='.$p_inst_id.'';
					}
					$sql = 'select count(*) num from omp_niceurl where language="'.$row['language'].'" and niceurl="'.clean_url($valor).'" '.$sql_add;

					$ret = mysql_query($sql, $dbh);
					if(!$ret) {
						die("error:". mysql_error());
					}

					$row = mysql_fetch_array($ret, MYSQL_ASSOC);
					if ($row['num'] > 0) return false;
				}
			}
		}
		next($_REQUEST);
	}

	return true;
}

///////////////////////////////////////////////////////////////////////////////////////////
function keys_to_string ($p_keys) {
	$res='';
	if ($p_keys) {
		while ($valor=current($p_keys)) {
			$res .= $valor.',';
			next($p_keys);
		}
		return substr($res, 0, strlen($res)-1);
	}
	else {
		return '';
	}
}

///////////////////////////////////////////////////////////////////////////////////////////
function html_keys ($p_keys) {
	$res='';
	$res.='<table><tr><td><b>'.keys_to_string($p_keys).'</b></td></tr></table>';
	return $res;
}

///////////////////////////////////////////////////////////////////////////////////////////
function html_mandatory_chunk($p_mandatory) {
	if ($p_mandatory=="Y") {
		return("<span class=\"required\" title=\"Camp obligatori\">*</span>");
	}
}

///////////////////////////////////////////////////////////////////////////////////////////
function html_size_chunk($p_width, $p_height) {
	if ($p_width!=null || $p_height!=null) return "(".$p_width."x".$p_height.")";
	else return "";
}

///////////////////////////////////////////////////////////////////////////////////////////
function html_item_stack_postfix($p_mode) {
	return "</p></div></div>";
}

///////////////////////////////////////////////////////////////////////////////////////////
function html_item_stack_prefix($p_fila_ant, $p_columna_ant, $p_fila, $p_columna, $p_mode, $p_last = 1) {
	$ret="";
	if ($p_fila_ant==-1) { // Primera fila
		$ret .= "<div class='fila' rel='". $p_fila."'>";
	}
	if ($p_columna_ant==-1) { // Primera fila i columna
		$ret .= "<div class='columna'><p>";
	}

	if ($p_columna_ant!=-1) {
		if ($p_columna_ant!=$p_columna) { // Cambio de columna
			if ($p_fila_ant==$p_fila) { // En la misma fila
				$ret .= "</p></div><div class='columna'><p>";
			}
			elseif ( $p_last == 0) { // En la misma fila
				$ret .= "</p></div></div>";
			}
			else { // Canvi de columna i de fila
				$ret .= "</p></div></div><div class='fila' rel='". $p_fila."'><div class='columna'><p>";
			}
		}
		else { // Estamos en la misma columna
			if ($p_fila_ant!=$p_fila && $p_last != 0) { // Canvi nomes de fila
				$ret .= "</p></div></div><div class='fila' rel='". $p_fila."'><div class='columna'><p>";
			}
		}
	}
	$ret .= "\n";
	return $ret;
}

///////////////////////////////////////////////////////////////////////////////////////////
function order_link($url,$p_order_by,$a_fer) {
	if ($p_order_by!='') {
		$explode_ordre=explode('--',$p_order_by);
		if ($a_fer==$explode_ordre[0]) {
			if ($explode_ordre[1]=='asc') $ad='desc';
			elseif ($explode_ordre[1]=='desc') $ad='asc';
			else $ad='desc';
		}
		else $ad='desc';
	}
	else $ad='desc';
	
	return $url.'&p_order_by='.$a_fer.'--'.$ad;
}

///////////////////////////////////////////////////////////////////////////////////////////
function getRolName ($rol_id) {
	global $dbh;

	$sql = "select r.rol_name from omp_roles r where id = ".str_replace("\"", "\\\"", str_replace("[\]","",$rol_id)).";";
	
	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}
	$row = mysql_fetch_array($ret, MYSQL_ASSOC);

	return $row['rol_name'];
}

///////////////////////////////////////////////////////////////////////////////////////////
function getDefaultLanguage() {
    if (isset($_REQUEST['u_lang'])) {
        $lg=control_idioma($_REQUEST['u_lang']);
    }elseif (isset($_COOKIE['u_language'])) {
		$lg=control_idioma($_COOKIE['u_language']);
	}elseif (isset($_SESSION['u_lang'])) {
		$lg=control_idioma($_SESSION['u_lang']);
	}else{
		$lg=control_idioma('');
	}
	setcookie('u_language', control_idioma($lg), -1 , '/', DOMAIN_SERVER);
	$_SESSION['u_lang']=$lg;
	
	return $lg;
}

///////////////////////////////////////////////////////////////////////////////////////////
function selectedTrue($val1, $val2) {
	if ($val1==$val2 && $val1!='') return 'selected="true"';
	return '';
}

///////////////////////////////////////////////////////////////////////////////////////////
function getWH_Attribute ($p_attr_id, $tipus) {
	global $dbh;
	$sql = "select a.".$tipus." info from omp_attributes a where a.id = ".$p_attr_id;

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}

	$row = mysql_fetch_array($ret, MYSQL_ASSOC);

	return $row['info'];
}

///////////////////////////////////////////////////////////////////////////////////////////
function getWH_Class_Attribute ($p_attr_id, $inst_id, $tipus) {
	global $dbh;
	$sql = "select ca.".$tipus." info from omp_class_attributes ca, omp_instances i where i.id = ".$inst_id." and ca.class_id = i.class_id and ca.atri_id = ".$p_attr_id;

	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}
	$row = mysql_fetch_array($ret, MYSQL_ASSOC);

	return $row['info'];
}

///////////////////////////////////////////////////////////////////////////////////////////
function html_inst_preview ($p_class_id, $inst_id) {
	$res='';
	$res.='<form action="/controller_prev.php" method="post">';
		 $res.='<input type="hidden" id="p_action" name="p_action" value="'.getTag($p_class_id).'" />';
		 $res.='<input type="hidden" name="inst" value="'.$inst_id.'"/>';
		 $res.='<table>';
			 $res.='<tr>';
				 $res.='<td>';
				 $res.=getMessage('login_label_language').'<select name="lang">';
				 global $array_langs;
				 foreach ($array_langs as $l) {
				   $res.='<option value="'.$l.'">'.$l.'</option>';
				 }
				 $res.='</select>';
				 $res.='</td>';
				 $res.='<td>';
				 $res.=getMessage('preview_format').'<select name="format">';
				 $res.='</select>';
				 $res.='</td>';
				 $res.='<td>';
				 $res.=getMessage('preview_debug').'<input type="checkbox" name="req_debug" value="1"/>';
				 $res.='</td>';
			 $res.='</tr>';
			 $res.='<tr>';
				 $res.='<td colspan="4" align="center"><input type="submit" value="'.getMessage('preview_enviar').'"/></td>';
			 $res.='</tr>';
		 $res.='</table>';
	$res.='</form>';
	return $res;
}

///////////////////////////////////////////////////////////////////////////////////////////
function get_massive_file($p_relation_id) {
	global $dbh;
	$sql = "SELECT massive_file FROM omp_relations WHERE id=".$p_relation_id;
	$ret = mysql_query($sql, $dbh);
	if ($ret) {
		$row=mysql_fetch_array($ret, MYSQL_ASSOC);
		if ($row) return $row['massive_file'];
		else return '';
	}
	else return '';
}

///////////////////////////////////////////////////////////////////////////////////////////
function createExcel($filename, $data) {
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-type: text/csv");
	header ('Content-Disposition: attachment; filename="'.$filename.'"');
	echo $data;
}

///////////////////////////////////////////////////////////////////////////////////////////
function getInstName($inst_id) {
	global $dbh;
	$sql="select v.text_val as value
	from omp_values v, omp_instances i, omp_class_attributes ca, omp_attributes a
	where i.id = ".$inst_id."
	and i.id=v.inst_id 
	and ca.id=v.atri_id
	and a.tag='nom_intern'
	and a.id=ca.atri_id
	and v.atri_id=a.id";
	
	$ret=mysql_query($sql,$dbh);
	if(!$ret) {
		return '';
	}
	$row=mysql_fetch_array($ret,MYSQL_ASSOC);
	
	return $row['value'];
}

///////////////////////////////////////////////////////////////////////////////////////////
function clean_url( $url, $id = '') {
	if ('' == $url) return $url;
	$url = trim($url);
	$url=strip_tags($url);

	$search = array(
		"à", "á", "â", "ã", "ä", "À", "Á", "Â", "Ã", "Ä",
		"è", "é", "ê", "ë", "È", "É", "Ê", "Ë",
		"ì", "í", "î", "ï", "Ì", "Í", "Î", "Ï",
		"ó", "ò", "ô", "õ", "ö", "Ó", "Ò", "Ô", "Õ", "Ö",
		"ú", "ù", "û", "ü", "Ú", "Ù", "Û", "Ü",
		",", ".", ";", ":", "`", "´", "<", ">", "?", "}",
		"{", "ç", "Ç", "~", "^", "Ñ", "ñ"
	);
	$change = array(
		"a", "a", "a", "a", "a", "A", "A", "A", "A", "A",
		"e", "e", "e", "e", "E", "E", "E", "E",
		"i", "i", "i", "i", "I", "I", "I", "I",
		"o", "o", "o", "o", "o", "O", "O", "O", "O", "O",
		"u", "u", "u", "u", "U", "U", "U", "U",
		" ", "-", " ", " ", " ", " ", " ", " ", " ", " ",
		" ", "c", "C", " ", " ", "NY", "ny"
	);

	$url = strtoupper(str_ireplace($search,$change,$url));
	$temp=explode("/",$url);
	$url=$temp[count($temp)-1];

	$url = preg_replace('|[^a-z0-9-~+_. #=&;,/:]|i', '', $url);
	$url = str_replace('/', '', $url);
	$url = str_replace(' ', '-', $url);
	$url = str_replace('&', '', $url);
	$url = str_replace("'", "", $url);
	$url = str_replace(';//', '://', $url);
	$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);

	$url=strtolower($url);

	//ultims canvis
	$url = trim(str_replace("[^ A-Za-z0-9_-]", "", $url));
	$url = str_replace("[ \t\n\r]+", "-", $url);
	$url = str_replace("[ -]+", "-", $url);

	if ($id == '') return $url;

	return $url."-".$id;
}

///////////////////////////////////////////////////////////////////////////////////////////
function getListImage($inst_id) {
    $editora = new Omatech\Editora\Admin\Models\editoraModel;
    $image = $editora->getInstanceImage($inst_id);
    if ($image!=null){
        return '<img src="'.$image.'" style="max-height:50px; max-width:50px">';
    }else{
        return '';
    }
}

///////////////////////////////////////////////////////////////////////////////////////////
function clean_file_name( $filename) {
	if ('' == $filename){
		return $filename;
	}
	$filename = trim($filename);
	
	$filename = iconv('UTF-8','ASCII//TRANSLIT',$filename);
	$replace_chars = [
		"à"=>'a', "á"=>'a', "â"=>'a', "ã"=>'a', "ä"=>'a', "À"=>'A', "Á"=>'A', "Â"=>'A', "Ã"=>'A', "Ä"=>'A',
		"è"=>'e', "é"=>'e', "ê"=>'e', "ë"=>'e', "È"=>'E', "É"=>'E', "Ê"=>'E', "Ë"=>'E',
		"ì"=>'i', "í"=>'i', "î"=>'i', "ï"=>'i', "Ì"=>'I', "Í"=>'I', "Î"=>'I', "Ï"=>'I',
		"ó"=>'o', "ò"=>'o', "ô"=>'o', "õ"=>'o', "ö"=>'o', "Ó"=>'O', "Ò"=>'O', "Ô"=>'O', "Õ"=>'O', "Ö"=>'O',
		"ú"=>'u', "ù"=>'u', "û"=>'u', "ü"=>'u', "Ú"=>'U', "Ù"=>'U', "Û"=>'U', "Ü"=>'U',
		","=>'', ";"=>'', ":"=>'', "`"=>'', "´"=>'', "<"=>'', ">"=>'', "?"=>'', "}"=>'',
		"{"=>'', "ç"=>'c', "Ç"=>'C', "~"=>'', "^"=>'', "Ñ"=>'NY', "ñ"=>'ny', 
		"/"=>"-", " "=>"-","'"=>"-","(C)"=>"c"
		];

	$output_filename = strtr($filename, $replace_chars);
	return $output_filename;
}