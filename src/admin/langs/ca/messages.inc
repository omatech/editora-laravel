<?php
function getMessage ($id) {
	$mess_arr = array();
	
	//Mensajes del Twitter
	$mess_arr['no_twitted'] = 'El missatge no s\'ha pujat a Twitter';
	$mess_arr['twitted'] = 'El missatge s\'ha pujat a Twitter';
	$mess_arr['too_long'] = 'El missatge és massa llarg';
	$mess_arr['caracters'] = 'caràcters';

	$mess_arr['site_login_title'] = 'Gestor de continguts de';

	$mess_arr['login_label_username'] = 'Usuari';
	$mess_arr['login_label_password'] = 'Contrasenya';
	$mess_arr['login_label_language'] = 'Idioma';
	$mess_arr['login_label_submit'] = 'Entrar';

	$mess_arr['user'] = 'Usuari';
	$mess_arr['name'] = 'Nom';

	$mess_arr['language_choose_ca'] = 'Català';
	$mess_arr['language_choose_en'] = 'English';
	$mess_arr['language_choose_es'] = 'Español';

	$mess_arr['info_create_object'] = 'Crear objecte del tipus';
	$mess_arr['info_view_object'] = 'Veure objecte';
	$mess_arr['info_edit_object'] = 'Editar objecte';
	$mess_arr['info_delete_object'] = 'Borrar objecte del tipus';
	$mess_arr['info_delete_image'] = 'Borrar imagen';
	$mess_arr['info_delete_multiple_object'] = 'Borrar varis objecte de diferents tipus';
	$mess_arr['info_search_object'] = 'Cercar objecte';
	$mess_arr['info_addjoin_object'] = 'Crear objecte relacionat';
	$mess_arr['info_addmassive_object'] = 'Creador massiu d\'objectes relacionats';
	$mess_arr['info_joinsearch_object_lite'] = 'Cercar objectes';
	$mess_arr['info_joinsearch_object'] = 'Cercar objectes de tipus';
    $mess_arr['creando_objeto'] = 'Creant objecte ';
	$mess_arr['viendo_objeto'] = 'Veient objecte ';
	$mess_arr['viendo_objeto2'] = ' de tipus ';
	$mess_arr['related_objects'] = 'Objectes relacionats trobats';
	$mess_arr['delete_not_possible'] = 'No és possible l\'esborrat, existeixen una o més relacions amb l\'objecte que desitja eliminar.';

	$mess_arr['info_objects_found'] = 'Objectes trobats';
	$mess_arr['info_object_created'] = 'creat satisfactoriament';
	$mess_arr['info_object_updated'] = 'actualitzat satisfactoriament';
	$mess_arr['info_object_deleted'] = 'Objecte borrat satisfactoriament';
	$mess_arr['info_image_deleted'] = 'Imatge esborrada satisfactoriament';
	$mess_arr['info_object_deleted_plural'] = 'Objectes borrats satisfactoriament';
	$mess_arr['info_object_favorited'] = 'Objecte afegit satisfactoriament als seus preferits';
	$mess_arr['info_object_unfavorited'] = 'Objecte eliminat satisfactoriament dels seus preferits';
	$mess_arr['info_object_joined'] = 'relacionat correctament';
	$mess_arr['info_object_unjoined'] = 'relacionat incorrectament';
	$mess_arr['info_objects_joined'] = 'Objectes relacionats correctament';
	$mess_arr['relacionar_varis'] = 'Relacionar';
	$mess_arr['eliminar_varis'] = 'Eliminar';
	$mess_arr['info_create_role'] = 'Crear rol';
	$mess_arr['info_view_role'] = 'Veure rols';
	$mess_arr['info_edit_role'] = 'Editar objecte';
	$mess_arr['info_delete_role'] = 'Borrar objecte del tipus';
	$mess_arr['info_view_role_user'] = 'Veure usuaris del rol';
	$mess_arr['info_delete_role_users'] = 'Borrar usuaris del rol';
	$mess_arr['info_view_privileges'] = 'Veure privilegis';
	$mess_arr['info_edit_privileges'] = 'Editar privilegis';
	$mess_arr['info_privileges_updated'] = 'Privilegis modificats satisfactoriament';
	$mess_arr['info_privileges_unexpected'] = 'Error inesperat al modificar els privilegis';
	$mess_arr['info_create_user'] = 'Crear usuari';
	$mess_arr['info_edit_user'] = 'Editar usuari';

	$mess_arr['site_login_session']='Inici de sessió';
	$mess_arr['info_error'] = 'La sessió ha caducat o no s\'ha validat correctament. Si us plau, torni a validar-se al sistema.';
	$mess_arr['info_word_keyword_explanation'] = 'Ordenar per paraula clau';
	$mess_arr['info_word_status_explanation'] = 'Ordenar per estat';
	$mess_arr['info_word_publishing_creation_date_explanation'] = 'Ordenar per data de creació';
	$mess_arr['info_word_publishing_begins_explanation'] = 'Ordenar per inici de publicació';
	$mess_arr['info_word_publishing_update_explanation'] = 'Ordenar per data de modificació';
	$mess_arr['info_word_last_modifications'] = 'Últimes modificacions';
	$mess_arr['info_word_close_last_modifications'] = 'Tancar ultimes modificacions';
	$mess_arr['info_word_close_relations'] = 'Tancar relacions';
	
	$mess_arr['info_word_privileges'] = 'La seva sessió ha caducat o no s\'ha validat correctament, per favor, torni a validar-se al sistema.';
	$mess_arr['info_word_object'] = 'Objecte';
	$mess_arr['info_word_joinwith'] = 'per relacionar amb';
	$mess_arr['info_word_typeof'] = 'de tipus';
	$mess_arr['info_word_searcher'] = 'Cercador';
    $mess_arr['advanced_search'] = 'Cerca avançada';
	$mess_arr['info_word_type'] = 'Tipus';
	$mess_arr['info_word_type_explanation']='Ordenar per tipus';
	$mess_arr['info_word_text'] = 'Text';
	$mess_arr['info_word_select_date'] = 'Seleccionar data';
	$mess_arr['info_word_initial_date'] = 'Data inicial';
	$mess_arr['info_word_final_date'] = 'Data final';
	$mess_arr['info_word_search_button'] = 'Cercar';
	$mess_arr['info_word_no_orderup'] = 'Pujar (no permès)';
	$mess_arr['info_word_no_orderdown'] = 'Baixar (no permès)';
	$mess_arr['info_word_ordertop'] = 'Pujar al inici';
	$mess_arr['info_word_orderbottom'] = 'Baixar al final';
	$mess_arr['info_word_orderup'] = 'Pujar';
	$mess_arr['info_word_orderdown'] = 'Baixar';
	$mess_arr['info_word_unjoin'] = 'Desvincular';
	$mess_arr['info_word_unjoin_all'] = 'Desvincular totes';
	$mess_arr['info_word_edit'] = 'Editar';
	$mess_arr['info_word_status'] = 'Estat';
	$mess_arr['info_word_status_explanation'] = 'Ordenar per estat';
	$mess_arr['not_published'] = 'No publicat';
	$mess_arr['pending'] = 'Pendent';
	$mess_arr['published_on'] = 'Publicat el';
	$mess_arr['published_from'] = 'Publicat del';
	$mess_arr['published_to'] = 'al';
	$mess_arr['published'] = 'Publicat';
	$mess_arr['info_word_status_pending'] = 'Pendent';
	$mess_arr['info_word_status_reviewed'] = 'Revisat';
	$mess_arr['info_word_status_published'] = 'Publicat';
	$mess_arr['info_word_publishing_begins'] = 'Inici de publicació';
	$mess_arr['info_word_publishing_ends'] = 'Final de publicació';
	$mess_arr['info_word_add_button'] = 'Afegir';
	$mess_arr['info_word_update_button'] = 'Actualitzar';
	$mess_arr['info_word_edit_button'] = 'Editar';
	$mess_arr['info_word_testurl'] = 'Provar URL';
	$mess_arr['info_word_addjoin'] = 'Afegir i relacionar';
	$mess_arr['info_word_clone'] = 'Clonar';
	$mess_arr['info_word_addmassive'] = 'Afegir massivament';
	$mess_arr['info_word_exportmassive'] = 'Exportar tots els fills';
	$mess_arr['info_word_join'] = 'Relacionar';
	$mess_arr['info_word_ID'] = 'ID';
	$mess_arr['info_word_img'] = 'Img.';
	$mess_arr['info_word_ID_explanation']='Ordenar per identificador';
	$mess_arr['info_word_keyword'] = 'Clau';
	$mess_arr['info_word_view'] = 'Veure';
	$mess_arr['info_word_yes'] = 'Sí';
	$mess_arr['info_word_no'] = 'No';
	$mess_arr['info_word_areyousure'] = 'Està segur?';
	$mess_arr['info_word_areyousure_arr'] = 'Els següents elements s\'eliminaran, està segur?';
	$mess_arr['info_word_not_eliminate'] = 'Els següents element no es poden eliminar';
	$mess_arr['info_word_utilities'] = 'Útils';
	$mess_arr['info_word_creation_date'] = 'Data de creació';
	$mess_arr['info_word_update_date'] = 'Data de modificació';
	$mess_arr['info_word_date_condition'] = 'Condició de data';
	$mess_arr['info_word_addfavorites'] = 'Afegir a preferits';
	$mess_arr['info_word_delete'] = 'Esborrar';
	$mess_arr['info_word_delete_instance'] = 'Esborrar instancia';
	$mess_arr['info_word_modifyperm'] = 'No';
	$mess_arr['info_word_deletejoin'] = 'Relació eliminada satisfactoriament';
	$mess_arr['info_word_deletejoin_error'] = 'La relació no s\'ha pogut eliminar correctament';
	$mess_arr['info_word_orderjoin'] = 'Ordre actualizat satisfactoriament';
	$mess_arr['info_word_logged'] = 'Estàs connectat com a';
	$mess_arr['info_word_logout'] = 'Desconnectar';
	$mess_arr['info_word_search'] = 'Cercador';
	$mess_arr['info_word_relations'] = 'Relacions';
	$mess_arr['info_word_more_relations'] = 'Més relacions';
	$mess_arr['info_word_childs'] = 'Fills';
	$mess_arr['info_word_instance'] = 'Instancia';
	$mess_arr['info_word_order'] = 'Ordenar';

	$mess_arr['error_role_privileges'] = 'El seu rol d\'usuari no te els privilegis necessaris per accedir a aquesta funcionalitat';
	$mess_arr['error_role_privileges2'] = 'El seu rol d\'usuari no te els privilegis necessaris per accedir a aquesta instancia';
	$mess_arr['error_role_privileges3'] = 'No té suficients privilegis per editar l\'objecte en l\'estat actual';
	$mess_arr['error_session_timeout'] = 'La seva sessió ha caducat o no s\'ha validat correctament, per favor, torni a validar-se al sistema';
	$mess_arr['error_param_mandatory'] = 'Falta algun paràmetre obligatori';
	$mess_arr['error_param_data'] = 'Error en les dades!';
	$mess_arr['error_param_urlnice'] = 'URL Maca duplicada!';
	$mess_arr['error_search_data'] = 'Dades de cerca erronies';
	$mess_arr['error_user_passwdnotmatch'] = 'Les contrasenyes no coincideixen';
	$mess_arr['error_object_delete'] = 'No és possible l\'eliminació, existeix una o més relacions amb l\'objecte que es vol eliminar!';
	$mess_arr['error_format_massive'] = 'El format de les representacions no es correcte';
	$mess_arr['error_page'] = 'Aquesta acció no existeix. Si us plau, repasseu que la url sigui correcte';

	$mess_arr['navigation_back'] = 'Tornar';
	$mess_arr['navigation_home'] = 'Inici';
	$mess_arr['navigation_new'] = 'Nou';
	$mess_arr['navigation_list'] = 'Llista';
    $mess_arr['class_new'] = 'Crear element';

	$mess_arr['container_mini_cercador'] = 'Mini cercador';
	$mess_arr['container_twitter'] = 'Afegir a Twitter';
	$mess_arr['container_objetos_favoritos'] = 'Objectes preferits';
	$mess_arr['container_objetos_padre'] = 'Objectes pare';
	$mess_arr['container_ultimos_objetos'] = 'Últims objectes accedits';
	$mess_arr['container_derechos_reservados'] = 'Tots els drets reservats';
	$mess_arr['container_mes_funcions'] = 'Més funcions';
	$mess_arr['erase_cache'] = 'Esborrar caché';
	$mess_arr['clean_cache'] = 'Netejar caché';
	$mess_arr['regenerate_cache_without'] = 'Regenerar caché sense esborrar';
	$mess_arr['regenerate_cache'] = 'Regenerar caché';
	$mess_arr['info_erase_cache'] = 'Caché esborrada correctament';
	$mess_arr['info_clean_cache'] = 'Caché netejada correctament';
	$mess_arr['info_regenerate_cache'] = 'Caché regenerada correctament';
	$mess_arr['enllacos_trencats'] = 'Trobar enllaços trencats';
	$mess_arr['unlinked_images'] = 'Trobar imatges no relacionades';
    $mess_arr['no_found'] = 'No s\'han trobat elements';


	$mess_arr['paginacion_registros_del']='Registres del';
	$mess_arr['paginacion_al']='al';
    $mess_arr['paginacion_de']='de';
	$mess_arr['paginacion_anteriores']='Anteriors';
	$mess_arr['paginacion_siguientes']='Següents';

	$mess_arr['all']='Tots';

	$mess_arr['delete_massive_confirmation']='Estas segur de que vols desvincular i ESBORRRAR tots els objectes relacionats?';

	$mess_arr['relacionando']='Relacionant amb el objete ';
	$mess_arr['relacionando2']=' de classe ';

	$mess_arr['preview_format']='Format';
	$mess_arr['preview_debug']='Debug';
	$mess_arr['preview_enviar']='Enviar';

	$mess_arr['theoric_size']='Mida teòrica';
	$mess_arr['real_size']='Mida real';
	$mess_arr['msie6']='Alerta! Estas intentant entrar amb Internet Explorer 6.0. A part de ser una versió molt obsoleta d\'aquest navegador, es coneguda per tenir molts forats de seguretat i molts bugs. Omatech ha deixat de donar suport a aquesta versió del navegador i es posible que moltes funcionalitats estiguin desactivades.

	Disculpeu les molèsties.
	<br /><br /><a title="Descarregar Firefox" href="http://www.getfirefox.com">Descarregar Firefox</a>
	<br /><a title="Descarregar Google Chrome" href="http://www.google.com/chrome">Descarregar Google Chrome</a>';


	$mess_arr['file_name']="Nom d'imatge";
	$mess_arr['file_image']="Imatge";
	$mess_arr['file_upload_date']="Data de pujada";
	$mess_arr['acciones']="Accions";

	$mess_arr['error_getting_apps']="Error al obtener los datos de la App, inténtelo de nuevo";
	
	$mess_arr['tab']="Pestanya";
	$mess_arr['crop_image']="Retallar imatge";
	$mess_arr['leave_as_is']="Deixar com està";
	$mess_arr['resize']="Redimensionar";
	$mess_arr['crop']="Retallar";
	$mess_arr['small_image']="L'imatge que s'intenta pujar es més petita de la necessaria. Es possible que al retallar l'imatge o redimensionarla, aquesta perdi qualitat.";
	$mess_arr['small_original']="Original massa petit";
	$mess_arr['not_empty']="No está buit";
	$mess_arr['original']="Original";
	
	$mess_arr['save']="Guardar";
	$mess_arr['saved']="Guardat!";
	$mess_arr['saved_wrong']="Alguna cosa ha anat malament!";
	
	$mess_arr['static_text']="Textos estatics";
	$mess_arr['settings']="Configuració";
	$mess_arr['user_data']="Dades usuari";

	$mess_arr['change_password'] = 'Canviar contrasenya';
	$mess_arr['current_password'] = 'Contrasenya actual';
	$mess_arr['new_password'] = 'Nova contrasenya';
	$mess_arr['repeat_password'] = 'Repeteix la contrasenya';
	$mess_arr['save_password'] = 'Guardar contraseña';	
	$mess_arr['create_users'] = 'Crear usuaris';
	$mess_arr['new_user'] = 'Nou usuari';
	$mess_arr['role'] = 'Rol';
	$mess_arr['create'] = 'Crear';
	$mess_arr['special_functions'] ='Funcions especials';
	$mess_arr['unlinked_files'] = 'Arxius no relacionats';
	$mess_arr['view_unlinked_files'] = 'Mostrant arxius no relacionats';
	$mess_arr['unlink'] = 'Desvincular';
	$mess_arr['load_content'] = 'Carrega contingut';
	$mess_arr['path'] = 'Ruta';
	$mess_arr['size'] = 'Mida';
	$mess_arr['image'] = 'Imatge';
	$mess_arr['info_word_language'] = 'Idioma';
	$mess_arr['results_for'] = 'Resultats para';
	$mess_arr['list_classes_to_export'] = 'Llistat de classes per exportar';
	$mess_arr['inserted_elements'] = 'Elements insertats';
	$mess_arr['export'] = 'Exportar';
	$mess_arr['file_import'] = 'Importar excel';
	$mess_arr['video_preview'] = 'Previsualització del vídeo';
	$mess_arr['not_related_instances'] = 'No hi ha objectes relacionats';
	$mess_arr['preview'] = 'Previsualitzar';
	$mess_arr['close'] = 'Tancar';

	return $mess_arr[$id];
}
?>