<?php
	
namespace Omatech\Editora\Admin\Extras\Accions;

use Omatech\Editora\Admin\Templates\Template;

class AccionListTemplate extends Template
{
	function getExtraSpecialFunctions($p_class_id, $p_inst_id) {
		$html = '';
		
		//Textos por idioma
		$lang = $_SESSION['u_lang']; 
		switch ($lang){
			case 'ca':

			break;
			case 'es':

			break;
			case 'en':

			break;
			default://ca

			break;
		}

		//Listado de acciones
        
		return $html;
	}
	
}