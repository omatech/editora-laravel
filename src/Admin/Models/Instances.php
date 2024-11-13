<?php

namespace Omatech\Editora\Admin\Models;
use Illuminate\Support\Facades\Session;

class Instances extends model
{
    private function generaQuery($p_search_query, $p_class_id, $p_fecha_ini, $p_fecha_fin, $p_search_state, $p_parent_inst_id, &$sql_add, &$from_add, $p_mode, $p_relation_id = '')
    {
        if ($p_class_id) { // TODO: Aki s'hauria de mirar a kines classes pot accedir el menda
            $p_class_id = str_replace("\"", "\\\"", str_replace("[\]", "", $p_class_id));
            $sql_add.=' and i.class_id='.$p_class_id.' ';
        }

        if ($p_class_id==0 && $p_mode=='R') {
            $sql="select multiple_child_class_id as mcci from omp_relations r where id = ".$p_relation_id;
            $Row = parent::get_one($sql);
            $sql_add.='and i.class_id in ('.$Row['mcci'].')';
        }

        if ($p_fecha_ini) {
            $sql_add.=' and i.creation_date >= "'.date_to_mysql($p_fecha_ini).'" ';
        }
        if ($p_fecha_fin) {
            $p_fecha_fin = str_replace("\"", "\\\"", str_replace("[\]", "", $p_fecha_fin));
            $sql_add.=' and DATE_SUB(i.creation_date, INTERVAL 1 DAY) <= "'.date_to_mysql($p_fecha_fin).'" ';
        }

        if ($p_search_state) {
            $sql_add.=' and i.status = "'.$p_search_state.'"';
        }

        if ($p_search_query) {
            $p_search_query = str_replace("\"", "\\\"", str_replace("[\]", "", $p_search_query));
            $sql_add.=' and (v.text_val LIKE "%'.$p_search_query.'%" or v.inst_id = "'.$p_search_query.'") and v.inst_id = i.id';
            $from_add=', omp_values v ';
        }
    }

    function instanceList($param_arr)
    {
        $sql_add='';
        $from_add='';

        $p_search_query      = $param_arr['param4'];
        $p_class_id         = $param_arr['param1'];
        $p_fecha_ini         = str_replace("-", "/", $param_arr['param5']);
        $p_fecha_fin        = str_replace("-", "/", $param_arr['param6']);
        $p_pagina            = $param_arr['param3'];
        $p_order_by        = $param_arr['param7'];
        $p_search_state    = $param_arr['param8'];
        $p_mode             = $param_arr['p_mode'];
        $p_parent_inst_id    = $param_arr['param10'];
        $p_relation_id     = $param_arr['param9'];

        $this->generaQuery($p_search_query, $p_class_id, $p_fecha_ini, $p_fecha_fin, $p_search_state, $p_parent_inst_id, $sql_add, $from_add, $p_mode, $p_relation_id);

        $first_row=0;
        if ($p_pagina) {
            $first_row=($p_pagina-1)*ROWS_PER_PAGE;
        }

        $sql="select i.id id
		, i.key_fields key_fields
		, i.status status
		, c.name class_name
		, c.name_".Session::get('u_lang') ." class_realname
		, date_format(i.publishing_begins, '".STANDARD_DATE_FORMAT."') publishing_begins
		, publishing_begins pb_ordre
		, date_format(i.publishing_ends, '".STANDARD_DATE_FORMAT."') publishing_ends
		, date_format(i.creation_date, '".STANDARD_DATE_FORMAT."') creation_date
		, update_date cd_ordre
		, c.id class_id
		, rc.editable edit
		, rc.deleteable deletea
		from omp_instances i
		".$from_add."
		, omp_classes c
		, omp_roles_classes rc
		where i.class_id = c.id ";
        if ($p_mode=='R') {
            $sql.=" and i.id!=".$p_parent_inst_id." ";
        }
        $sql.=$sql_add."
		and rc.class_id = i.class_id
		and rol_id=".Session::get('rol_id')."
		and browseable='Y'
		group by i.id
		, i.key_fields
		, i.status
		, c.name
		, c.name_".Session::get('u_lang') ."
		, i.publishing_begins
		, publishing_begins
		, i.publishing_ends
		, i.creation_date
		, update_date
		, c.id
		, rc.editable
		, rc.deleteable ";

        if ($p_order_by!='' && $p_order_by!=DEFAULT_NULL_STR) {
            $explode_order=explode('--', $p_order_by);
            $sql.=" order by ".$explode_order[0]." ".$explode_order[1];
        } else {
            $sql.=" order by i.update_date desc";
        }
        $sql.=" limit $first_row,".ROWS_PER_PAGE;

        return $this->get_data($sql) ?: [];
    }

    function getInstInfo($p_inst_id = null)
    {
        if ($p_inst_id == null) {
            return array();
        }
        $sql = "SELECT i.id, i.status, i.key_fields key_fields
		, date_format(i.publishing_begins, '" . STANDARD_DATE_FORMAT . "') publishing_begins
		, date_format(i.publishing_ends, '" . STANDARD_DATE_FORMAT . "') publishing_ends
		, i.creation_date creation_date, c.name class_name, c.id class_id, i.external_id, i.batch_id
		from omp_classes c, omp_instances i
		where i.class_id = c.id
		and i.id = " . $p_inst_id . "  ";
        $ret = $this->get_one($sql);
        if (!$ret) {
            return array();
        }
        return $ret;
    }

    function instanceList_count($param_arr)
    {
        $sql_add = '';
        $from_add = '';

        $p_search_query = $param_arr['param4'];
        $p_class_id = $param_arr['param1'];
        $p_fecha_ini = str_replace("-", "/", $param_arr['param5']);
        $p_fecha_fin = str_replace("-", "/", $param_arr['param6']);
        $p_order_by = $param_arr['param7'];
        $p_search_state = $param_arr['param8'];
        $p_mode = $param_arr['p_mode'];
        $p_parent_inst_id = $param_arr['param10'];
        $p_relation_id = $param_arr['param9'];

        $this->generaQuery($p_search_query, $p_class_id, $p_fecha_ini, $p_fecha_fin, $p_search_state, $p_parent_inst_id, $sql_add, $from_add, $p_mode, $p_relation_id);

        $sql_count = "select count(distinct i.id) as conta
		from omp_instances i
		" . $from_add . "
		, omp_classes c
		, omp_roles_classes rc
		where i.class_id = c.id ";
        if ($p_mode == 'R') {
            $sql_count .= " and i.id!=" . $p_parent_inst_id . " ";
        }
        $sql_count .= $sql_add . "
		and rc.class_id = i.class_id
		and rol_id=" . Session::get('rol_id') . "
		and browseable='Y'";

        $ret = parent::get_one($sql_count);
        if (!$ret) {
            return 0;
        }

        return $ret['conta'];
    }

    function getLastInstances()
    {
        return $this->getUserBoxInstances(Session::get('user_id'), 'A', 'desc', 10);
    }

    function getFavorites()
    {
        return $this->getUserBoxInstances(Session::get('user_id'), 'F', 'asc', 30);
    }

    function getUserBoxInstances($p_user_id, $p_tipo, $order, $limit)
    {
        $sql = "select i.id, i.status, i.class_id, i.key_fields, max(ui.fecha) fecha
		from omp_instances i
		, omp_user_instances ui
		where ui.user_id = " . $p_user_id . "
		and ui.tipo_acceso='" . $p_tipo . "'
		and ui.inst_id = i.id
		group by i.id, i.status, i.class_id, i.key_fields
		order by fecha " . $order . "
		limit " . $limit;

        $ret = parent::get_data($sql);
        if (!$ret) {
            return array();
        } else {
            $instances = [];
            foreach ($ret as $item) {
                $instances[$item['id']] = $item;
            }

            return $instances;
        }
    }

    function getParents($param_arr, $actualitzats = array())
    {
        $p_inst_id = $param_arr['param2'];

        $sql = "select i.id id
		, i.key_fields key_fields
		, i.status status
		, c.name class_name
		, c.name_" . Session::get('u_lang') . " class_realname
		, date_format(i.publishing_begins, '" . STANDARD_DATE_FORMAT . "') publishing_begins
		, date_format(i.publishing_ends, '" . STANDARD_DATE_FORMAT . "') publishing_ends
		, date_format(i.creation_date, '" . STANDARD_DATE_FORMAT . "') creation_date
		, c.id class_id
		, ri.id ri_id
		, ri.rel_id ri_rel_id
		from omp_classes c, omp_instances i, omp_relation_instances ri
		where ri.child_inst_id = " . $p_inst_id . " and ri.parent_inst_id = i.id and i.class_id = c.id";
        if (count($actualitzats) > 0) {
            $sql .= " and ri.parent_inst_id not in (-1," . implode(",", $actualitzats) . ")";
        }
        $sql .= " group by i.id
		 , i.key_fields
		, i.status
		, c.name
		, c.name_" . Session::get('u_lang') . "
		, i.publishing_begins
		, i.publishing_ends
		, i.creation_date
		, c.id
		, ri.id
		, ri.rel_id
		order by i.update_date desc";

        $ret = $this->get_data($sql);

        if (!$ret) {
            return array();
        }

        return $ret;
    }

    function getDistinctParents($param_arr, $actualitzats = array())
    {
        $p_inst_id = $param_arr['param2'];

        $sql = "select i.id id
		, i.key_fields key_fields
		, i.status status
		, c.name class_name
		, c.name_" . Session::get('u_lang') . " class_realname
		, date_format(i.publishing_begins, '" . STANDARD_DATE_FORMAT . "') publishing_begins
		, date_format(i.publishing_ends, '" . STANDARD_DATE_FORMAT . "') publishing_ends
		, date_format(i.creation_date, '" . STANDARD_DATE_FORMAT . "') creation_date
		, c.id class_id
		from omp_classes c, omp_instances i, omp_relation_instances ri
		where ri.child_inst_id = " . $p_inst_id . " and ri.parent_inst_id = i.id and i.class_id = c.id";
        if (count($actualitzats) > 0) {
            $sql .= " and ri.parent_inst_id not in (-1," . implode(",", $actualitzats) . ")";
        }
        $sql .= " group by i.id
		 , i.key_fields
		, i.status
		, c.name
		, c.name_" . Session::get('u_lang') . "
		, i.publishing_begins
		, i.publishing_ends
		, i.creation_date
		, c.id
		order by i.update_date desc";

        $ret = $this->get_data($sql);

        if (!$ret) {
            return array();
        }

        return $ret;
    }

    function getChilds($param_arr, $actualitzats = array())
    {
        $p_inst_id = $param_arr['param2'];

        $sql = "select i.id id
		, i.key_fields key_fields
		, i.status status
		, c.name class_name
		, c.name_" . Session::get('u_lang') . " class_realname
		, date_format(i.publishing_begins, '" . STANDARD_DATE_FORMAT . "') publishing_begins
		, date_format(i.publishing_ends, '" . STANDARD_DATE_FORMAT . "') publishing_ends
		, date_format(i.creation_date, '" . STANDARD_DATE_FORMAT . "') creation_date
		, c.id class_id
		, ri.id ri_id
		, ri.rel_id ri_rel_id
		from omp_classes c, omp_instances i, omp_relation_instances ri
		where ri.parent_inst_id = " . $p_inst_id . " and ri.child_inst_id = i.id and i.class_id = c.id";
        if (count($actualitzats) > 0) {
            $sql .= " and ri.child_inst_id not in (-1," . implode(",", $actualitzats) . ")";
        }
        $sql .= " group by i.id
		, i.key_fields
		, i.status
		, c.name
		, c.name_" . Session::get('u_lang') . "
		, i.publishing_begins
		, i.publishing_ends
		, i.creation_date
		, c.id
		, ri.id
		, ri.rel_id
		order by i.update_date desc";

        $ret = parent::get_data($sql);
        if (!$ret) {
            return array();
        }
        return $ret;
    }

    function getBrothers($param_arr)
    {
        $p_inst_id = $param_arr['param2'];
        if (isset($param_arr['relation_id']) && $param_arr['relation_id'] > 0) {
            $p_rel_id = $param_arr['relation_id'];
        }

        $sql = "select i.id id
		, i.key_fields key_fields
		, i.status status
		, c.name class_name
		, c.name_" . Session::get('u_lang') . " class_realname
		, date_format(i.publishing_begins, '" . STANDARD_DATE_FORMAT . "') publishing_begins
		, date_format(i.publishing_ends, '" . STANDARD_DATE_FORMAT . "') publishing_ends
		, date_format(i.creation_date, '" . STANDARD_DATE_FORMAT . "') creation_date
		, c.id class_id
		, ri.id ri_id
		, ri.rel_id ri_rel_id
		from omp_classes c, omp_instances i, omp_relation_instances ri,
		(
			select ri.rel_id ri_relid
			from omp_relation_instances ri
			where 1=1";
        if (isset($param_arr['relation_id']) && $param_arr['relation_id'] > 0) {
            $sql = " and ri.rel_id = " . $p_rel_id;
        }

        $sql .= "	and ri.child_inst_id = " . $p_inst_id . " group by ri.id
		) rel_list
		where rel_list.ri_relid = ri.rel_id and ri.child_inst_id = i.id and i.class_id = c.id and i.id <> " . $p_inst_id . "
		group by i.id
		, i.key_fields
		, i.status
		, c.name
		, c.name_" . Session::get('u_lang') . "
		, i.publishing_begins
		, i.publishing_ends
		, i.creation_date
		, c.id
		, ri.id
		, ri.rel_id
		order by i.update_date desc";

        $ret = parent::get_data($sql);
        if (!$ret) {
            return array();
        }
        return $ret;
    }

    function logAccess($param_arr)
    {
        $p_inst_id = $param_arr['param2'];
        $p_tipo = $param_arr['p_acces_type'];
        $this->LogAccessUser(Session::get("user_id"), $p_inst_id, $p_tipo);
    }

    function deleteLogAccess($param_arr)
    {
        $p_inst_id = $param_arr['param2'];
        $p_tipo = $param_arr['p_acces_type'];
        $this->DeleteLogAccessUser(Session::get("user_id"), $p_inst_id, $p_tipo);
    }

    private function LogAccessUser($p_user_id, $p_inst_id, $p_tipo)
    {
        $sql = "insert into omp_user_instances (user_id, inst_id, tipo_acceso) values (" . $p_user_id . ", " . $p_inst_id . ", '" . $p_tipo . "');";
        $ret = parent::insert_one($sql);
        if (!$ret) {
            die("error:" . mysql_error());
        }
    }

    private function DeleteLogAccessUser($p_user_id, $p_inst_id, $p_tipo)
    {
        $sql = "delete from omp_user_instances where user_id = " . $p_user_id . " and inst_id = " . $p_inst_id . " and tipo_acceso = '" . $p_tipo . "';";
        parent::execute($sql);
    }


    private function get_insert_chunk($p_type, $p_valor)
    {
        // Asumim ordre de insert TEXT,DATE,NUM, img_info
        $p_valor = trim(str_replace("\xc2\xa0", ' ', $p_valor));
        if ($p_type == 'D' || $p_type == 'E') {
            $ret = 'NULL, "'.date_to_mysql($p_valor).'",NULL, NULL';
        } elseif ($p_type == 'M') {
            $ret = '"'.$p_valor.'",NULL, NULL, NULL';
        } elseif ($p_type == 'L') {
            $ret = 'NULL, NULL, "'.$p_valor.'", NULL';
        } elseif ($p_type == 'N') {
            $ret = 'NULL, NULL, "'.$p_valor.'", NULL';
        } elseif ($p_type == 'C') {
            $ret = '"'.$p_valor.'",NULL, NULL, NULL';
        } elseif ($p_type == 'I') { // Es imatge
            if (GD_LIB && $p_valor != '') { // tenim el GD activat, precalculem el width i el height
                if (!filter_var($p_valor, FILTER_VALIDATE_URL)) {
                    $file = public_path($p_valor);
                } else {
                    $file = $p_valor;
                }
                $ii = @getimagesize($file);
                if ($ii) {
                    $wh = $ii[0].'.'.$ii[1];
                    $ret = '"'.$p_valor.'",NULL, NULL, "'.$wh.'"';
                } else {
                    $ret = '"'.$p_valor.'",NULL, NULL, NULL';
                }
            } else {
                $ret = '"'.$p_valor.'",NULL, NULL, NULL';
            }
        } elseif ($p_type == 'Y') { //Es link de Youtube
            $p_valor = $this->getParseVideo($p_valor);
            $ret = '"'.$p_valor.'", NULL, NULL, NULL';
        } else {
            $ret = '"'.$p_valor.'", NULL, NULL, NULL';
        }
        return $ret;
    }

    function getParseVideo($p_valor)
    {
        if (strpos($p_valor, 'youtube.com') || strpos($p_valor, 'youtu.be')) {
            $explode_youtube = explode('/', $p_valor);
            $p_valor = $explode_youtube[count($explode_youtube) - 1];
            if (strpos($p_valor, '=') != false) {
                $explode_youtube = explode('=', $p_valor);
                $p_valor = $explode_youtube[1];
            }
            if (strpos($p_valor, '&') != false) {
                $explode_youtube = explode('&', $p_valor);
                $p_valor = $explode_youtube[0];
            }
            $p_valor = 'youtube:'.$p_valor;
        } elseif (strpos($p_valor, 'vimeo.com')) {
            $explode_vimeo1 = explode('vimeo.com/', $p_valor);
            $explode_vimeo2 = explode('/', $explode_vimeo1[count($explode_vimeo1) - 1]);
            $explode_vimeo2 = explode('#', $explode_vimeo2[0]);
            $p_valor = 'vimeo:'.$explode_vimeo2[count($explode_vimeo2) - 1];
        } elseif (strpos($p_valor, 'tv3.cat')) {
            $explode_tv3 = explode('/', $p_valor);
            $p_valor = 'tv3:'.$explode_tv3[count($explode_tv3) - 1];
            if (is_nan($explode_tv3[count($explode_tv3) - 1])) {
                $p_valor = 'tv3:'.$explode_tv3[count($explode_tv3) - 2];
            }
        } elseif (!strpos($p_valor, 'http://') && $p_valor != '') {
            $explode_nice = explode(':', $p_valor);
            if ($explode_nice[0] != 'nicepeople' && $explode_nice[0] != 'youtube' && $explode_nice[0] != 'vimeo' && $explode_nice[0] != 'tv3') {
                $p_valor = 'nicepeople:'.$p_valor;
            }
        }

        return $p_valor;
    }

    function insertAttributes($param_arr)
    {
        $p_id = $param_arr['param1'];
        $p_inst_id = $param_arr['param2'];
        if (!check_mandatories()) {
            return -1;
        }
        if (!$this->check_urlnice($p_inst_id)) {
            return -3;
        }
        $res = '';
        if ($_REQUEST['p_status'] == null) {
            $inst_status = 'P';
        } else {
            $inst_status = str_replace("\"", "\\\"", str_replace("[\]", "", $_REQUEST['p_status']));
        }
        if (isset($_REQUEST['external_id']) && $_REQUEST['external_id'] != null) {
            $external_id = $_REQUEST['external_id'];
        } else {
            $external_id = null;
        }
        if ($_REQUEST['p_publishing_begins'] == null) {
            // Es un update
            if ($p_inst_id) {
                $inst_publishing_begins = 'now()';
            } else {
                $inst_publishing_begins = 'SYSDATE()';
            }
        } else {
            $inst_publishing_begins = $_REQUEST['p_publishing_begins'];
            if (!isDate($inst_publishing_begins)) {
                return -2;
            }
            $inst_publishing_begins = '"'.date_to_mysql(str_replace("\"", "\\\"", str_replace("[\]", "", $inst_publishing_begins))).'"';
        }

        if ($_REQUEST['p_publishing_ends'] == null) {
            $inst_publishing_ends = 'NULL';
        } else {
            $inst_publishing_ends = $_REQUEST['p_publishing_ends'];
            if (!isDate($inst_publishing_ends)) {
                return -2;
            }
            $inst_publishing_ends = '"'.date_to_mysql(str_replace("\"", "\\\"", str_replace("[\]", "", $inst_publishing_ends))).'"';
        }
        if ($p_inst_id) {
            $sql = 'update omp_instances
			set status="'.$inst_status.'" ,
				publishing_begins='.$inst_publishing_begins.',
				publishing_ends='.$inst_publishing_ends.'
			where id='.$p_inst_id;
            parent::update_one($sql);

            $sql = 'delete from omp_values where inst_id='.$p_inst_id;
            parent::execute($sql);
            $new_instance_id = $p_inst_id;
        } else {
            $sql = 'select t.nou_id
			from(
				select (max(id)+1) as nou_id from omp_instances where id >= '.USERINSTANCES.'
				union
				select 1 as nou_id from dual) t
			order by t.nou_id desc
			limit 1';
            $row = parent::get_one($sql);
            if (isset($row['nou_id']) && $row['nou_id'] > USERINSTANCES) {
                $nou_id = $row['nou_id'];
            } else {
                $nou_id = USERINSTANCES;
            }
            $sql = 'insert into omp_instances (id, class_id, status, publishing_begins, publishing_ends, creation_date, external_id)
			values ('.$nou_id.','.$p_id.',"'.$inst_status.'",'.$inst_publishing_begins.','.$inst_publishing_ends.',now(), "'.$external_id.'")';
            $new_instance_id = parent::insert_one($sql);
        }

        reset($_REQUEST);

        while (key($_REQUEST)) {
            $valor = current($_REQUEST);
            $nom = key($_REQUEST);

            if (isset($valor) && $valor <> '') {
                $part = strtok($nom, '_');
                if ($part == 'atr') { // Estic escanejant un atribut, comprovo els flags
                    $part = strtok('_');
                    $flags = $part;
                    $type = substr($flags, 1, 1);
                    // Ara busquem el id del atribut
                    $part = strtok('_');
                    $atr_id = $part;
                    // Ara busquem si l'atribut es clau
                    $part = strtok('_');
                    $key_order = $part;
                    if ($key_order) {
                        $keys[$key_order] = $valor;
                    }
                    if (is_array($valor)) { // L'atribut te varios valors, es un lookup de tipus checkbox
                        for ($j = 0; $j < sizeof($valor); $j++) {
                            $insert_chunk = $this->get_insert_chunk($type, $valor[$j]);
                            $sql = '
							insert into omp_values (inst_id, atri_id, text_val, date_val, num_val, img_info)
							values ('.$new_instance_id.', '.$atr_id.', '.$insert_chunk.');';
                            parent::insert_one($sql);
                        }
                    } else {
                        $valor = addslashes($valor);
                        $insert_chunk = $this->get_insert_chunk($type, $valor);
                        switch ($type) {
                            case "Z":
                                $sql_lan = "select language from omp_attributes where id=".$atr_id;
                                $row_lan = parent::get_one($sql_lan);
                                $sql = 'select count(1) as nicecount from omp_niceurl where inst_id = '.$new_instance_id.' and language = "'.$row_lan['language'].'";';
                                $ret = parent::get_one($sql);

                                $niceURL = $this->getUniqueNiceURL($new_instance_id, $row_lan['language'], clean_url($valor));

                                if (!$ret || $ret['nicecount'] == 0) {
                                    $sql = 'insert into omp_niceurl (inst_id,language,niceurl) values ('.$new_instance_id.', "'.$row_lan['language'].'", "'.$niceURL.'");';
                                    parent::insert_one($sql);
                                } else {
                                    $sql = 'update omp_niceurl
                                        set niceurl = "'.$niceURL.'"
                                        where inst_id = '.$new_instance_id.'
                                        and language = "'.$row_lan['language'].'";';
                                    parent::update_one($sql);
                                }
                                break;
                            case "W":
                                require_once($_SERVER['DOCUMENT_ROOT'].'/Util/appslib/Appscrapper.php');
                                $Appscrapper = new Appscrapper();
                                if (strpos($valor, 'itunes.apple')) {
                                    $appdata = $Appscrapper->loaditunesData($valor);
                                } elseif (strpos($valor, 'play.google')) {
                                    $appdata = $Appscrapper->loadgoogleplayData($valor);
                                }
                                $sql = 'insert into omp_values (inst_id, atri_id, text_val, date_val, num_val, img_info) values ('.$new_instance_id.', '.$atr_id.', \''.mysql_real_escape_string($appdata).'\',NULL,NULL,NULL);';
                                parent::insert_one($sql);
                                break;
                            case "B":
                                //update en omp_instances el valor para poder ordenar
                                $sql = 'update omp_instances set order_string = "'.$valor.'" where id = '.$new_instance_id.';';
                                parent::update_one($sql);

                                //insertamos el valor en omp_values como normalmente hacemos
                                $sql = 'insert into omp_values (inst_id, atri_id, text_val, date_val, num_val, img_info) values ('.$new_instance_id.', '.$atr_id.', '.$insert_chunk.');';
                                parent::insert_one($sql);
                                break;
                            case "E":
                                //update en omp_instances el valor para poder ordenar
                                $sql = 'update omp_instances set order_date = "'.date_to_mysql($valor).'" where id = '.$new_instance_id.';';
                                parent::update_one($sql);

                                //insertamos el valor en omp_values como normalmente hacemos
                                $sql = 'insert into omp_values (inst_id, atri_id, text_val, date_val, num_val, img_info) values ('.$new_instance_id.', '.$atr_id.', '.$insert_chunk.');';
                                parent::insert_one($sql);
                                break;
                            case "J":
                                //If not exists, create json_val in omp_values for save json value.
                                if (!$this->getExistsColumnDatabase('omp_values', 'json_val')) {
                                    $result_json_val = $this->createJsonVal();
                                }
                                $json_insert_chunk = $this->get_insert_chunk($type, '');
                                $sql = 'insert into omp_values (inst_id, atri_id, text_val, date_val, num_val, img_info, json_val) values ('.$new_instance_id.', '.$atr_id.', '.$json_insert_chunk.', "'.$valor.'");';
                                parent::insert_one($sql);
                                break;
                            case "Y":
                                //If not exists, create json_val in omp_values for save json info for video.
                                if (!$this->getExistsColumnDatabase('omp_values', 'json_val')) {
                                    $result_json_val = $this->createJsonVal();
                                }
                                if ($this->getExistsColumnDatabase('omp_values', 'json_val')) {
                                    //Get json value from API video
                                    $json_val = $this->getJsonValVideo($this->getParseVideo($valor));
                                    if (!empty($json_val)) {
                                        $sql = 'insert into omp_values (inst_id, atri_id, text_val, date_val, num_val, img_info, json_val) values ('.$new_instance_id.', '.$atr_id.', '.$insert_chunk.', "'.addslashes($json_val).'");';
                                    } else {
                                        $sql = 'insert into omp_values (inst_id, atri_id, text_val, date_val, num_val, img_info) values ('.$new_instance_id.', '.$atr_id.', '.$insert_chunk.');';
                                    }
                                } else {
                                    $sql = 'insert into omp_values (inst_id, atri_id, text_val, date_val, num_val, img_info) values ('.$new_instance_id.', '.$atr_id.', '.$insert_chunk.');';
                                }
                                parent::insert_one($sql);
                                break;
                            case "N":
                                // if the value isn't numeric, don't insert anything
                                if (!is_numeric($valor)) {
                                    break;
                                }

                                $sql = 'insert into omp_values (inst_id, atri_id, text_val, date_val, num_val, img_info) values ('.$new_instance_id.', '.$atr_id.', '.$insert_chunk.');';
                                parent::insert_one($sql);
                                break;
                            default:
                                $sql = 'insert into omp_values (inst_id, atri_id, text_val, date_val, num_val, img_info) values ('.$new_instance_id.', '.$atr_id.', '.$insert_chunk.');';
                                parent::insert_one($sql);
                                break;
                        }
                    }
                }
            } else { //Cas en que el valor es buit, s'ha d'actualitzar la taula omp_niceurl per si de cas em borrar el valor.
                $part = strtok($nom, '_');
                if ($part == 'atr') {
                    $part = strtok('_');
                    $flags = $part;
                    $type = substr($flags, 1, 1);
                    // Ara busquem el id del atribut
                    $part = strtok('_');
                    $atr_id = $part;
                    // Ara busquem si l'atribut es clau
                    $part = strtok('_');
                    $key_order = $part;
                    if ($key_order) {
                        $keys[$key_order] = $valor;
                    }
                    if (!is_array($valor)) {
                        if ($type == 'Z') {
                            $sql_lan = "select language from omp_attributes where id=" . $atr_id;
                            $row_lan = parent::get_one($sql_lan);
                            $sql = 'update omp_niceurl
                            set niceurl = null
                            where inst_id = '.$new_instance_id.'
                            and language = "'.$row_lan['language'].'";';
                            parent::update_one($sql);
                        } elseif ($type == 'B') {
                            //update en omp_instances el valor para poder ordenar
                            $sql = 'update omp_instances set order_string = NULL where id = '.$new_instance_id.';';
                            parent::update_one($sql);
                        } elseif ($type == 'E') {
                            //update en omp_instances el valor para poder ordenar
                            $sql = 'update omp_instances set order_date = NULL where id = '.$new_instance_id.';';
                            parent::update_one($sql);
                        }
                    }
                }
            }
            next($_REQUEST);
        }

        if ($keys) {
            sort($keys);
            $res .= keys_to_string($keys);
        }




        $sql = 'update omp_instances
        set external_id = "'.$external_id.'"
        where id='.str_replace("\"", "\\\"", str_replace("[\]", "", $new_instance_id));
        parent::update_one($sql);


        $sql = 'update omp_instances
        set key_fields = "'.str_replace("\"", "\\\"", str_replace("[\]", "", keys_to_string($keys))).'"
        where id='.str_replace("\"", "\\\"", str_replace("[\]", "", $new_instance_id));

        parent::update_one($sql);
        $res = $new_instance_id;

        return $res;
    }

    function getJsonValVideo($p_valor)
    {

        $video = explode(':', $p_valor);
        $json_val = NULL;

        try {
            if (!empty($video) && isset($video[0]) && isset($video[1])) {

                switch ($video[0]) {

                    case 'vimeo':
                        $json_val = file_get_contents("http://vimeo.com/api/v2/video/$video[1].json");
                        break;

                    case 'youtube':
                        break;

                    default:
                        break;
                }
            }
        } catch (\Exception $e) {
        }

        return $json_val;
    }
    function getExistsColumnDatabase($table, $column)
    {
        $sql = "SHOW COLUMNS FROM $table LIKE '$column'; ";
        $query_result = parent::get_data($sql);
        return $query_result;
    }

    function createJsonVal()
    {
        $create = false;
        $sql = "show columns from omp_values";
        $rows = parent::get_data($sql);

        $json_val_found = false;
        foreach ($rows as $row) {
            if ($row['Field'] == 'json_val')
                $json_val_found = true;
        }
        if (!$json_val_found) {
            $sql = "alter table omp_values add column json_val TEXT default null\n";
            parent::insert_one($sql);
            $create = true;
        }

        return $create;
    }

    function checkDeleteInstance($param_arr)
    {
        $mix_pf = array();
        $mix_pf['pares'] = $this->getParents($param_arr);
        $mix_pf['fills'] = $this->getChilds($param_arr);
        return $mix_pf;
    }

    function deleteInstance($param_arr)
    {
        $p_inst_id = $param_arr['param2'];

        $sql = "delete from omp_values where inst_id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id)) . ";";
        parent::execute($sql);

        $sql = "delete from omp_niceurl where inst_id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id)) . ";";
        parent::execute($sql);

        $sql = "delete from omp_relation_instances where parent_inst_id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id)) . ";";
        parent::execute($sql);

        $sql = "delete from omp_relation_instances where child_inst_id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id)) . ";";
        parent::execute($sql);

        $sql = "delete from omp_instances where id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id)) . ";";
        parent::execute($sql);

        return getMessage('info_object_deleted');
    }

    function deleteImage($full_path)
    {
        if (file_exists($full_path) == true) {
            if (unlink($full_path)) {
                return 1;
            }
        }
        return 0;
    }

    function checkDeleteInstanceArr($p_inst_arr)
    {
        $delete_array = array();
        foreach ($p_inst_arr as $value) {
            //Mirem el total de fills que té.
            $sql = "select count(*) as total
			from omp_instances ic
			, omp_relation_instances ric
			, omp_instances i
			where ric.parent_inst_id = i.id
			and ric.child_inst_id = ic.id
			and i.id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $value['param2'])) . ";";
            $ret = parent::get_one($sql);
            if (!$ret) {
                return array('unexpeted');
            }

            $sql = "select ic.id id, key_fields as key_fields, name_" . Session::get('u_lang') . " as name, status, class_id
			from omp_instances ic
			, omp_classes oc
			where  ic.id = '" . str_replace("\"", "\\\"", str_replace("[\]", "", $value['param2'])) . "'
			and oc.id = ic.class_id;";
            $ret2 = parent::get_one($sql);
            if (!$ret2) {
                return array('unexpeted');
            }

            $ret2['num_fills'] = $ret['total'];
            array_push($delete_array, $ret2);
        }

        return $delete_array;
    }

    function deleteInstanceArr($p_inst_idArr)
    {
        foreach ($p_inst_idArr as $p_inst_id) {
            $sqlArr = array(
                "delete from omp_values where inst_id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id['param2'])) . ";",
                "delete from omp_niceurl where inst_id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id['param2'])) . ";",
                "delete from omp_relation_instances where parent_inst_id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id['param2'])) . ";",
                "delete from omp_relation_instances where child_inst_id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id['param2'])) . ";",
                "delete from omp_instances where id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id['param2'])) . ";"
            );
            foreach ($sqlArr as $sql) {
                parent::execute($sql);
            }
        }
        return getMessage('info_object_deleted_plural');
    }

    function getClassID_from_Instance($p_inst_id)
    {
        $sql = "select class_id c_id from omp_instances where id = " . str_replace("\"", "\\\"", str_replace("[\]", "", $p_inst_id)) . ";";
        $ret = parent::get_one($sql);
        if (!$ret) {
            return 0;
        }
        return $ret['c_id'];
    }

    function cloneInstance($inst_id, $session = '', $prefix = null)
    {
        $date = date('Y-m-d');

        //////////////////////////
        ///INSTANCE//////////////
        $desti =  "class_id, key_fields, status, publishing_begins, publishing_ends, creation_date, update_date";
        $origen = "class_id, key_fields, 'P', '{$date}', publishing_ends, now(), now()";
        $sql = "insert into omp_instances({$desti}) (select {$origen} from omp_instances where id='$inst_id')";


        $dolly_id = parent::insert_one($sql);

        if (!$dolly_id) {
            echo 'Ha fallat al crear la nova Dolly.';
            return 0;
        }

        //////////////////////////
        ///RELATIONS /////////////
        if (empty($session)) { //si fem clone recursion no hem d'entrar aquí perque ho fa el procés
            $desti =  "rel_id, parent_inst_id, child_inst_id, weight, relation_date";
            $origin = "rel_id, {$dolly_id}, child_inst_id, weight, now()";
            //this will create the relations to the children instances of the instance we're cloning
            //previous behavior: it also cloned parent relations
            $sqlChildren = "insert into omp_relation_instances({$desti}) (select {$origin} from omp_relation_instances where parent_inst_id='{$inst_id}')";

            $childrenResult = parent::insert_one($sqlChildren);
            if ($childrenResult) {
                $sql = "SELECT rel_id, parent_inst_id FROM omp_relation_instances WHERE relation_date = now() and child_inst_id = {$dolly_id} GROUP BY rel_id, parent_inst_id;";

                $rel_info = parent::get_data($sql);
                //update del wheight
                if (!empty($rel_info)) {
                    foreach ($rel_info as $row) {
                        $sql = "SELECT  ri.rel_id rel_id, min(weight)-10 AS weight , ri.parent_inst_id
                        FROM omp_relation_instances ri

                        WHERE ri.parent_inst_id = {$row['parent_inst_id']}
                            AND ri.rel_id = {$row['rel_id']}
                        GROUP BY ri.rel_id, ri.parent_inst_id; ";

                        $ret = parent::get_one($sql);

                        $sql = "UPDATE omp_relation_instances set weight = {$ret['weight']}, relation_date = now()
                            where relation_date = now()
                            and child_inst_id = {$dolly_id} and rel_id={$ret['rel_id']};";

                        parent::update_one($sql);
                    }
                }
            }
        }

        //////////////////////////
        ///URL////////////////////
        // $desti= "inst_id, language, niceurl";
        //$origen=  "'".$dolly_id."', language, '".$dolly_id."'";
        //$sql = "insert into omp_niceurl (".$desti.") (select ".$origen." from omp_niceurl where inst_id='$inst_id')";
        $url_origen = parent::get_data("select  language, niceurl from omp_niceurl where inst_id='$inst_id'");
        if ($url_origen) {
            $sql = "insert into omp_niceurl (inst_id, language, niceurl) values (" . $dolly_id . ", '" . $url_origen[0]['language'] . "', '" . $url_origen[0]['niceurl'] . "-" . $dolly_id . "')";
            parent::insert_one($sql);
        }

        //////////////////////////
        ///VALUES/////////////////
        ///TROBAR NOM INTERN//
        $sqlNI = "SELECT text_val FROM omp_values ov, omp_attributes oa WHERE inst_id='{$inst_id}' AND atri_id=oa.id AND oa.tag = 'nom_intern';";
        $resultNI = parent::get_one($sqlNI);
        if (empty($resultNI)) {
            return 'Nom intern empty.';
        } else {
            if ($prefix) {
                $prefix = $prefix . '-';
            }
            $nomclonat = $this->cloneName($prefix.str_replace("'", "\'", $resultNI['text_val']));
        }

        //////////////////////////
        $desti = "inst_id, atri_id, text_val, date_val, num_val, img_info";
        $origen = "'" . $dolly_id . "', atri_id, text_val, date_val, num_val, img_info";
        $origen_1 = "'" . $dolly_id . "', atri_id, '" . $nomclonat . "', date_val, num_val, img_info";
        $sql = "insert into omp_values (" . $desti . ") (select " . $origen . " from omp_values where inst_id='$inst_id' and atri_id<>'1')";

        parent::insert_one($sql);

        $sql = "insert into omp_values (" . $desti . ") (select " . $origen_1 . " from omp_values where inst_id='$inst_id' and atri_id='1')";
        parent::insert_one($sql);

        //////////////////////////
        ///UPDATE NOM-INTERN///////
        $sql = "update omp_instances set key_fields='$nomclonat' where id='$dolly_id'";
        parent::update_one($sql);
        ////////////////////////////

        return $dolly_id;
    }

    public function recursive_clone($inst_id, $session, $prefix)
    {
        //clonar la instancia
        $dolly_id = $this->cloneInstance($inst_id, $session, $prefix);

        if ($dolly_id) { //s'ha clonat correctament
            //regenerem cache
            //update_cache($dolly_id);
            //busquem els fills
            $relations = $this->get_relations_as_parent($inst_id);

            if (!empty($relations)) {
                foreach ($relations as $relation) {
                    //clonem els fills i els seus fills recursivament
                    $dolly_child_id = $this->recursive_clone($relation['child_inst_id'], $session, $prefix);

                    //creem la relació de pare-fill
                    if ($dolly_child_id) {
                        $this->create_relation_dolly($dolly_id, $dolly_child_id, $session, $relation);
                    }
                }
            }
            return $dolly_id;
        } else {
            //hi ha hagut algun error i no podem seguir, o es un instancia amb la mateixa sessio o no hi ha resultats pero no tenim $dolly_id
            return false;
        }
    }

    private function get_relations_as_parent($instid)
    {
        $sql = "select * from omp_relation_instances where parent_inst_id='$instid'";
        return parent::get_data($sql);
    }

    private function create_relation_dolly($parent_inst_id, $child_inst_id, $session, $relation)
    {
        $sql = "select id from omp_relation_instances ori where ori.clone_session = $session AND ori.parent_inst_id = $parent_inst_id AND ori.child_inst_id = $child_inst_id AND ori.rel_id = $relation[rel_id] AND ori.cloned_instance = $relation[id];";
        $row = $this->get_one($sql);
        if (!empty($row)) {
            return $row['id'];
        }

        $desti =   "rel_id, parent_inst_id, child_inst_id, weight, relation_date, clone_session, cloned_instance";
        $origen = "'{$relation['rel_id']}', '$parent_inst_id', '$child_inst_id', '{$relation['weight']}', now(), '$session', '{$relation['id']}'";

        $sql = "insert into omp_relation_instances(" . $desti . ") values (" . $origen . ")";
        $result = $this->insert_one($sql);
        if (!$result) {
            die("Error query Parent Relation: " . $sql);
        } else {
            return $result;
        }
    }

    private function cloneName($dolly_name, $clone_index = 0)
    {
        if ($clone_index == 100) {
            die('Hi ha mases subconsultes.');
        }
        if ($clone_index != 0) {
            $dolly_sample = $dolly_name.'-'.$clone_index;
        } else {
            $dolly_sample = $dolly_name;
        }
        $sql = "select id from omp_values where text_val='$dolly_sample'";

        $row = parent::get_data($sql);

        if (!isset($row[0]['id'])) {
            return $dolly_sample;
        } else {
            $dolly_name = $this->cloneName($dolly_name, (int)($clone_index + 1));
        }

        return $dolly_name;
    }

    //Cache JF
    private function hasCacheOption($inst_id)
    {
        $sql = "select count(1) as nicecount from omp_niceurl where inst_id=" . $inst_id . " and niceurl <> ''";
        $ret = parent::get_one($sql);
        if (!$ret) {
            return false;
        }
        if ($ret['nicecount'] > 0) {
            return true;
        }
        return false;
    }

    private function hasCacheFunctions($inst_id)
    {
        $sql = "select count(1) as nicecount from omp_niceurl where inst_id=" . $inst_id . " and niceurl <> ''";
        $ret = parent::get_one($sql);
        if (!$ret) {
            return false;
        }
        if ($ret['nicecount'] > 0) {
            return true;
        }
        return false;
    }

    function changeInstanceCacheStatus($param_arr)
    {
        $inst_id = $param_arr['param2'];
        $cache_status = $param_arr['p_cache_status'];
        $sql = "update omp_niceurl set use_cache='$cache_status' where inst_id=$inst_id";
        return parent::update_one($sql);
    }

    function deteleInstanceCache($param_arr)
    {
        $inst_id = $param_arr['param2'];
        $sql = "update omp_niceurl set cache_pending='D' where inst_id=$inst_id";
        return parent::update_one($sql);
    }

    function refreshInstanceCache($param_arr)
    {
        $inst_id = $param_arr['param2'];
        $sql = "update omp_niceurl set cache_pending='Y' where inst_id=$inst_id";
        return parent::update_one($sql);
    }

    function refreshCache($param_arr)
    {
        $inst_id = $param_arr['param2'];
        if ($this->hasCacheFunctions($inst_id)) {
            $this->setPendingCache($inst_id);
        }

        $this->refreshParents($param_arr);
        $this->refreshChilds($param_arr);
        $this->refreshBrothers($param_arr);
    }

    private function isPendingCache($inst_id)
    {
        $sql = "select count(1) as nicecount from omp_niceurl where cache_pending = 'Y' and inst_id=" . $inst_id . " and niceurl <> '' and use_cache='Y'";
        $ret = parent::get_one($sql);
        if (!$ret) {
            return false;
        }
        if ($ret['nicecount'] > 0) {
            return false;
        }

        return true;
    }

    private function setPendingCache($inst_id)
    {
        $sql = "update omp_niceurl set cache_pending='Y' where inst_id=$inst_id";
        return parent::update_one($sql);
    }

    private function refreshParents($param_arr, $max_deep = 1, $actualitzats = array())
    {
        if ($max_deep == 0) {
            return;
        }

        $pares = $this->getParents($param_arr, $actualitzats);
        if (count($pares) > 0) {
            foreach ($pares as $pare) {
                if ($this->isPendingCache($pare['id'])) {
                    array_push($actualitzats, $pare['id']);
                    if ($this->hasCacheFunctions($pare['id'])) {
                        $this->setPendingCache($pare['id']);
                    }

                    $param_arr['param2'] = $pare['id'];
                    $this->refreshParents($param_arr, $max_deep--, $actualitzats);
                }
            }
        }

        return;
    }

    private function refreshChilds($param_arr, $max_deep = 1, $actualitzats = array())
    {
        if ($max_deep == 0) {
            return;
        }

        $fills = $this->getChilds($param_arr, $actualitzats);
        if (count($fills) > 0) {
            foreach ($fills as $fill) {
                if ($this->isPendingCache($fill['id'])) {
                    array_push($actualitzats, $fill['id']);
                    if ($this->hasCacheFunctions($fill['id'])) {
                        $this->setPendingCache($fill['id']);
                    }

                    $param_arr['param2'] = $fill['id'];
                    $this->refreshChilds($param_arr, $max_deep--, $actualitzats);
                }
            }
        }

        return;
    }

    private function refreshBrothers($param_arr)
    {
        $germans = $this->getBrothers($param_arr);
        if (count($germans) > 0) {
            foreach ($germans as $germa) {
                if ($this->hasCacheFunctions($germa['id'])) {
                    $this->setPendingCache($germa['id']);
                }
            }
        }

        return;
    }
    //Fi Cache JF

    function unlinkedImages()
    {
        $conta = 0;
        $volta = 0;
        $return_files = array();
        $directories = @scandir(DIR_UPLOADS, 1);
        if (!$directories) {
            return $return_files;
        }
        foreach ($directories as $dir) {
            if (is_dir(DIR_UPLOADS . $dir) && $dir != '.' && $dir != '..') {
                $files = @scandir(DIR_UPLOADS . $dir, 1);
                foreach ($files as $file) {
                    if ($file != "." && $file != "..") {
                        $sql = "select count(id) as conta from omp_values where text_val like '%uploads/" . $dir.'/'.$file . "%';";
                        $ret = parent::get_data($sql);
                        if ($ret[0]['conta'] == 0) {
                            $return_files[$conta]['url'] = '/uploads/'.$dir.'/'.$file;
                            $return_files[$conta]['full_url'] = DIR_UPLOADS . $dir.'/'.$file;
                            $return_files[$conta]['name'] = $file;
                            $return_files[$conta]['date'] = date("d-m-Y H:i:s", filemtime(DIR_UPLOADS . $dir.'/'.$file));
                            $conta++;
                        }
                    }
                }
            }
            $volta++;
        }

        return $return_files;
    }

    function getUniqueNiceURL($inst_id, $language, $nice_url)
    {
        $i = 0;
        $sql = 'select count(1) as nicecount from omp_niceurl where inst_id <> '.$inst_id.' and language = "'.$language.'" and niceurl = "'.$nice_url.'"';
        $ret = parent::get_one($sql);
        if ($ret['nicecount'] == 0) {
            return $nice_url;
        }

        do {
            $i++;
            $sql = 'select count(1) as nicecount from omp_niceurl where inst_id <> '.$inst_id.' and language = "'.$language.'" and niceurl = "'.$nice_url . $i.'"';
            $ret = parent::get_one($sql);
        } while ($ret['nicecount'] <> 0);

        return $nice_url . $i;
    }

    function get_multiples_id($p_relation_id)
    {
        $sql = "select multiple_child_class_id as mcci from omp_relations r where id = " . $p_relation_id;
        $Row = parent::get_one($sql);
        return $Row['mcci'];
    }

    function save_cloneimage_attribute($inst_id, $atri_id, $value, $width, $height)
    {
        $sql = "select count(*) num from omp_values where inst_id=$inst_id and atri_id=$atri_id;";
        $row = parent::get_one($sql);
        if ($row['num'] == 0) { // hem d'insertar, no hi era
            $sql = "insert into omp_values (inst_id, atri_id, text_val, img_info) values($inst_id, $atri_id, '" . $value . "','" . $width.'.'.$height . "');";
            parent::insert_one($sql);
        } else { // hem de fer update
            $sql = "update omp_values set text_val='" . $value . "', img_info='" . $width.'.'.$height . "' where inst_id=$inst_id and atri_id=$atri_id;";
            parent::execute($sql);
        }
    }

    function instance_update_date_and_backup($inst_id)
    {
        $sql = 'update omp_instances set update_date=now() where id='.$inst_id;
        parent::update_one($sql);

        //$this->backup($inst_id);
    }


    function backup($inst_id)
    {
        global $dbh;
        require_once(DIR_APLI_ADMIN.'/objects/MainObject.php');
        $idiomes = $this->busca_idiomes();
        $mainobject = new MainObject();

        if ($idiomes) {
            foreach ($idiomes as $idioma) {
                $backup_xml = $mainobject->getInstanceInfo($inst_id, $idioma, 'cache', '', 'D', Session::get('rol_id'));

                $sql_backup = 'insert omp_instances_backup (inst_id,language,xml_cache,date,user) values ('.$inst_id.', "'.$idioma.'", "'.mysql_real_escape_string($backup_xml, $dbh).'", now(),'.Session::get('user_id').');';
                $ret_backup = parent::insert_one($sql_backup);
                if (!$ret_backup) {
                    echo mysql_error($dbh) . chr(13) . chr(10) . chr(13) . chr(10);
                }
            }
        }

        return 1;
    }

    function busca_idiomes()
    {
        $sql = 'select language as lang from omp_attributes where language!="ALL" group by language';
        $ret = parent::get_data($sql);
        if ($ret) {
            $idiomes = array();
            foreach ($ret as $row) {
                $idiomes[] = $row['lang'];
            }

            return $idiomes;
        }
    }


    function check_urlnice($p_inst_id)
    {
        reset($_REQUEST);
        while (key($_REQUEST)) {
            $valor = current($_REQUEST);
            $nom = key($_REQUEST);

            if (isset($valor) && $valor <> '') {
                $part = strtok($nom, '_');
                if ($part == 'atr') { // Estic escanejant un atribut, comprovo els flags
                    $part = strtok('_');
                    $flags = $part;
                    $type = substr($flags, 1, 1);
                    // Ara busquem el id del atribut
                    $part = strtok('_');
                    $atr_id = $part;
                    if ($type == 'Z') { // url nice, check the uniqueness
                        //Primer busquem el idioma del atribut que estem mirant per buscar-ho a omp_niceurl
                        $sql = 'select language from omp_attributes where id="'.$atr_id.'"';
                        $row = parent::get_one($sql);

                        $sql_add = '';
                        if ($p_inst_id != null) {
                            $sql_add = 'and inst_id!='.$p_inst_id.'';
                        }
                        $sql = 'select count(*) num from omp_niceurl where language="'.$row['language'].'" and niceurl="'.clean_url($valor).'" '.$sql_add;

                        $row = parent::get_one($sql);

                        if ($row['num'] > 0) {
                            return false;
                        }
                    }
                }
            }
            next($_REQUEST);
        }

        return true;
    }


    function get_niceurl($id, $lang = 'ca')
    {
        $sql = "select niceurl from omp_niceurl n, omp_instances i where i.id=inst_id and inst_id=" . $id . " and (language='" . $lang . "' or language='ALL')";

        $Row = parent::get_one($sql);
        return $Row['niceurl'];
    }

    function brokenLinks()
    {
        $sql = "SELECT i.id as inst_id, i.class_id class_id,  v.text_val as url
        FROM omp_values AS v, omp_instances AS i
        WHERE v.atri_id IN (SELECT id FROM omp_attributes WHERE type = 'U')
        AND v.inst_id = i.id
        AND i.status='O';";
        $ret = parent::get_data($sql);
        $url_errors = [];
        foreach ($ret as $item) {
            if (substr($item["url"], 0, 1) == "/") {
                $item["url"] = config('app.url') . $item["url"];
            }
            if ($this->isUrlBroken($item["url"])) {
                array_push($url_errors, $item);
            }
        }
        return $url_errors;
    }

    function isUrlBroken($url = NULL)
    {
        if (empty($url)) {
            return true;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $accepted_response = array(200, 301, 302);
        if (in_array($httpcode, $accepted_response)) {
            return false;
        } else {
            return true;
        }
    }
}
