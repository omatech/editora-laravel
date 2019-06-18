<?php

namespace Omatech\Editora\Admin\Templates;

class Template
{
	var $info = "";
	var $limit = 10;
	
	function __construct($_info=null, $_limit=10){
		$this->info = $_info;
		$this->limit = $_limit;
	}

	function set_limit($_limit){
		$this->limit = $_limit;
	}

	function set_info($_info)
	{
		$this->info = $_info;
	}

	function pinta_paginacion($p_pagina, $p_action, $p_params, $p_num_rows, $conta)
	{
		$res='';
		$flag_ultima=false;



		/*
		$res.='<table><tr>';
		if ($p_pagina!='')
			$res.='<td class="omp_menu2">Página '.$p_pagina.'</td>';
		else
			$res.='<td class="omp_menu2">&nbsp;</td>';
                */

		$first_row=0;
		if ($p_pagina)
			$first_row=($p_pagina-1)*ROWS_PER_PAGE;
			

		if ($p_num_rows == ROWS_PER_PAGE) // OK, tenim mes pagines
			$last_row=$first_row+ROWS_PER_PAGE-1;
		else
		{// Es la ultima pagina tenim menys registres del normal
			$last_row=$first_row+$p_num_rows-1;
			$flag_ultima=true;
		}
                 $res.='<div class="pager right">';


		if ($last_row+1 == 0) // Comprovacio del numero de rows per si no n'hi ha cap
			$res.='<h2>'.getMessage('paginacion_registros_del').' 0 '.getMessage('paginacion_al').' '.($last_row+1).' '.getMessage('paginacion_de').' '.$conta.'</h2>';

		else // Perfecte tenim mes de 0 rows
			$res.='<h2>'.getMessage('paginacion_registros_del').' '.($first_row+1).' '.getMessage('paginacion_al').' '.($last_row+1).' '.getMessage('paginacion_de').' '.$conta.'</h2>';
                 $res.='<ul>';
		if ($p_pagina!=1 && $p_pagina!=null)

			$res.='<li class="prev"><a href="'.APP_BASE.'/'.$p_action.'?p_pagina='.($p_pagina-1).$p_params.'" title="'.getMessage('paginacion_anteriores').'">'.getMessage('paginacion_anteriores').'</a></li>';
		

		if (!$flag_ultima)
                       
		{
			if ($p_pagina == null || $p_pagina <= 0)
				$p_pagina=1;

			$res.='<li class="next"><a href="'.APP_BASE.'/'.$p_action.'?p_pagina='.($p_pagina+1).$p_params.'" title="'.getMessage('paginacion_siguientes').'">'.getMessage('paginacion_siguientes').'</a></li>';
		}
		$res.='</ul></div>';

		return $res;
	}
	
	function status_to_html($p_status)
	{
         
	if ($p_status=='P')
		return '<span class="status pending" title="'.getMessage('not_published').'"></span>';

	if ($p_status=='O')
		return '<span class="status publish" title="'.getMessage('published').'"></span>';

	if ($p_status=='V')
		return '<span class="status revised" title="'.getMessage('pending').'"></span>';
	}

	function status_to_html2($p_status)
	{
		if ($p_status=='P')
                    return'<input type="text" id="" name="" value="'.getMessage('pending').'" disabled="disabled" class="w_50 disabled" />
                           <span class="status pending" title="'.getMessage('pending').'">'.getMessage('pending').'</span><!-- "publish" "pending" "revised"-->';
			
		if ($p_status=='O')
			return '<input type="text" id="" name="" value="'.getMessage('published').'" disabled="disabled" class="w_50 disabled" />
                           <span class="status publish" title="'.getMessage('published').'">'.getMessage('published').'</span><!-- "publish" "pending" "revised" -->';

		if ($p_status=='V')
			return '<input type="text" id="" name="" value="'.getMessage('info_word_status_reviewed').'" disabled="disabled" class="w_50 disabled" />
                           <span class="status revised" title="'.getMessage('info_word_status_reviewed').'">'.getMessage('info_word_status_reviewed').'</span><!-- "publish" "pending" "revised" -->';
	}

	function getClassList($p_class_id) {
		$cc = $_SESSION['classes_cache'];
                asort($cc);

		$ret = "";

		$ret.='<select name="p_class_id" class="maxw_150">';
		$ret.='<option value="">'.getMessage('all').'</option>';

		//for ($i = 0; $i < sizeof($classes_ids); $i++)
                foreach ($cc as $id => $nom)
		{
                    
			if ($id==$p_class_id)
				$ret .= '<option selected value="'.$id.'">'.$nom.'</option>';
			else
				$ret .= '<option value="'.$id.'">'.$nom.'</option>';
		}
		$ret.="</select>";
		return $ret;
	}
}