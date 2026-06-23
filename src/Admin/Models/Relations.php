<?php

namespace Omatech\Editora\Admin\Models;

class Relations extends Model 
{


	///////////////////////////////////////////////////////////////////////////////////////////
	function createRelation($param_arr) {
		$p_relation_id=$param_arr['param9'];
		$p_parent_inst_id=$param_arr['param11'];
		$p_child_inst_id=$param_arr['param13'];
		
		// SECURITY (SQLi): parent_inst_id y rel_id son ids numéricos -> cast inline a (int)
		$sql='select r.order_type, min(weight) weight
		from omp_relations r
		, omp_relation_instances ri
		where ri.parent_inst_id='.((int) $p_parent_inst_id).'
		and ri.rel_id='.((int) $p_relation_id).'
		and ri.rel_id = r.id
		group by r.order_type';

		$Row=parent::get_one($sql);

		if ($Row && $Row['weight']) $weight = ($Row['weight'])-10;
		else $weight = 100000;

		// SECURITY (SQLi): rel_id, parent_inst_id, child_inst_id y weight son numéricos -> cast inline a (int)
		$sql='insert into omp_relation_instances
		(rel_id, parent_inst_id, child_inst_id, weight, relation_date)
		values
		('.((int) $p_relation_id).','.((int) $p_parent_inst_id).', '.((int) $p_child_inst_id).', '.((int) $weight).', now())';
		
		$new_relation_instance_id=parent::insert_one($sql);
		if ($new_relation_instance_id) return $new_relation_instance_id;

		return 0;
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function deleteRelationInstance($param_arr) {
		$p_relinst_id=$param_arr['param9'];

		// SECURITY (SQLi): relinst_id es un id numérico -> cast inline a (int)
		$sql = "select ri.rel_id, ri.parent_inst_id, ri.weight from omp_relation_instances ri where ri.id =".((int) $p_relinst_id).";";
		$ret=parent::get_one($sql);

		// SECURITY (SQLi): relinst_id es un id numérico -> cast inline a (int)
		$sql = "delete from omp_relation_instances where id = ".((int) $p_relinst_id).";";
		parent::execute($sql);

		// SECURITY (SQLi): rel_id, parent_inst_id y weight son numéricos -> cast inline a (int)
		$sql = "update omp_relation_instances set weight = (weight + 10) where rel_id = ".((int) $ret['rel_id'])." and parent_inst_id = ".((int) $ret['parent_inst_id'])." and weight < ".((int) $ret['weight']).";";
		parent::update_one($sql);

		return true;
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function relationShake($p_relinst_id, $delta) {
		// SECURITY (SQLi): relinst_id es un id numérico -> cast inline a (int)
		$sql='select weight, rel_id, parent_inst_id from omp_relation_instances where id='.((int) $p_relinst_id);
		$Row=parent::get_one($sql);

		$pes_inicial = $Row['weight'];
		$parent_inst_id = $Row['parent_inst_id'];
		$rel_id = $Row['rel_id'];

		$pes_desti = $pes_inicial+$delta;

		// SECURITY (SQLi): relinst_id es un id numérico -> cast inline a (int)
		$sql='update omp_relation_instances set weight=-1 where id='.((int) $p_relinst_id);
		parent::update_one($sql);

		// SECURITY (SQLi): rel_id, parent_inst_id y weight son numéricos -> cast inline a (int)
		$sql='update omp_relation_instances set weight='.((int) $pes_inicial).'
		where rel_id = '.((int) $rel_id).'
		and parent_inst_id = '.((int) $parent_inst_id).'
		and weight='.((int) $pes_desti);
		parent::update_one($sql);

		// SECURITY (SQLi): rel_id, parent_inst_id y weight son numéricos -> cast inline a (int)
		$sql='update omp_relation_instances set weight='.((int) $pes_desti).'
		where rel_id = '.((int) $rel_id).'
		and parent_inst_id = '.((int) $parent_inst_id).'
		and weight=-1';
		parent::update_one($sql);
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	function relationShakeTop($p_relinst_id, $delta) {
		// SECURITY (SQLi): relinst_id es un id numérico -> cast inline a (int)
		$sql='select weight, rel_id, parent_inst_id from omp_relation_instances where id='.((int) $p_relinst_id);
		$Row=parent::get_one($sql);

		$pes_inicial = $Row['weight'];
		$parent_inst_id = $Row['parent_inst_id'];
		$rel_id = $Row['rel_id'];

		$pes_desti = $pes_inicial+$delta;

		// SECURITY (SQLi): rel_id y parent_inst_id son numéricos -> cast inline a (int)
		$sql='select max(weight) as max, min(weight) as min from omp_relation_instances where rel_id = '.((int) $rel_id).'
		and parent_inst_id = '.((int) $parent_inst_id);
		$Row=parent::get_one($sql);
		$max=$Row['max'];
		$min=$Row['min'];

		// SECURITY (SQLi): relinst_id es un id numérico -> cast inline a (int)
		$sql='update omp_relation_instances set weight=-1 where id='.((int) $p_relinst_id);
		parent::update_one($sql);

		if($delta>0) {
			$new_delta=$min;
			// SECURITY (SQLi): delta, rel_id, parent_inst_id y weight son numéricos -> cast inline a (int)
			$sql='update omp_relation_instances set weight=(weight+'.((int) $delta).')
			where rel_id = '.((int) $rel_id).'
			and parent_inst_id = '.((int) $parent_inst_id).'
			and weight<'.((int) $pes_inicial).'
			and weight!=-1';
		}
		else {
			$new_delta=$max;
			// SECURITY (SQLi): delta, rel_id, parent_inst_id y weight son numéricos -> cast inline a (int)
			$sql='update omp_relation_instances set weight=(weight-'.((int) abs($delta)).')
			where rel_id = '.((int) $rel_id).'
			and parent_inst_id = '.((int) $parent_inst_id).'
			and weight>'.((int) $pes_inicial).'
			and weight!=-1';
		}
		parent::update_one($sql);

		// SECURITY (SQLi): new_delta, rel_id y parent_inst_id son numéricos -> cast inline a (int)
		$sql='update omp_relation_instances set weight='.((int) $new_delta).'
		where rel_id = '.((int) $rel_id).'
		and parent_inst_id = '.((int) $parent_inst_id).'
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
			// SECURITY (SQLi): weight, parent_inst_id y child_inst_id son numéricos -> cast inline a (int)
			$sql='UPDATE omp_relation_instances SET weight='.((int) $weight).' where parent_inst_id = '.((int) $parent).' AND child_inst_id = '.((int) $child).';';
			parent::update_one($sql);
			$weight-=10;
		}
		return true;
	}
}
