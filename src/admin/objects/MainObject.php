<?php
// à

class MainObject
{
	var $xml;
	var $arr;
	var $arr_temp;
	var $params;
	var $arr_v;
	//definexi si volem que faci la paginació al while_xml
	var $auto_paginate = true;
	var $dbh;

	//////////////////////////////////////////////////////////////////////////////////////////
	function __construct() {
		global $dbh;

		$this->dbh = $dbh;
		$this->params['paginacio'] = 'n';
		return;
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	function get_data($sql) {
		global $dbh;

		$ret = mysql_query($sql, $dbh);
		if (!$ret) {
			debug('Error en mysql function get_data: '.mysql_error($dbh));
			return false;
		}
		else {
			$arr=array();
			while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
				array_push($arr, $row);
			}
			if (count($arr)>0) return $arr;
			else return false;
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	function get_one($sql) {
		global $dbh;

		$ret = mysql_query($sql, $dbh);
		if (!$ret) {
			debug('Error en mysql function get_one: '.mysql_error($dbh));
			return false;
		}
		else {
			$row = mysql_fetch_array($ret, MYSQL_ASSOC);
			if ($row) return $row;
			else return false;
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	function insert_one($sql) {
		global $dbh;

		$ret = mysql_query($sql, $dbh);
		if (!$ret) {
			debug('Error en mysql function insert_one: '.mysql_error($dbh));
			return false;
		}
		else {
			$id = mysql_insert_id($dbh);
			if ($id) return $id;
			else return false;
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	function update_one($sql) {
		global $dbh;

		$ret = mysql_query($sql, $dbh);
		if (!$ret) {
			debug('Error en mysql function update_one: '.mysql_error($dbh));
			return false;
		}
		else {
			return true;
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	function delete_one($sql) {
		return $this->update_one($sql);
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	function execute($sql) {
		global $dbh;
		$ret = mysql_query($sql, $dbh);
		if (!$ret) {
			debug('Error en mysql function execute: '.mysql_error($dbh));
			return false;
		}
		$id = mysql_insert_id($dbh);

		return $id;
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	function escape($string) {
		global $dbh;
		return mysql_real_escape_string($string, $dbh);
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	function setItems($items, $arr_v = array()) {
		foreach($items as $key => $item) {
			$this->pushParam($key, $item);
		}
		$this->arr_v = $arr_v;
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	function pushParam($name, $param) {
		$this->params[$name]=$param;
	}

	////////////////////////////////////////////// GET FILLS ////////////////////////////////////////////////////////////////
	function getFills($arr_v, $p_inst_id, $p_lang, $extra_params = array()) {
		$res='';
		if (is_array($arr_v)) {
			reset($arr_v);
			$cont=0;
			while($cont <= sizeof($arr_v)) {
				if (isset($arr_v[$cont])) {
					$Value = $arr_v[$cont];
					if (is_array($Value)) {
						$info=explode(",",trim($Value[0]));
						$classe=$info[0];
						$function_name=$info[1].'()';
						$parametres = tractaParametres($info[2], $p_inst_id, $p_lang, 0, $extra_params);

						$class_name = trim($classe);
						if (control_classe($class_name)) {
							eval('require_once(DIR_OBJECTS."/$class_name.php");');
							eval('$mo = new $class_name;');
						}
						else {
							eval('require_once(DIR_OBJECTS."/MainObject.php");');
							eval('$mo = new MainObject;');
						}
						$mo->setItems($parametres, $Value);
						eval('$res.= $mo->'.$function_name.';');
					}
				}
				$cont+=1;
			}
		}

		return $res;
	}

	////////////////////////////////////////////// ALL INSTANCE ////////////////////////////////////////////////////////////////
	function allinstance() {
		$this->rel_type = 'allinstance';
		$sql2 = '';
		if (isset($this->params['year']) && $this->params['year']) {
			$year = $this->params['year'];
			$sql2=' AND YEAR(publishing_begins)='.$year;
		}
		if (isset($this->params['month']) && $this->params['month']) {
			$month = $this->params['month'];
			$sql2.=' AND MONTH(publishing_begins)='.$month;
		}
		$sql='SELECT id as child_inst_id
		FROM omp_instances
		WHERE class_id='.$this->params['class_id'];
		$sql.= $sql2;
		$sql.=' {{req_info}} ORDER BY publishing_begins DESC;';

		$this->xml.='<allinstances_'.$this->params['tag'].'>';
			$this->xml.=$this->while_xml($sql);
		$this->xml.='</allinstances_'.$this->params['tag'].'>';

		return $this->xml;
	}

	////////////////////////////////////////////// ALL INSTANCE DESC ////////////////////////////////////////////////////////////////
	function allinstance_ordre() {
		$this->rel_type = 'allinstance';

		if (isset($this->params['filter']) && $this->params['filter']) {
			$sql2='SELECT i.id as child_inst_id
			FROM omp_instances i, omp_values v
			WHERE class_id='.$this->params['class_id'].'
			AND i.id = v.inst_id AND v.num_val='.$this->params['filter'];
		}
		else {
			$sql2 = '';
		}

		$sql='SELECT i.id as child_inst_id
		FROM omp_instances i, omp_values v
		WHERE class_id='.$this->params['class_id'].'
		AND i.id = v.inst_id AND v.atri_id='.$this->params['camp_ordenar'];
		if(!empty($sql2)) $sql.=' AND i.id in ('.$sql2.')';
		$sql.='{{req_info}} ORDER BY v.text_val DESC;';

		$this->xml.='<allinstances_'.$this->params['tag'].'>';
			$this->xml.=$this->while_xml($sql);
		$this->xml.='</allinstances_'.$this->params['tag'].'>';

		return $this->xml;
	}

	////////////////////////////////////////////// ALL INSTANCE ASC ////////////////////////////////////////////////////////////////
	function allinstance_asc() {
		$this->rel_type = 'allinstance';

		$sql='SELECT id as child_inst_id
		FROM omp_instances
		WHERE class_id='.$this->params['class_id'].'
		{{req_info}} ORDER BY publishing_begins ASC;';

		$this->xml.='<allinstances_'.$this->params['tag'].'>';
			$this->xml.=$this->while_xml($sql);
		$this->xml.='</allinstances_'.$this->params['tag'].'>';

		return $this->xml;
	}

	////////////////////////////////////////////// ALL INSTANCE AB ////////////////////////////////////////////////////////////////
	function allinstance_ab() {
		$this->rel_type = 'allinstance';

		$sql='SELECT i.id as child_inst_id
		FROM omp_instances i, omp_values v
		WHERE class_id='.$this->params['class_id'].'
			AND v.atri_id in (10,11,12)
			AND v.inst_id = i.id
		{{req_info}} ORDER BY text_val ASC;';

		$this->xml.='<allinstances_'.$this->params['tag'].'>';
			$this->xml.=$this->while_xml($sql);
		$this->xml.='</allinstances_'.$this->params['tag'].'>';

		return $this->xml;
	}

	////////////////////////////////////////////// INSTANCE ////////////////////////////////////////////////////////////////
	function instance() {
		$this->rel_type = 'instance';
		if (isset($this->params['tag_name']) && $this->params['tag_name']) $this->xml.='<'.$this->params['tag_name'].'>'.chr(13).chr(10);
		else $this->xml.='<'.$this->params['tag'].'>'.chr(13).chr(10);

		$xml_cache=$this->getInstanceCache($this->params['inst_id'], $this->params['lang'], $this->params['mostrar'], null, null);
		if ($xml_cache) {
			$this->xml.=$xml_cache;
		}
		else {
			$chunk=$this->getInstanceInfo($this->params['inst_id'], $this->params['lang'], null, null, $this->params['mostrar'],-1);

			if (MEMCACHE_ENABLED)
			{
			$mc=new Memcache;
			$memcacheAvailable=$mc->connect('localhost', 11211);
			if ($memcacheAvailable) {
				$key=DIR_APLI.':'.$this->params['inst_id'].':'.$this->params['lang'].':'.$this->params['mostrar'];
				$exists=$mc->get($key);
				if (!$exists) {// insertamos
					$cache_chunk=str_replace('rel_type=""', 'rel_type="%cache_name%"', $chunk);
					$mc->set($key, $cache_chunk, MEMCACHE_COMPRESSED, 3600);
				}
			}
			}

			$this->xml.=$chunk;
		}

		$this->xml.=$this->getFills($this->arr_v, $this->params['inst_id'], $this->params['lang']);
		if (isset($this->params['tag_name']) && $this->params['tag_name']) $this->xml.='</'.$this->params['tag_name'].'>'.chr(13).chr(10);
		else $this->xml.='</'.$this->params['tag'].'>'.chr(13).chr(10);

		return $this->xml;
	}

	////////////////////////////////////////////// RANDOM ////////////////////////////////////////////////////////////////
	function random() {
		$this->rel_type = 'random';

		$sql="SELECT i.id as child_inst_id";
		$sql.=" FROM omp_instances i ";
		$sql.=" WHERE class_id={$this->params['class_id']}";
		$sql.=" {{req_info}} ORDER BY rand() {{limit}};";

		$this->xml.=$this->while_xml($sql);
		return $this->xml;
	}

	////////////////////////////////////////////// CHILD ////////////////////////////////////////////////////////////////
	function child() {
		$this->rel_type = 'child';

		$sql="SELECT child_inst_id, rel_id";
		$sql.=" FROM omp_relation_instances ri, omp_instances i, omp_classes c";
		if (isset($this->params['rel_name']) && $this->params['rel_name']!='') $sql.=" , omp_relations r";
		$sql.=" WHERE ri.parent_inst_id = ".$this->params['inst_id']." AND i.id = ri.child_inst_id";
		$sql.=" AND c.id = i.class_id";
		if (isset($this->params['rel_id']) && $this->params['rel_id']!=0) $sql.=" AND rel_id=".$this->params['rel_id']."";
		else $sql.=" AND c.tag = '".$this->params['tag']."'";
		if (isset($this->params['rel_name']) && $this->params['rel_name']!='') $sql.=" AND ri.rel_id=r.id AND r.name='".$this->params['rel_name']."'";
		$sql.="{{req_info}}
		ORDER BY ri.weight ASC;";

		$this->xml.=$this->while_xml($sql);
		return $this->xml;
	}

	////////////////////////////////////////////// FATHER ////////////////////////////////////////////////////////////////
	function father() {
		$this->rel_type = 'father';

		$sql="SELECT parent_inst_id as child_inst_id, rel_id";
		$sql.=" FROM omp_relation_instances ri, omp_instances i, omp_classes c";
		if (!empty($this->params['rel_name']) &&  $this->params['rel_name'] <> '') $sql.=" , omp_relations r";
		$sql.=" WHERE ri.child_inst_id = ".$this->params['inst_id']." AND i.id = ri.parent_inst_id";
		$sql.=" AND c.id = i.class_id";
		if (isset($this->params['rel_id']) && $this->params['rel_id']!=0) $sql.=" AND rel_id = '".$this->params['rel_id']."'";
		else $sql.=" AND c.tag = '".$this->params['tag']."'";
		if ( !empty($this->params['rel_name']) &&  $this->params['rel_name'] <> '') $sql.=" AND ri.rel_id=r.id AND r.tag like '".$this->params['rel_name']."'";

        $sql.="{{req_info}} ORDER BY ri.weight, publishing_begins DESC {{limit}};";
		$this->xml.=$this->while_xml($sql);
		return $this->xml;
	}

	////////////////////////////////////////////// TRACTA SQL ////////////////////////////////////////////////////////////////
	private function tractaSql($sql) {
		$search = array();
		$replace = array();

		$req_info = '';
		if (!isset($_REQUEST['req_info']) || $_REQUEST['req_info']==0) {
			$req_info.=" and status = 'O' ";
			$req_info.=" and DATE_FORMAT(publishing_begins,'%Y%m%d%H%i%S') <= now()+0";
			$req_info.=" and IFNULL(DATE_FORMAT(publishing_ends,'%Y%m%d%H%i%S'),now()+1) > now()+0";
		}
		array_push($search, '{{req_info}}');
		array_push($replace, $req_info);

		//PAGINACIO
		$limit = '';
		if (xx($this->params['obj'])) $limit = "limit {$this->params['obj']}";
		if ($this->params['paginacio'] == 'y') $limit = '';
		array_push($search, '{{limit}}');
		array_push($replace, $limit);

		$sql = str_ireplace($search, $replace, $sql);

		return $sql;
	}

	////////////////////////////////////////////// WHILE XML ////////////////////////////////////////////////////////////////
	function while_xml($sql = '') {
		if(empty($sql) && $this->auto_paginate == true) die('Falta la sql');

		// inicialitzem valors
		if (!empty($this->params['tag_name'])) $this->tag = $this->params['tag_name'];
		else $this->tag = $this->params['tag'];

		$rel_name = '';
		if (isset($this->params['rel_name'])) $rel_name= $this->params['rel_name'];
		// FI inicialització

		if($this->auto_paginate == true) {
			$this->execute_sql($sql);
			$this->xml .= $this->get_paginacio();
		}

		if(!isset($this->from_ROW) || is_null($this->from_ROW)) $this->from_ROW = 0;
		if(empty($this->params['obj'])) $this->params['obj'] = 0;
		if(mysql_num_rows($this->result) == 0) return $this->bad_result();
		if (!mysql_data_seek($this->result, $this->from_ROW)) {
			echo "Cannot seek to row $this->from_ROW: " . mysql_error() . "\n";
		}

		$i=0;
		while (($row = mysql_fetch_array($this->result, MYSQL_ASSOC)) && ($i < $this->params['obj'])) {
			$rel_id = '';
			if(!empty($row['rel_id'])) $rel_id = $row['rel_id'];

			$this->xml.="<{$this->tag} rel_name='{$rel_name}' rel_id='{$rel_id}'>".chr(13).chr(10);

			$xml_cache=$this->getInstanceCache($row['child_inst_id'], $this->params['lang'], $this->params['mostrar'], $this->rel_type, $rel_id);
			if ($xml_cache) {
				$this->xml.=$xml_cache;
			}
			else  {
				$chunk=$this->getInstanceInfo($row['child_inst_id'], $this->params['lang'], $this->rel_type, $rel_id, $this->params['mostrar'],-1);

		if (MEMCACHE_ENABLED)
		{
                        global $mc;
			//$mc=new Memcache;
			//$memcacheAvailable=$mc->connect('localhost', 11211);
			//if ($memcacheAvailable) {
				$key=DIR_APLI.':'.$row['child_inst_id'].':'.$this->params['lang'].':'.$this->params['mostrar'];
				$exists=$mc->get($key);
				if (!$exists) {// insertamos
					$cache_chunk=str_replace('rel_type="'.$this->rel_type.'"', 'rel_type="%cache_name%"', $chunk);
					$cache_chunk=str_replace('rel="'.$rel_id.'"', 'rel="%cache_relid%"', $cache_chunk);
					$mc->set($key, $cache_chunk, MEMCACHE_COMPRESSED, 3600);
				}
                                //$mc->close();
			//}

		}
		$this->xml.=$chunk;
		}

			$this->xml.=$this->getFills($this->arr_v, $row['child_inst_id'], $this->params['lang']);
			$this->xml.="</{$this->tag}>".chr(13).chr(10);
			$i++;
		}
	}

	////////////////////////////////////////////// EXECUTE SQL ////////////////////////////////////////////////////////////////
	protected function execute_sql($sql) {
		if (!$this->dbh) {
			return $this->bad_result();
		}

		$sql = $this->tractaSql($sql);
		$this->result = mysql_query($sql,$this->dbh);
		if(!$this->result) {
			return $this->bad_result();
		}
		$this->num_ROWS = mysql_num_rows($this->result);
		$this->from_ROW = null;

		return true;
	}

	////////////////////////////////////////////// GET PAGINACIO ////////////////////////////////////////////////////////////////
	protected function get_paginacio() {
		if ($this->params['paginacio'] == 'y') { //ES PAGINABLE
			$num=$this->num_ROWS;
			$max_pag=ceil($num/$this->params['obj']);
			$blocs = 9;

			if (isset($this->params['num_paginacio']) && $this->params['num_paginacio']<>'') $blocs = $this->params['num_paginacio'];

			$this->xml.=$this->generaXML_paginacio($this->params['lang'], $this->tag, $_SESSION['pagina_paginacio'], $max_pag, $blocs);
			$this->from_ROW = (($_SESSION['pagina_paginacio']-1)*$this->params['obj']);
		}
	}

	////////////////////////////////////////////// BAD RESULT ////////////////////////////////////////////////////////////////
	private function bad_result() {
		return "<{$this->tag}/>".chr(13).chr(10);
	}

	////////////////////////////////////////////// GET INSTANCE CACHE ////////////////////////////////////////////////////////////////
	function getInstanceCache($id, $lang, $mostrar, $relation, $rel_id) {
		// nou codi, cache amb memcache, apons 20130919
		if (MEMCACHE_ENABLED)
		{
                  global $mc;

		//$mc=new Memcache;
		//$memcacheAvailable=$mc->connect('localhost', 11211);
		//if ($memcacheAvailable) {
			$key=DOCUMENT_ROOT.':'.$id.':'.$lang.':'.$mostrar;
                        //var_dump($mc);
			$memcache_value=$mc->get($key);
			if ($memcache_value) {
				$xml=str_replace('%cache_name%',$relation,$memcache_value);
				$xml=str_replace('%cache_relid%',$rel_id,$xml);
				return $xml;
			}
                        //$mc->close();
		//}
		}
		// fi nou codi, cache amb memcache

		if ($mostrar=='R') $mostrar='_r';
		elseif ($mostrar=='D') $mostrar='_d';
		else return 0;

		global $dbh;
		if (!$dbh) return 0;

		$sql="SELECT xml_cache".$mostrar." as cache FROM omp_instances_cache ic, omp_instances i
		WHERE inst_id=".$id." AND language='".$lang."'
		{{req_info}};";
		$ret=mysql_query($sql,$dbh);

		if ($ret) {
			$row=mysql_fetch_array($ret,MYSQL_ASSOC);
			if ($row) {
				if ($memcacheAvailable && !$memcache_value) {
					$mc->set($key, $row['cache'], MEMCACHE_COMPRESSED, 3600);
				}
				$xml=str_replace('%cache_name%',$relation,$row['cache']);
				$xml=str_replace('%cache_relid%',$rel_id,$xml);
				return $xml;
			}
			else return 0;
		}
		else return 0;

		return 0;
	}

	function getInstanceInfo($p_instance_id, $p_lang, $rel_type, $p_relacio, $p_detail,$usuari) {
		$res = "";
		global $dbh;
		if (!$dbh) {
			echo "Error connect: ";
			return "Error connect: ";
		}

		$sql="SELECT i.key_fields key_fields, i.status status, i.publishing_begins publi_b, i.publishing_ends publi_e, i.class_id type, i.creation_date, i.update_date, c.name c_nom, c.id c_id
		FROM omp_instances i, omp_classes c
		WHERE i.id = ".$p_instance_id." AND c.id = i.class_id;";
		$sql2="SELECT a.id a_id, a.tag tag, a.type type, v.text_val val_text, v.date_val val_data, v.num_val val_num, v.img_info, ca.id ca_id
		FROM omp_instances i, omp_attributes a, omp_values v, omp_class_attributes ca
		WHERE i.id = ".$p_instance_id."
		AND (a.language = '".$p_lang."' or a.language = 'ALL')
		AND v.inst_id = i.id
		AND v.atri_id = a.id
		AND a.id = ca.atri_id
		AND i.class_id = ca.class_id";

		if ($p_detail == 'R') $sql2.=" and ca.detail = 'N';";
		/*  /IDIOMA/NICE-URL/ACCIO/FORMAT */

		$ret = mysql_query($this->tractaSql($sql),$dbh);
		$ret2 = mysql_query($sql2,$dbh);

		$lanice=get_nice_from_id ($p_instance_id, $p_lang);
		$erlang='';
		if (MULTI_LANG == 1) $erlang='/'.$p_lang;

		$row = @mysql_fetch_array($ret, MYSQL_ASSOC);

		$res.='<pub_date_b>'.$row['publi_b'].'</pub_date_b>'.chr(13).chr(10);
		$res.='<pub_date_e>'.$row['publi_e'].'</pub_date_e>'.chr(13).chr(10);
		$res.='<auto_link>'.chr(13).chr(10);
		$res.='<link name="simple">'.$erlang.'/'.$lanice.'</link>'.chr(13).chr(10);
		$res.='</auto_link>'.chr(13).chr(10);

		$res.='<instance id="'.$p_instance_id.'" status="'.$row['status'].'" tipus="'.$row['type'].'" lang="'.$p_lang.'" rel="'.$p_relacio.'" rel_type="'.(xx($rel_type)?$rel_type:$this->rel_type).'" class_name="'.$row['c_nom'].'" nice="'.$lanice.'" creation="'.$row['creation_date'].'" publishing="'.$row['publi_b'].'" update="'.$row['update_date'].'">'.chr(13).chr(10);
			if(isset ($_SESSION['rol_id']) && $_SESSION['rol_id']!='' && $row['type']!="" && $p_instance_id!="") {
				$res.="<edit>'".APP_BASE."'/view_instance?p_class_id=".$row['type']."&amp;p_inst_id=$p_instance_id</edit>";
			}

			while ($row2 = @mysql_fetch_array($ret2, MYSQL_ASSOC)) {
				//Deixar tots en una sola linea, del contrari estem introduïnt salts de linea fora del CDATA dins dels tags d'xml i pot afectar al funcionament.
				if ($row2['type'] == 'D') {
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'" unix_time="'.strtotime($row2['val_data']).'">'.mysql_to_date($row2['val_data'],true).'</'.$row2['tag'].'>'.chr(13).chr(10);
				}
				elseif ($row2['type'] == 'N') {
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'"><![CDATA['.$row2['val_num'].']]></'.$row2['tag'].'>'.chr(13).chr(10);
				}
				elseif ($row2['type'] == 'L') {
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'" num_val="'.$row2['val_num'].'" value="'.get_true_value($row2['val_num'], $p_lang).'"><![CDATA['.get_value($row2['val_num'], $p_lang).']]></'.$row2['tag'].'>'.chr(13).chr(10);
				}
				elseif ($row2['type'] == 'A') {
                    //$new_val=nl2br($row2['val_text']);
                    $new_val=$row2['val_text'];
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'"><![CDATA['.$new_val.']]></'.$row2['tag'].'>'.chr(13).chr(10);
				}
				elseif ($row2['type'] == 'K') {
					$new_val=$row2['val_text'];
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'"><![CDATA['.$new_val.']]></'.$row2['tag'].'>'.chr(13).chr(10);
				}
				elseif ($row2['type'] == 'T') {
					$new_val=$row2['val_text'];
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'"><![CDATA['.$new_val.']]></'.$row2['tag'].'>'.chr(13).chr(10);
				}
				elseif ($row2['type'] == 'M') {
					$pos=explode("@",$row2['val_text']);
					$new_val=explode(":",$pos[0]);
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'"><lat><![CDATA['.$new_val[0].']]></lat><long><![CDATA['.$new_val[1].']]></long></'.$row2['tag'].'>'.chr(13).chr(10);
				}
				elseif ($row2['type'] == 'I') {
					$wh=$row2['img_info'];
					$swh=(string)$wh;
					$swh=str_replace(',', '.', $swh);
					$ii=explode('.', $swh);
					$img_url=$row2['val_text'];
					if (substr($img_url, 0, 7)=='uploads') $img_url='/'.$img_url;
					$img = '';
					if(!empty($img_url)) $img = str_replace(chr(10), "<br />", $img_url);
					$width = '';
					if(!empty($ii[0])) $width = $ii[0];
					$height = '';
					if(!empty($ii[1])) $height = $ii[1];
					$res.='<'.$row2['tag'].' width="'.$width.'" height="'.$height.'" type="'.$row2['type'].'"><![CDATA['.$img.']]></'.$row2['tag'].'>'.chr(13).chr(10);
				}
				elseif ($row2['type']== 'C') {
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'">';
						$res.='<![CDATA[';
							$fn = create_function('$lg, $inst_id',$row2['val_text']);
							$res.= $fn($p_lang, $_SESSION['actual_inst']);
						$res.=']]>';
					$res.='</'.$row2['tag'].'>'.chr(13).chr(10);
				}
				elseif ($row2['type'] == 'F') {
					$arxiu=DIR_APLI.'/'.$row2['val_text'];
					if (file_exists($arxiu)) $mida_arxiu=round((filesize($arxiu)/1024),1);
					else $mida_arxiu=0;
					$img_url=$row2['val_text'];
					if (substr($img_url, 0, 7)=='uploads') $img_url='/'.$img_url;
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'" mida="'.$mida_arxiu.'"><![CDATA['.str_replace(chr(10), "<br />", $img_url).']]></'.$row2['tag'].'>'.chr(13).chr(10);
				}
				elseif ($row2['type'] == 'G') {
					$img_url=$row2['val_text'];
					if (substr($img_url, 0, 7)=='uploads') $img_url='/'.$img_url;
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'"><![CDATA['.$img_url.']]></'.$row2['tag'].'>'.chr(13).chr(10);
				}
				else {
					$new_val=$row2['val_text'];
					if (function_exists('extract_'.$row2['ca_id'])) {
						$fe = 'extract_'.$row2['ca_id'];
						$new_val = $fe($row2['val_text']);
					}
					else {
						$new_val = extract_default($row2['val_text']);
					}
					$res.='<'.$row2['tag'].' type="'.$row2['type'].'"><![CDATA['.$new_val.']]></'.$row2['tag'].'>'.chr(13).chr(10);
				}
			}
		$res.='</instance>'.chr(13).chr(10);

		return $res;
	}

	protected function generaXML_paginacio($lg, $tag, $pag_actual, $max_pages, $blocs) {
		$ret="";
		$cont=1;
		$mitat_bloc=floor($blocs/2);

		$ret.='<paginacio_'.$tag.' blocs="'.$blocs.'" lang="'.$lg.'">'.chr(13).chr(10);
			$ret.='<max_pagines>'.$max_pages.'</max_pagines>'.chr(13).chr(10);
			$ret.='<act_pagines>'.$pag_actual.'</act_pagines>'.chr(13).chr(10);
			if ($pag_actual > 1) $ret.='<primer>1</primer>'.chr(13).chr(10);
			if ($pag_actual < $max_pages) $ret.='<ultim>'.$max_pages.'</ultim>'.chr(13).chr(10);
			if ($max_pages <= $blocs) {
				while ($cont<=$max_pages) {
					if ($cont==$pag_actual) $ret.='<pag act="Y">'.$cont.'</pag>'.chr(13).chr(10);
					else $ret.='<pag act="N">'.$cont.'</pag>'.chr(13).chr(10);
					$cont=$cont+1;
				}
			}
			else {
				$cont = $pag_actual - $mitat_bloc;
				if ($cont<=0) $cont=1;
				if (($pag_actual + $mitat_bloc) > $max_pages) {
					$cont=$pag_actual -($blocs-($max_pages-$pag_actual));
				}
				while ($cont<$pag_actual) {
					$ret.='<pag act="N">'.$cont.'</pag>'.chr(13).chr(10);
					$cont=$cont+1;
				}
				$ret.='<pag act="Y">'.$cont.'</pag>';
				$cont=$cont+1;
				if (($pag_actual - $mitat_bloc) <= 0) {
					while (($cont<=$blocs) && ($cont <= $max_pages)) {
						$ret.='<pag act="N">'.$cont.'</pag>'.chr(13).chr(10);
						$cont=$cont+1;
					}
				}
				else {
					while ($cont<=($pag_actual+$mitat_bloc) && ($cont <= $max_pages)) {
						$ret.='<pag act="N">'.$cont.'</pag>'.chr(13).chr(10);
						$cont=$cont+1;
					}
				}
			}
		$ret.="</paginacio_".$tag.">";

		return $ret;
	}
}
