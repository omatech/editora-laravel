<?php

namespace Omatech\Editora\Admin\Models;

class relations extends model 
{


	///////////////////////////////////////////////////////////////////////////////////////////
	function createRelation($param_arr) {
		$p_relation_id=$param_arr['param9'];
		$p_parent_inst_id=$param_arr['param11'];
		$p_child_inst_id=$param_arr['param13'];
		
		$sql='select r.order_type, min(weight) weight
		from omp_relations r
		, omp_relation_instances ri
		where ri.parent_inst_id='.str_replace("\"", "\\\"", str_replace("[\]","",$p_parent_inst_id)).'
		and ri.rel_id='.str_replace("\"", "\\\"", str_replace("[\]","",$p_relation_id)).'
		and ri.rel_id = r.id
		group by r.order_type';

		$Row=parent::get_one($sql);

		if ($Row && $Row['weight']) $weight = ($Row['weight'])-10;
		else $weight = 100000;

		$sql='insert into omp_relation_instances
		(rel_id, parent_inst_id, child_inst_id, weight, relation_date)
		values
		('.str_replace("\"", "\\\"", str_replace("[\]","",$p_relation_id)).','.str_replace("\"", "\\\"", str_replace("[\]","",$p_parent_inst_id)).', '.str_replace("\"", "\\\"", str_replace("[\]","",$p_child_inst_id)).', '.str_replace("\"", "\\\"", str_replace("[\]","",$weight)).', now())';
		
		$new_relation_instance_id=parent::insert_one($sql);
		if ($new_relation_instance_id) return $new_relation_instance_id;

		return 0;
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function deleteRelationInstance($param_arr) {
		$p_relinst_id=$param_arr['param9'];

		$sql = "select ri.rel_id, ri.parent_inst_id, ri.weight from omp_relation_instances ri where ri.id =".$p_relinst_id.";";
		$ret=parent::get_one($sql);

		$sql = "delete from omp_relation_instances where id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_relinst_id)).";";
		parent::execute($sql);

		$sql = "update omp_relation_instances set weight = (weight + 10) where rel_id = ".$ret['rel_id']." and parent_inst_id = ".$ret['parent_inst_id']." and weight < ".$ret['weight'].";";
		parent::update_one($sql);

		return true;
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function relationShake($p_relinst_id, $delta) {
		$sql='select weight, rel_id, parent_inst_id from omp_relation_instances where id='.str_replace("\"", "\\\"", str_replace("[\]","",$p_relinst_id));
		$Row=parent::get_one($sql);

		$pes_inicial = $Row['weight'];
		$parent_inst_id = $Row['parent_inst_id'];
		$rel_id = $Row['rel_id'];

		$pes_desti = $pes_inicial+$delta;

		$sql='update omp_relation_instances set weight=-1 where id='.str_replace("\"", "\\\"", str_replace("[\]","",$p_relinst_id));
		parent::update_one($sql);

		$sql='update omp_relation_instances set weight='.$pes_inicial.' 
		where rel_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$rel_id)).'
		and parent_inst_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$parent_inst_id)).'
		and weight='.str_replace("\"", "\\\"", str_replace("[\]","",$pes_desti));
		parent::update_one($sql);

		$sql='update omp_relation_instances set weight='.$pes_desti.' 
		where rel_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$rel_id)).'
		and parent_inst_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$parent_inst_id)).'
		and weight=-1';
		parent::update_one($sql);
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function relationShakeTop($p_relinst_id, $delta) {
		$sql='select weight, rel_id, parent_inst_id from omp_relation_instances where id='.str_replace("\"", "\\\"", str_replace("[\]","",$p_relinst_id));
		$Row=parent::get_one($sql);

		$pes_inicial = $Row['weight'];
		$parent_inst_id = $Row['parent_inst_id'];
		$rel_id = $Row['rel_id'];

		$pes_desti = $pes_inicial+$delta;

		$sql='select max(weight) as max, min(weight) as min from omp_relation_instances where rel_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$rel_id)).'
		and parent_inst_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$parent_inst_id));
		$Row=parent::get_one($sql);
		$max=$Row['max'];
		$min=$Row['min'];

		$sql='update omp_relation_instances set weight=-1 where id='.str_replace("\"", "\\\"", str_replace("[\]","",$p_relinst_id));
		parent::update_one($sql);

		if($delta>0) {
			$new_delta=$min;
			$sql='update omp_relation_instances set weight=(weight+'.$delta.') 
			where rel_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$rel_id)).'
			and parent_inst_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$parent_inst_id)).'
			and weight<'.str_replace("\"", "\\\"", str_replace("[\]","",$pes_inicial)).' 
			and weight!=-1';
		}
		else {
			$new_delta=$max;
			$sql='update omp_relation_instances set weight=(weight-'.abs($delta).') 
			where rel_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$rel_id)).'
			and parent_inst_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$parent_inst_id)).'
			and weight>'.str_replace("\"", "\\\"", str_replace("[\]","",$pes_inicial)).' 
			and weight!=-1';
		}
		parent::update_one($sql);

		$sql='update omp_relation_instances set weight='.$new_delta.' 
		where rel_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$rel_id)).'
		and parent_inst_id = '.str_replace("\"", "\\\"", str_replace("[\]","",$parent_inst_id)).'
		and weight=-1';
		parent::update_one($sql);
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function relationInstanceUp($param_arr) {
		$this->relationShake($param_arr['param9'], -10);
		return getMessage('info_word_orderjoin');
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function relationInstanceDown($param_arr) {
		$this->relationShake($param_arr['param9'], 10);
		return getMessage('info_word_orderjoin');
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function relationInstanceUpTop($param_arr) {
		$this->relationShakeTop($param_arr['param9'], 10);
		return getMessage('info_word_orderjoin');
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function relationInstanceDownBottom($param_arr) {
		$this->relationShakeTop($param_arr['param9'], -10);
		return getMessage('info_word_orderjoin');
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	public function order_relation($parent, $childs) {
		$weight = 100000;
		$childs = array_reverse($childs);
		
		foreach($childs as $child) {
			$sql='UPDATE omp_relation_instances SET weight='.$weight.' where parent_inst_id = '.$parent.' AND child_inst_id = '.$child.';';
			parent::update_one($sql);
			$weight-=10;
		}
		return true;
	}
}
