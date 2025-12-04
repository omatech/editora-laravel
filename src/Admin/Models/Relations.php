<?php

namespace Omatech\Editora\Admin\Models;

class Relations extends Model 
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

        $this->updateNiceurlsForChildRelations($p_child_inst_id);

		if ($new_relation_instance_id) return $new_relation_instance_id;

		return 0;
	}



	///////////////////////////////////////////////////////////////////////////////////////////
	function deleteRelationInstance($param_arr) {
		$p_relinst_id=$param_arr['param9'];

		$sql = "select ri.rel_id, ri.parent_inst_id, ri.child_inst_id, ri.weight from omp_relation_instances ri where ri.id =".$p_relinst_id.";";
		$ret=parent::get_one($sql);

        $sql = "select name from omp_relations where id = ".$ret['rel_id'];
        $relation=parent::get_one($sql);

        if ($relation && $relation['name'] == 'Childs') {
            $this->updateNiceurlsForChildRelations($ret['child_inst_id'], true);
        }

		$sql = "delete from omp_relation_instances where id = ".str_replace("\"", "\\\"", str_replace("[\]","",$p_relinst_id)).";";
		parent::execute($sql);

		$sql = "update omp_relation_instances set weight = (weight + 10) where rel_id = ".$ret['rel_id']." and parent_inst_id = ".$ret['parent_inst_id']." and weight < ".$ret['weight'].";";
		parent::update_one($sql);

		return true;
	}


    ///////////////////////////////////////////////////////////////////////////////////////////
    function updateNiceurlsForChildRelations($inst_id, $isDeleting = false) {
        $sql = "SELECT * FROM omp_niceurl LIMIT 1";
        $niceurl = parent::get_one($sql);
        if (!array_key_exists('parents', $niceurl)) return;

        $sql = "SELECT id FROM omp_relations WHERE name = 'Childs'";
        $rel_ids = parent::get_data($sql);
        if (!$rel_ids) return;

        $rel_id_list = implode(',', array_column($rel_ids, 'id'));

        $sql = "SELECT * FROM omp_relation_instances WHERE rel_id IN ($rel_id_list)";
        $relations = parent::get_data($sql);

        $sql = "SELECT inst_id, language, niceurl FROM omp_niceurl";
        $niceurls = parent::get_data($sql);

        $niceurls = collect($niceurls)->groupBy('inst_id')->map(function ($group) {
            return $group->keyBy('language')->toArray();
        })->toArray();

        $languages = config('editora.availableLanguages');

        $child_to_parents = [];
        $parent_to_children = [];

        foreach ($relations as $rel) {
            $parent = $rel['parent_inst_id'];
            $child = $rel['child_inst_id'];

            $child_to_parents[$child][] = $parent;
            $parent_to_children[$parent][] = $child;
        }

        $root = $inst_id;
        $visited = [];

        while (!empty($child_to_parents[$root]) && !isset($visited[$root])) {
            $visited[$root] = true;
            $root = $child_to_parents[$root][0];
        }

		$sqlUpdates = [];

        $this->setNiceurlsRecursive($root, [], $parent_to_children, $child_to_parents, $niceurls, $languages, $isDeleting ? $inst_id : null, $sqlUpdates);

        foreach ($sqlUpdates as $sql) {
            parent::update_one($sql);
        }
    }


    ///////////////////////////////////////////////////////////////////////////////////////////
    function setNiceurlsRecursive($current_id, $parent_chain, $parent_to_children, $child_to_parents, $niceurls, $languages, $deleted_node_id = null, &$sqlUpdates = []) {

        if ($deleted_node_id == $current_id) {
            unset($parent_chain[array_search($child_to_parents[$current_id][0] ?? null, $parent_chain)]);
        } else {
            if (!in_array($current_id, array_values($parent_chain)) && isset($child_to_parents[$current_id][0])) {
                $parent_chain[] = $child_to_parents[$current_id][0] ?? $current_id;
            }
        }

        if (empty($parent_chain)) {
            $parents = 'NULL';
        } else {
            $parent_chain = array_unique($parent_chain);
            if (($key = array_search($current_id, $parent_chain)) !== false) {
                unset($parent_chain[$key]);
            }
            $parents = "'" . implode(',', $parent_chain) . "'";
        }

        foreach ($languages as $language) {
            $full_niceurl = $this->getFullNiceUrl($niceurls, $parent_chain, $current_id, $language);
            $sqlUpdates[] = "UPDATE omp_niceurl SET parents = " . $parents . ", full_niceurl = " . $full_niceurl . " WHERE niceurl != '' AND niceurl IS NOT NULL AND inst_id = " . (int)$current_id . " AND language = '" . $language . "'";
        }

        $children = $parent_to_children[$current_id] ?? [];
        foreach ($children as $child_id) {
            $this->setNiceurlsRecursive($child_id, $parent_chain, $parent_to_children, $child_to_parents, $niceurls, $languages, $deleted_node_id, $sqlUpdates);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    function getFullNiceUrl($niceurls, $parent_chain, $instance_id, $language = null) {
        $parent_chain = array_unique($parent_chain ?? []);
        if (($key = array_search($instance_id, $parent_chain)) !== false) {
            unset($parent_chain[$key]);
        }
        $full_niceurl = '';
        foreach ($parent_chain ?? [] as $key => $parent) {
            if (isset($niceurls[$parent][$language])) {
                $full_niceurl .= ($key > 0 ? '/' : '') . $niceurls[$parent][$language]['niceurl'];
            }
        }

        if (empty($parent_chain) || $parent_chain == null || $full_niceurl == '') {
            return 'NULL';
        }

        if (isset($niceurls[$instance_id][$language])) {
            $full_niceurl .= '/' . $niceurls[$instance_id][$language]['niceurl'];
        }

        return "'" . str_replace("'", "\'", $full_niceurl) . "'";
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
