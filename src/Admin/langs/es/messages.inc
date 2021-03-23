<?php
function getMessage ($id) {
	$mess_arr = array();

	//Mensajes del Twitter
	$mess_arr['no_twitted'] = 'El mensaje no se ha subido a Twitter';
	$mess_arr['twitted'] = 'El mensaje se ha subido a Twitter';
	$mess_arr['too_long'] = 'El mensaje es demasiado largo';
	$mess_arr['caracters'] = 'caracteres';

	$mess_arr['site_login_title'] = 'Gestor de contenidos de';

	$mess_arr['login_label_username'] = 'Usuario';
	$mess_arr['login_label_password'] = 'Contraseña';
	$mess_arr['login_label_language'] = 'Idioma';
	$mess_arr['login_label_submit'] = 'Entrar';

	$mess_arr['language_choose_ca'] = 'Català';
	$mess_arr['language_choose_en'] = 'English';
	$mess_arr['language_choose_es'] = 'Español';

	$mess_arr['user'] = 'Usuario';
	$mess_arr['name'] = 'Nombre';


	$mess_arr['info_create_object'] = 'Crear objecto del tipo';
	$mess_arr['info_view_object'] = 'Ver objeto';
	$mess_arr['info_edit_object'] = 'Editar objeto';
	$mess_arr['info_delete_object'] = 'Borrar objeto del tipo';
	$mess_arr['info_delete_image'] = 'Borrar imagen';
	$mess_arr['info_delete_multiple_object'] = 'Borrar varios objeto de diferentes tipos';
	$mess_arr['info_search_object'] = 'Buscar objeto';
	$mess_arr['info_addjoin_object'] = 'Crear objeto relacionado';
	$mess_arr['info_addmassive_object'] = 'Creador massivo de objetos relacionados';
	$mess_arr['info_joinsearch_object_lite'] = 'Buscar objetos';
	$mess_arr['info_joinsearch_object'] = 'Buscar objetos de tipo';
	$mess_arr['creando_objeto'] = 'Creando objeto ';
	$mess_arr['viendo_objeto'] = 'Viendo objeto ';
	$mess_arr['viendo_objeto2'] = ' de tipo ';
	$mess_arr['related_objects'] = 'Objetos relacionados encontrados';
	$mess_arr['delete_not_possible'] = 'No es posible el borrado, existen una o más relaciones con el objeto que desea eliminar.';

	$mess_arr['info_objects_found'] = 'Objetos encontrados';
	$mess_arr['info_object_created'] = 'creado satisfactoriamente';
	$mess_arr['info_object_updated'] = 'actualizado satisfactoriamente';
	$mess_arr['info_object_deleted'] = 'Objeto borrado satisfactoriamente';
	$mess_arr['info_image_deleted'] = 'Imagen borrado satisfactoriamente';
	$mess_arr['info_object_deleted_plural'] = 'Objetos borrados satisfactoriamente';
	$mess_arr['info_object_favorited'] = 'Objeto añadido satisfactoriamente a sus favoritos';
	$mess_arr['info_object_unfavorited'] = 'Objeto eliminado satisfactoriamente de sus favoritos';
	$mess_arr['info_object_joined'] = 'relacionado correctamente';
	$mess_arr['info_object_unjoined'] = 'relacionado incorrectamente';
	$mess_arr['info_objects_joined'] = 'Objectos relacionados correctamente';
	$mess_arr['relacionar_varis'] = 'Relacionar';
	$mess_arr['eliminar_varis'] = 'Eliminar';
	$mess_arr['info_create_role'] = 'Crear rol';
	$mess_arr['info_view_role'] = 'Ver rols';
	$mess_arr['info_edit_role'] = 'Editar objeto';
	$mess_arr['info_delete_role'] = 'Borrar objeto del tipo';
	$mess_arr['info_view_role_user'] = 'Ver usuarios del rol';
	$mess_arr['info_delete_role_users'] = 'Borrar usuarios del rol';
	$mess_arr['info_view_privileges'] = 'Ver privilegios';
	$mess_arr['info_edit_privileges'] = 'Editar privilegios';
	$mess_arr['info_privileges_updated'] = 'Privilegios modificados satisfactoriamente';
	$mess_arr['info_privileges_unexpected'] = 'Error inesperado al modificar los privilegios';
	$mess_arr['info_create_user'] = 'Crear usuario';
	$mess_arr['info_edit_user'] = 'Editar usuario';

	$mess_arr['site_login_session']='Inicio de sesión';
	$mess_arr['info_error'] = 'La sesión ha caducado o no se ha validado correctamente. Por favor, vuelva a validarse en el sistema.';
	$mess_arr['info_word_keyword_explanation'] = 'Ordenar por palabra clave';
	$mess_arr['info_word_status_explanation'] = 'Ordenar por estado';
	$mess_arr['info_word_publishing_creation_date_explanation'] = 'Ordenar por fecha de creación';
	$mess_arr['info_word_publishing_begins_explanation'] = 'Ordenar por fecha de publicación';
    $mess_arr['info_word_publishing_update_explanation'] = 'Ordenar por fecha de modificación';
    $mess_arr['info_word_last_modifications'] = 'Últimas modificaciones';
    $mess_arr['info_word_close_last_modifications'] = 'Cerrar últimas modificaciones';
	$mess_arr['info_word_close_relations'] = 'Cerrar relaciones';

	$mess_arr['info_word_privileges'] = 'Privilegios';
	$mess_arr['info_word_object'] = 'Objeto';
	$mess_arr['info_word_joinwith'] = 'para relacionar con';
	$mess_arr['info_word_typeof'] = 'de tipo';
	$mess_arr['info_word_searcher'] = 'Buscador';
	$mess_arr['advanced_search'] = 'Búsqueda avanzada';
	$mess_arr['info_word_type'] = 'Tipo';
	$mess_arr['info_word_type_explantion']='Ordenar por tipo';
	$mess_arr['info_word_text'] = 'Texto';
	$mess_arr['info_word_select_date'] = 'Seleccionar fecha';
	$mess_arr['info_word_initial_date'] = 'Fecha inicial';
	$mess_arr['info_word_final_date'] = 'Fecha final';
	$mess_arr['info_word_search_button'] = 'Buscar';
	$mess_arr['info_word_no_orderup'] = 'Subir (no permitido)';
	$mess_arr['info_word_no_orderdown'] = 'Bajar (no permitido)';
	$mess_arr['info_word_ordertop'] = 'Subir al inicio';
	$mess_arr['info_word_orderbottom'] = 'Bajar al final';
	$mess_arr['info_word_orderup'] = 'Subir';
	$mess_arr['info_word_orderdown'] = 'Bajar';
	$mess_arr['info_word_unjoin'] = 'Desvincular';
	$mess_arr['info_word_unjoin_all'] = 'Desvincular todas';
	$mess_arr['info_word_edit'] = 'Editar';
	$mess_arr['info_word_status'] = 'Estado';
	$mess_arr['info_word_status_explanation'] = 'Ordenar por estado';
	$mess_arr['not_published'] = 'No publicado';
	$mess_arr['pending'] = 'Pendiente';
	$mess_arr['published'] = 'Publicado';
	$mess_arr['published_on'] = 'Publicado el';
	$mess_arr['published_from'] = 'Publicado del';
	$mess_arr['published_to'] = 'al';
	$mess_arr['info_word_status_pending'] = 'Pendiente';
	$mess_arr['info_word_status_reviewed'] = 'Revisado';
	$mess_arr['info_word_status_published'] = 'Publicado';
	$mess_arr['info_word_publishing_begins'] = 'Inicio de publicación';
	$mess_arr['info_word_publishing_ends'] = 'Final de publicación';
	$mess_arr['info_word_add_button'] = 'Añadir';
	$mess_arr['info_word_update_button'] = 'Actualizar';
	$mess_arr['info_word_edit_button'] = 'Editar';
	$mess_arr['info_word_testurl'] = 'Probar URL';
	$mess_arr['info_word_addjoin'] = 'Añadir y relacionar';
	$mess_arr['info_word_clone'] = 'Clonar';
	$mess_arr['info_word_addmassive'] = 'Añadir masivamente';
	$mess_arr['info_word_exportmassive'] = 'Exportar todos los hijos';
	$mess_arr['info_word_join'] = 'Relacionar';
	$mess_arr['info_word_ID'] = 'ID';
	$mess_arr['info_word_img'] = 'Img.';
	$mess_arr['info_word_ID_explanation'] = 'Ordenar por identificador';
	$mess_arr['info_word_keyword'] = 'Clave';
	$mess_arr['info_word_view'] = 'Ver';
	$mess_arr['info_word_yes'] = 'Sí';
	$mess_arr['info_word_no'] = 'No';
	$mess_arr['info_word_areyousure'] = '¿Está seguro?';
	$mess_arr['info_word_areyousure_arr'] = 'Los siguientes elementos se eliminaran, está seguro?';
	$mess_arr['info_word_not_eliminate'] = 'Los siguientes elementos no se pueden eliminar';
	$mess_arr['info_word_utilities'] = 'Herramientas';
	$mess_arr['info_word_creation_date'] = 'Fecha de creación';
    $mess_arr['info_word_update_date'] = 'Fecha de modificación';
    $mess_arr['info_word_date_condition'] = 'Condición de fecha';
	$mess_arr['info_word_addfavorites'] = 'Añadir a favoritos';
	$mess_arr['info_word_delete'] = 'Borrar';
	$mess_arr['info_word_delete_instance'] = 'Borrar instancia';
	$mess_arr['info_word_modifyperm'] = 'No';
	$mess_arr['info_word_deletejoin'] = 'Relación eliminada satisfactoriamente';
	$mess_arr['info_word_deletejoin_error'] = 'La relación no se ha podido eliminar';
	$mess_arr['info_word_orderjoin'] = 'Orden actualizado satisfactoriamente';
	$mess_arr['info_word_logged'] = 'Estás conectado como';
	$mess_arr['info_word_logout'] = 'Desconectar';
	$mess_arr['info_word_search'] = 'Buscador';
	$mess_arr['info_word_more_relations'] = 'Más relaciones';
	$mess_arr['info_word_relations'] = 'Relaciones';
	$mess_arr['info_word_childs'] = 'Hijos';
	$mess_arr['info_word_instance'] = 'Instancia';
	$mess_arr['info_word_order'] = 'Ordenar';

	$mess_arr['error_role_privileges'] = 'Su rol de usuario no tiene los privilegios necesarios para acceder a esta funcionalidad';
	$mess_arr['error_role_privileges2'] = 'Su rol de usuario no tiene los privilegios necesarios para acceder a esta instancia';
	$mess_arr['error_role_privileges3'] = 'No tiene suficientes privilegios para editar el objeto en el estado actual';
	$mess_arr['error_session_timeout'] = 'Su sesión ha caducado o no se ha validado correctamente, por favor, vuelva a conectarse al sistema';
	$mess_arr['error_param_mandatory'] = 'Falta algun parámetro obligatorio!';
	$mess_arr['error_param_data'] = 'Error en los datos!';
	$mess_arr['error_param_urlnice'] = 'URL Bonita duplicada!';
	$mess_arr['error_search_data'] = 'Datos de búsqueda erroneos!';
	$mess_arr['error_user_passwdnotmatch'] = 'Las contraseñas no coinciden';
	$mess_arr['error_object_delete'] = 'No es posible el borrado, existen una o más relaciones con el objeto que desea eliminar!';
	$mess_arr['error_format_massive'] = 'El formato de las representaciones no es correcto';
	$mess_arr['error_page'] = 'Esta acción no existe. Por favor, repase que la url sea correcta';

	$mess_arr['navigation_back'] = 'Volver';
	$mess_arr['navigation_home'] = 'Inicio';
	$mess_arr['navigation_new'] = 'Nuevo';
	$mess_arr['navigation_list'] = 'Lista';
    $mess_arr['class_new'] = 'Crear elemento';

	$mess_arr['container_mini_cercador']='Mini buscador';
	$mess_arr['container_twitter']='Añadir a Twitter';
	$mess_arr['container_objetos_favoritos']='Objetos favoritos';
	$mess_arr['container_objetos_padre']='Objetos padre';
	$mess_arr['container_ultimos_objetos']='Últimos objetos accedidos';
	$mess_arr['container_derechos_reservados']='Todos los derechos reservados';
	$mess_arr['container_mes_funcions'] = 'Más funciones';
	$mess_arr['erase_cache'] = 'Borrar caché';
	$mess_arr['clean_cache'] = 'Limpiar caché';
	$mess_arr['regenerate_cache_without'] = 'Regenerar caché sin borrar';
	$mess_arr['regenerate_cache'] = 'Regenerar caché';
	$mess_arr['info_erase_cache'] = 'Caché eliminada correctamente';
	$mess_arr['info_clean_cache'] = 'Caché limpiada correctamente';
	$mess_arr['info_regenerate_cache'] = 'Caché regenerada correctamente';
	$mess_arr['enllacos_trencats'] = 'Encontrar enlaces rotos';
	$mess_arr['unlinked_images'] = 'Encontrar imágenes no relacionadas';
	$mess_arr['no_found'] = 'No se han encontrado elementos';


	$mess_arr['paginacion_registros_del']='Registros del';
	$mess_arr['paginacion_al']='al';
    $mess_arr['paginacion_de']='de';
	$mess_arr['paginacion_anteriores']='Anteriores';
	$mess_arr['paginacion_siguientes']='Siguientes';

	$mess_arr['all']='Todos';

	$mess_arr['delete_massive_confirmation']='¿Estás seguro de querer desvincular y BORRAR todos los objetos relacionados?';

	$mess_arr['relacionando']='Relacionando con el objeto ';
	$mess_arr['relacionando2']=' de clase ';

	$mess_arr['preview_format']='Format';
	$mess_arr['preview_debug']='Debug';
	$mess_arr['preview_enviar']='Enviar';

	$mess_arr['theoric_size']='Tamaño teòrico';
	$mess_arr['real_size']='Tamaño real';
	$mess_arr['msie6']='Atención! Estás intentando entrar con Internet Explorer 6.0. Esta es una versión obsoleta de este navegador, conocida por tener muchos agujeros de seguridad i muchos bugs. Omatech ha dejado de dar soporte a esta versión del navegador i es posible que muchas funcionalidades esten desactivadas.

	Disculpen las molestias
	<br /><br /><a title="Descargar Firefox" href="http://www.getfirefox.com">Descargar Firefox</a>
	<br /><a title="Descargar Google Chrome" href="http://www.google.com/chrome">Descargar Google Chrome</a>';


	$mess_arr['file_name']="Nombre de imagen";
	$mess_arr['file_image']="Imagen";
	$mess_arr['file_upload_date']="Fecha de subida";
	$mess_arr['acciones']="Acciones";

	$mess_arr['error_getting_apps']="Error al obtener los datos de la App, inténtelo de nuevo";
	
	$mess_arr['tab']="Pestaña";
	$mess_arr['crop_image']="Recortar imagen";
	$mess_arr['leave_as_is']="Dejar como está";
	$mess_arr['resize']="Redimensionar";
	$mess_arr['crop']="Recortar";
	$mess_arr['small_image']="La imagen que intenta subir es más pequeña de lo necesario. És posible que al recortar la imagen o redimensionarla, esta perdi calidad.";
	$mess_arr['small_original']="Original muy pequeño";
	$mess_arr['not_empty']="No está vacío";
	$mess_arr['original']="Original";
	
	$mess_arr['save']="Guardar";
	$mess_arr['saved']="Guardado!";
	$mess_arr['saved_wrong']="Algo ha ido mal!";

	$mess_arr['static_text']="Textos estaticos";
	$mess_arr['settings']="Configuración";
	$mess_arr['user_data']="Datos usuario";

	$mess_arr['change_password'] = 'Cambiar contraseña';
	$mess_arr['current_password'] = 'Contraseña actual';
	$mess_arr['new_password'] = 'Nueva caontraseña';
	$mess_arr['repeat_password'] = 'Repite la contraseña';
	$mess_arr['save_password'] = 'Guardar contraseña';
	$mess_arr['create_users'] = 'Crear usuarios';
	$mess_arr['new_user'] = 'Nuevo usuario';
	$mess_arr['role'] = 'Rol';
	$mess_arr['create'] = 'Crear';
	$mess_arr['special_functions'] ='Funciones especiales';
	$mess_arr['unlinked_files'] = 'Ficheros no relacionados';
	$mess_arr['view_unlinked_files'] = 'Mostrando ficheros no relacionados';
	$mess_arr['unlink'] = 'Desvincular';
	$mess_arr['load_content'] = 'Carga contenido';
	$mess_arr['path'] = 'Ruta';
	$mess_arr['size'] = 'Tamaño';
	$mess_arr['image'] = 'Imagen';
	$mess_arr['info_word_language'] = 'Idioma';
	$mess_arr['results_for'] = 'Resultados para';
	$mess_arr['list_classes_to_export'] = 'Listado de clases para exportar';
	$mess_arr['inserted_elements'] = 'Elementos insertados';
	$mess_arr['export'] = 'Exportar';
	$mess_arr['file_import'] = 'Importar excel';
	$mess_arr['video_preview'] = 'Previsualización del vídeo';
	$mess_arr['not_related_instances'] = 'No hay objetos relacionados';
	$mess_arr['preview'] = 'Previsualizar';
	$mess_arr['close'] = 'Cerrar';

	$mess_arr['editora_info'] = 'Editora info';
	$mess_arr['classes_sample_modal'] = 'Ejemplo:';
    $mess_arr['classes_modal_not_image'] = 'Sin previsualización';
    $mess_arr['urls_broken'] = 'Enlaces rotos';

	return $mess_arr[$id];
}
?>