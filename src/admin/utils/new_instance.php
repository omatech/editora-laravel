<?php
/**
 * new_instance
 *
 * @version $Id$
 * @copyright 2004 
 **/

function insert_form ($p_class_id) {
	return(html_view_attributes($p_class_id, 'I', NULL, NULL));
}

function insert_form_related ($p_child_class_id, $p_parent_class_id, $p_relation_id, $p_inst_id) {
	$include_form = '';
	$include_form .= '
	<input type="hidden" name="p_parent_class_id" value="'.$p_parent_class_id.'">
	<input type="hidden" name="p_relation_id" value="'.$p_relation_id.'">
	<input type="hidden" name="p_parent_inst_id" value="'.$p_inst_id.'">';

	return(html_view_attributes($p_child_class_id, 'I', NULL, $include_form));
}

function insert_form_massive ($p_child_class_id, $p_parent_class_id, $p_relation_id, $p_inst_id) {
	$include_form = '';
	$include_form .= '
	<input type="hidden" name="p_parent_class_id" value="'.$p_parent_class_id.'">
	<input type="hidden" name="p_relation_id" value="'.$p_relation_id.'">
	<input type="hidden" name="p_parent_inst_id" value="'.$p_inst_id.'">';

	return(html_view_massive($p_child_class_id, 'M', NULL, $include_form));
}
?>