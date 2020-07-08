<?php
//à
	function pinta_CommonLayout($lg, $params, &$top_menu, &$buscador, &$last_accessed, &$favorites, &$special) {
		$ly=new layout();
		$in=new instances();
		$ly_t=new layout_template();

		$top_menu=$ly_t->pinta_topMenu($ly->get_topMenu($lg));
		$buscador=$ly_t->paintSearchForm($params);
		$last_accessed=$ly_t->paintUserBoxInstances($in->getLastInstances(),'A', $params);
		$favorites=$ly_t->paintUserBoxInstances($in->getFavorites(),'F', $params);
		$special=$ly_t->getSpecialFunctions();
	}
?>