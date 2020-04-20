<?php

namespace Omatech\Editora\Admin\Models;
use Illuminate\Support\Facades\Session;

class attributes extends Model
{
	public function getImageAttributes ($p_class_id) {
		$lg = Session::get('u_lang') ;

		$sql = "SELECT a.id id
		, a.name name
		, a.caption_".$lg." caption
		, a.description description
		, a.tag tag
		, a.type type
		, a.lookup_id lookup_id
		, a.max_length max_length
		, a.language
		, a.width a_width
		, a.height a_height
		, a.img_width ai_width
		, a.img_height ai_height
		, ca.class_id class_id
		, ca.rel_id rel_id
		, ca.fila fila
		, ca.columna columna
		, ca.ordre_key ordre_key
		, ca.mandatory mandatory
		, ca.caption_position caption_position
		, ca.width ca_width
		, ca.height ca_height
		, ca.img_width cai_width
		, ca.img_height cai_height
		, ca.tab_id
		, t.name_".$lg." tab_name
		, c.name class_name
		, c.id class_id
		, c.description class_description
		, ca.detail cadetail
		, a.params
		FROM omp_attributes a
		, omp_class_attributes ca
		, omp_classes c
		, omp_tabs t
		WHERE c.id=".$p_class_id."
		and c.id = ca.class_id
		and ca.atri_id = a.id
		and a.type='I'
		and ca.tab_id=t.id
		order by ca.tab_id, ca.fila, ca.columna";

		$ret=parent::get_data($sql);
		if(!$ret) return array();
		return $ret;
	}

	private function getClassTabs($p_class_id) {
		$lg = Session::get('u_lang');

		$sql = "SELECT ca.tab_id id, t.name name, t.name_".$lg." caption
		FROM omp_class_attributes ca left outer join omp_tabs t on ca.tab_id = t.id 
		WHERE ca.class_id=".$p_class_id." group by ca.tab_id, t.name, t.name_".$lg." order by t.ordering";

		$ret=$this->get_data($sql);
		if(!$ret) return array();

		return $ret;
	}

	private function getClassAttributes ($p_class_id, $tab_id) {
		$lg = Session::get('u_lang');

		$sql = "SELECT a.id id
		, a.name name
		, a.caption_".$lg." caption
		, a.description description
		, a.tag tag
		, a.type type
		, a.lookup_id lookup_id
		, a.max_length max_length
		, a.language
		, a.width a_width
		, a.height a_height
		, a.img_width ai_width
		, a.img_height ai_height
		, ca.class_id class_id
		, ca.rel_id rel_id
		, ca.fila fila
		, ca.columna columna
		, ca.ordre_key ordre_key
		, ca.mandatory mandatory
		, ca.caption_position caption_position
		, ca.width ca_width
		, ca.height ca_height
		, ca.img_width cai_width
		, ca.img_height cai_height
		, c.name class_name
		, c.id class_id
		, c.description class_description
		, 'Y' join_icon
		, 'N' create_icon
		, 'N' join_massive
		, 'N' autocomplete
		, ca.detail cadetail
		, a.params
		FROM omp_attributes a
		, omp_class_attributes ca
		, omp_classes c
		WHERE c.id=".$p_class_id."
		and ca.tab_id=".$tab_id."
		and c.id = ca.class_id
		and ca.atri_id = a.id
		union all
		SELECT r.id id
		, r.name name
		, r.caption_".$lg." caption
		, r.description description
		, r.tag tag
		, 'R' type
		, r.parent_class_id lookup_id
		, r.child_class_id max_length
		, r.language language
		, null a_width
		, null a_height
		, null ai_width
		, null ai_height
		, ca.class_id class_id
		, ca.rel_id rel_id
		, ca.fila fila
		, ca.columna columna
		, ca.ordre_key ordre_key
		, 'N' mandatory
		, ca.caption_position caption_position
		, null ca_width
		, null ca_height
		, null cai_width
		, null cai_height
		, c.name class_name
		, c.id class_id
		, c.description class_description
		, r.join_icon join_icon
		, r.create_icon create_icon
		, r.join_massive join_massive
		, r.autocomplete
		, ca.detail cadetail
		, null params
		FROM omp_relations r
		, omp_class_attributes ca
		, omp_classes c
		WHERE c.id=".$p_class_id."
		and ca.tab_id=".$tab_id."
		and c.id = ca.class_id
		and ca.rel_id = r.id
		order by fila, columna";

		$ret=parent::get_data($sql);
		if(!$ret) return array();

		return $ret;
	}

	function getAttributeValues($p_atri_id, $p_inst_id) {
		global $dbh;

		$sql = "select v.atri_id
		, v.text_val text_val
		, date_format(v.date_val, '".STANDARD_DATE_FORMAT."') date_val
		, v.num_val num_val
		, img_info
		from omp_values v
		where v.inst_id = ".$p_inst_id."
		and v.atri_id = ".$p_atri_id;

		$ret=parent::get_data($sql);
		if(!$ret) return array();

		return $ret;
	}

	function getInstanceAttributes($p_mode, $param_arr) {
		$ret = array();
		$status_arr = array();
		$instance_arr = array();
		$tabs_arr = array();

		$p_class_id=$param_arr['param1'];
		$p_inst_id=$param_arr['param2'];

		require_once(DIR_APLI_ADMIN . '/models/Security.php');
		require_once(DIR_APLI_ADMIN.'/models/instances.php');
		
		$sc=new security();
		$in=new instances();
		$instance_arr=$in->getInstInfo($p_inst_id);
		for ($st=1; $st<=3; $st++) $status_arr['status'.$st]=$sc->getStatus($st, $p_class_id, Session::get('rol_id'));

		$tabs=$this->getClassTabs($p_class_id);
		foreach ($tabs as $tab) {
			$attrs = $this->getClassAttributes($p_class_id, $tab['id']);
			$t=1;
			$arr_tmp = array();
			foreach ($attrs as $attr) {
				$this->attributeParse($attr, $p_mode, $p_inst_id, $tab['id'], $t);
				$t++;
				array_push($arr_tmp, $attr);
			}
			$tab['elsatribs']=$arr_tmp;
			array_push($tabs_arr, $tab);
		}
		$instance_arr['instance_tabs']=$tabs_arr;
		$ret['status_list']=$status_arr;
		$ret['instance_info']=$instance_arr;

		return $ret;
	}
	
	
	private function attributeParse(&$row, $p_mode, $p_inst_id, $tab_id='',$i) {
		$row['params']= json_decode($row['params']);
		$w1=FIELD_DEFAULT_LENGTH;
		if (isset($row['a_width']) and $row['a_width']<>'') $w1=$row['a_width'];
		if (isset($row['ca_width']) and $row['ca_width']<>'') $w1=$row['ca_width'];
		$row['max_width']=$w1;

		$h1=TEXTAREA_DEFAULT_HEIGHT;
		if (isset($row['a_height']) and $row['a_height']<>'') $h1=$row['a_height'];
		if (isset($row['ca_height']) and $row['ca_height']<>'') $h1=$row['ca_height'];
		$row['max_height']=$h1;
		

		$img_w='';
		if (isset($row['ai_width']) and $row['ai_width']<>'') $img_w=$row['ai_width'];
		if (isset($row['cai_width']) and $row['cai_width']<>'') $img_w=$row['cai_width'];
		$row['img_w']=$img_w;
			
		$img_h='';
		if (isset($row['ai_height']) and $row['ai_height']<>'') $img_h=$row['ai_height'];
		if (isset($row['cai_height']) and $row['cai_height']<>'') $img_h=$row['cai_height'];
		$row['img_h']=$img_h;
		global $googlemaps;

		// LOOKUP
		if ($row['type']=="L") $row['lookup_info']=$this->getLookupInfo($row, $p_mode, $p_inst_id);

		if ($p_mode!='I') {
			$row['atrib_values']=$this->getAttributeValues($row['id'], $p_inst_id);

			if ($row['type']=="M") {
				$googlemaps=true;
				null;
			}
			// RELATION
			elseif ($row['type']=="R") $row['related_instances']=$this->getRelatedInstances($row['id'], $p_inst_id, 1000, $tab_id);
			// LOOKUP
			//elseif ($row['type']=="L") $row['lookup_info']=$this->getLookupInfo($row, $p_mode, $p_inst_id);
			// URLNICE
			elseif ($row['type']=="Z") $row['niceurl']=$this->niceurlFromAtri($row['id'], $p_inst_id);
		}
	}
 
 
	private function getLookupInfo($p_row, $p_mode, $p_inst_id) {
		$lu_selec = array();
		$lu_values = array();
		
		$sql_info="select * from omp_lookups l where l.id=".$p_row['lookup_id'];
		$ret=parent::get_one($sql_info);

		$lu_values['info']=$ret;
		
		if ($p_mode!='I') {
			$sql = "select v.atri_id
			, v.text_val text_val
			, date_format(v.date_val, '".STANDARD_DATE_FORMAT."') date_val
			, v.num_val num_val
			, a.description description
			from omp_values v, omp_attributes a
			where v.inst_id = ".$p_inst_id."
			and v.atri_id = ".$p_row['id']."
			and a.id=v.atri_id";

			$ret=parent::get_data($sql);
			
			if(!$ret) {
				$lu_values['selected_values'] = array();
			}
			else {
				foreach ($ret as $r) {
					$lu_selec = $this->getLookupValue($p_row['lookup_id'], $r['num_val']);
					$lu_values['selected_values'] = $lu_selec;
				}
			}
		}

		if ($p_mode!='V') { // Mode insert o update
			$lg = Session::get('u_lang') ;

			$sql="select l.id lookup_id
			,l.default_id default_id
			,l.name name
			,l.type type
			,lv.id lookup_value_id
			,lv.caption_".$lg." label
			from omp_lookups l, omp_lookups_values lv
			where l.id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_row['lookup_id']))."
			and l.id=lv.lookup_id
			order by lv.ordre";
	
			$ret=parent::get_data($sql);
			if(!$ret) $lu_values['lookup_all_values'] = array();
				
			$lu_values['lookup_all_values'] = $ret;
		}
		return $lu_values;
	}
	
	private function getLookupValue($p_lookup_id, $p_lookup_value_id) {
		$lg = Session::get('u_lang');
		$sql_add = "";
		
		if ($p_lookup_value_id) $sql_add = " and lv.id=".$p_lookup_value_id;

		$sql="select lv.id, caption_".$lg." label
		from omp_lookups l, omp_lookups_values lv
		where l.id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_lookup_id))."
		and l.id = lv.lookup_id";
		$sql.=$sql_add;
		$sql.=" order by lv.ordre";

		$ret=parent::get_data($sql);
		if(!$ret) return array();

		return $ret;
	}

	public function getRelatedInstances($p_relation_id, $p_parent_inst_id, $num=1000) {
		$rel_inst = array();
		
		$sql = "select * from omp_relations where id=".$p_relation_id;
		$ret=parent::get_one($sql);
		if(!$ret) $order_type = 'M';
		else $order_type = $ret['order_type'];

		if ($order_type=='M') $order_chunk=" order by ri.weight";
		elseif ($order_type=='T') $order_chunk=" order by ri.relation_date desc";
		else $order_chunk=" order by ri.relation_date";

		$rel_inst['info']=$ret;
		
		$sql="select ri.id id
		, i.key_fields key_fields
		, i.id inst_id
		, i.status
		, i.class_id child_class_id
		, r.parent_class_id parent_class_id
		, c.name_".Session::get('u_lang') ." class_realname
		from omp_instances i
		, omp_relations r
		, omp_relation_instances ri
		, omp_classes c
		where ri.rel_id = ".$p_relation_id."
		and ri.parent_inst_id = ".$p_parent_inst_id."
		and ri.rel_id = r.id
		and ri.child_inst_id = i.id 
		and i.class_id = c.id 
		".$order_chunk." LIMIT ".$num;

		$ret=parent::get_data($sql);

		if(!$ret) $ret = [];
			
		$rel_inst['instances']=$ret;

		return $rel_inst;
	}
	
	private function niceurlFromAtri($atri_id,$inst_id) {
		$sql="select language from omp_attributes where id=".$atri_id;
		$ret=parent::get_one($sql);
		if(!$ret) return '';
		
		$sql="select niceurl from omp_niceurl where inst_id=".$inst_id." and language='".$ret['language']."'";
		$ret=parent::get_one($sql);
		if(!$ret) return '';

		return $ret['niceurl'];
	}

}
