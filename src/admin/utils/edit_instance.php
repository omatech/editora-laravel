<?php
/**
 * view_instance
 *
 * @version $Id$
 * @copyright 2004 
 **/

function edit_instance ($p_class_id, $p_inst_id) {
	return(html_view_attributes($p_class_id, 'U', $p_inst_id, NULL));
}
?>