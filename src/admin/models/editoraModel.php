<?php

namespace Omatech\Editora\Admin\Models;

use Illuminate\Support\Str;

class editoraModel extends model{

    function get_autocomplete($lang, $class_id, $term) {

        $sql = "select i.id, c.name_".$lang." as className, i.key_fields label, i.status
		from omp_instances i, omp_classes c
		where i.class_id = c.id
		and class_id in (".$class_id.")
		and key_fields like '%".$term."%'";

        $ret = parent::get_data($sql);
        if(!$ret){
            return null;
        }else{
            return json_encode($ret);
        }

    }

    function get_class_info($class_id) {

        $sql = "select c.id, c.name_".$_SESSION['u_lang']." as class_name, c.name as class_internal_name
		from omp_classes c
		where id = ".$class_id.";";

        $ret = parent::get_data($sql);
        if(!$ret){
            return null;
        }else{
            return $ret[0];
        }

    }

    function get_user_info($user_id) {

        $sql = "select id, username, complete_name
		from omp_users
		where id = ".$user_id.";";

        $ret = parent::get_data($sql);
        if(!$ret){
            return null;
        }else{
            return $ret[0];
        }

    }

    function exist_username($user_id, $username) {

        $sql = 'select id from omp_users where username = "'.$username.'" and id != "'.$user_id.'";';

        $ret = parent::get_data($sql);
        if(!$ret){
            return false;
        }else{
            return true;
        }

    }

    function update_user_info($user_id, $username, $complete_name) {

        $sql = 'UPDATE omp_users set username ="'.$username.'", complete_name ="'.$complete_name.'" where id = '.$user_id.' ';

        $ret = parent::update_one($sql);

        if(!$ret){
            return false;
        }else{
            return true;
        }

    }

    function create_user($username, $complete_name, $rol_id) {

        $language = 'ca';
        $tipus = 'U';

        $password = Str::random(8);
        $hashed_password = bcrypt($password);

        $sql = 'INSERT into omp_users (username, complete_name, rol_id, password, language, tipus, hashed_password) 
                VALUES ("'.$username.'", "'.$complete_name.'", "'.$rol_id.'", "'.$hashed_password.'", "'.$language.'", "'.$tipus.'", 1)';

        $ret = parent::update_one($sql);

        if(!$ret){
            return false;
        }else{
            return $password;
        }

    }

    function getListImage($inst_id, $atri_name) {
        $sql="select text_val
            from omp_values v, omp_attributes a
            where v.atri_id = a.id
            and v.inst_id=".$inst_id."
            and a.tag='".$atri_name."'
            limit 1;";

        $ret = parent::get_one($sql);
        if(!$ret) {
            return '';
        }
        return $ret['text_val'];
    }

    function getInstanceImage($inst_id) {
        $sql="select text_val as image from omp_values where inst_id = ".$inst_id." and img_info is not null limit 1;";

        $ret = parent::get_one($sql);
        if(!$ret) {
            return null;
        }
        return $ret['image'];
    }

    function get_roles() {

        $sql = 'select id, rol_name from omp_roles where enabled = "Y" and id != 1;';

        $ret = parent::get_data($sql);
        if(!$ret){
            return null;
        }else{
            return $ret;
        }

    }
}