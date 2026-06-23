<?php

namespace Omatech\Editora\Admin\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class EditoraModel extends model{

    function get_autocomplete($lang, $class_id, $term) {

        // SECURITY (SQLi): sanitize language column-name suffix, cast numeric id, bind free-text term
        $lang = preg_replace('/[^a-zA-Z]/', '', (string) $lang);
        $sql = "select i.id, c.name_".$lang." as className, i.key_fields label, i.status
		from omp_instances i, omp_classes c
		where i.class_id = c.id
		and class_id in (".((int) $class_id).")
		and key_fields like :term";

        $ret = parent::get_data($sql, ['term' => '%'.$term.'%']);
        if(!$ret){
            return null;
        }else{
            return json_encode($ret);
        }

    }

    function get_class_info($class_id) {

        // SECURITY (SQLi): sanitize language column-name suffix, cast numeric id
        $lg = preg_replace('/[^a-zA-Z]/', '', (string) Session::get('u_lang'));
        $sql = "select c.id, c.name_".$lg ." as class_name, c.name as class_internal_name
		from omp_classes c
		where id = ".((int) $class_id).";";

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
		where id = ".((int) $user_id).";"; // SECURITY (SQLi): cast numeric id

        $ret = parent::get_data($sql);
        if(!$ret){
            return null;
        }else{
            return $ret[0];
        }

    }

    function exist_username($user_id, $username) {

        // SECURITY (SQLi): bind free-text username, cast numeric id
        $sql = 'select id from omp_users where username = :username and id != '.((int) $user_id).';';

        $ret = parent::get_data($sql, ['username' => $username]);
        if(!$ret){
            return false;
        }else{
            return true;
        }

    }

    function update_user_info($user_id, $username, $complete_name) {

        // SECURITY (SQLi): bind free-text values, cast numeric id
        $sql = 'UPDATE omp_users set username = :username, complete_name = :complete_name where id = '.((int) $user_id).' ';

        $ret = parent::update_one($sql, ['username' => $username, 'complete_name' => $complete_name]);

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

        // SECURITY (SQLi): bind free-text values, cast numeric id
        $sql = 'INSERT into omp_users (username, complete_name, rol_id, password, language, tipus, hashed_password)
                VALUES (:username, :complete_name, "'.((int) $rol_id).'", :hashed_password, :language, :tipus, 1)';

        $ret = parent::update_one($sql, ['username' => $username, 'complete_name' => $complete_name, 'hashed_password' => $hashed_password, 'language' => $language, 'tipus' => $tipus]);

        if(!$ret){
            return false;
        }else{
            return $password;
        }

    }

    function getListImage($inst_id, $atri_name) {
        // SECURITY (SQLi): cast numeric id, bind free-text tag
        $sql="select text_val
            from omp_values v, omp_attributes a
            where v.atri_id = a.id
            and v.inst_id=".((int) $inst_id)."
            and a.tag=:atri_name
            limit 1;";

        $ret = parent::get_one($sql, ['atri_name' => $atri_name]);
        if(!$ret) {
            return '';
        }
        return $ret['text_val'];
    }

    function getInstanceImage($inst_id) {
        // SECURITY (SQLi): cast numeric id
        $sql="select text_val as image from omp_values where inst_id = ".((int) $inst_id)." and img_info is not null limit 1;";

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