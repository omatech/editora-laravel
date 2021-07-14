<?php
//à

function get_params_info()
{
    $param_arr = array();
    $param_arr['p_action'] = $_REQUEST['action'];
    for ($i = 1; $i < 50; $i++) {
        $param_arr['param'.$i] = '';
    }
    if (isset($_REQUEST['p_class_id'])) {
        $param_arr['param1'] = $_REQUEST['p_class_id'];
    }
    if (isset($_REQUEST['p_inst_id'])) {
        $param_arr['param2'] = $_REQUEST['p_inst_id'];
    }
    if (isset($_REQUEST['p_pagina'])) {
        $param_arr['param3'] = $_REQUEST['p_pagina'];
    }
    if (isset($_REQUEST['p_search_query'])) {
        $param_arr['param4'] = $_REQUEST['p_search_query'];
    }
    if (isset($_REQUEST['p_fecha_ini'])) {
        $param_arr['param5'] = $_REQUEST['p_fecha_ini'];
    }
    if (isset($_REQUEST['p_fecha_fin'])) {
        $param_arr['param6'] = $_REQUEST['p_fecha_fin'];
    }
    if (isset($_REQUEST['p_order_by'])) {
        $param_arr['param7'] = $_REQUEST['p_order_by'];
    }
    if (isset($_REQUEST['p_search_state'])) {
        $param_arr['param8'] = $_REQUEST['p_search_state'];
    }
    if (isset($_REQUEST['p_relation_id'])) {
        $param_arr['param9'] = $_REQUEST['p_relation_id'];
    }
    if (isset($_REQUEST['p_parent_class_id'])) {
        $param_arr['param10'] = $_REQUEST['p_parent_class_id'];
    }
    if (isset($_REQUEST['p_parent_inst_id'])) {
        $param_arr['param11'] = $_REQUEST['p_parent_inst_id'];
    }
    if (isset($_REQUEST['p_child_class_id'])) {
        $param_arr['param12'] = $_REQUEST['p_child_class_id'];
    }
    if (isset($_REQUEST['p_child_inst_id'])) {
        $param_arr['param13'] = $_REQUEST['p_child_inst_id'];
    }
    if (isset($_REQUEST['p_tab']) && $_REQUEST['p_tab'] != '') {
        $param_arr['param14'] = $_REQUEST['p_tab'];
    } else {
        $param_arr['param14'] = 1;
    }
    return $param_arr;
}