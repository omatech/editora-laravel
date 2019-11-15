<?php

namespace Omatech\Editora\Admin\Models;

use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Session;

class Security extends Model
{
    public function login($p_username, $p_password, $u_lang = null)
    {
        $hasher = new BcryptHasher();

        $sql = 'select u.id u_id, u.complete_name nom,
                    u.rol_id r_id, r.rol_name r_nom, u.tipus tipus, u.password password,
                    language
                    from omp_users u, omp_roles r 
                    where username = :username
                    and r.id = u.rol_id
                    limit 1';

        $user = $this->get_one($sql, ['username' => $p_username]);
        
        if (!$user || !$hasher->check($p_password, $user['password'])) {
            return 0;
        }

        Session::put('user_id', $user['u_id']);
        Session::put('user_nom', $user['nom']);
        Session::put('rol_id', $user['r_id']);
        Session::put('rol_nom', $user['r_nom']);
        Session::put('user_type', $user['tipus']);
        Session::put('user_language', $user['language']);
        Session::put('u_language', $u_lang);
        Session::put('u_lang', $u_lang);
        
        $this->cacheClasses($user['id']);
        $this->cleanUserInstances($user['id']);

        return 1;
    }

    private function cacheClasses($userId)
    {
        Session::put('classes_cache', []);

        $sql = "select c.id, c.name_".getDefaultLanguage(). " name
                from omp_classes c
                , omp_roles_classes rc
                , omp_users u
                where u.id = :userId
                and u.rol_id = rc.rol_id
                and rc.class_id = c.id
                and (rc.browseable = 'Y' or rc.browseable = 'P')";

        $result = parent::get_data($sql, ['userId' => $userId]);

        if (!$result) {
            return 0;
        }

        $cc = array();
        foreach ($result as $row) {
            $cc[$row['id']]=$row['name'];
        }
        Session::put('classes_cache', $cc);

        return true;
    }

    private function cleanUserInstances($userId)
    {
        $sql = "delete from omp_user_instances where user_id = :userId and tipo_acceso = 'A' and fecha<DATE_SUB(NOW(),INTERVAL 60 DAY)";
        $this->delete($sql, ['userId' => $userId]);
    }



    function testSession()
    {
        if ($_SERVER['REQUEST_URI']!='') {
            Session::put('last_page', $_SERVER['REQUEST_URI']);
        }
        if (Session::has('user_id') && Session::get('user_id')!='') {
            return 1;
        } else {
            return 0;
        }
    }
    
    function endSession()
    {
        return response()->redirectTo(APP_BASE)->send();
        die();
    }
    
    
    function getStatus($p_status, $p_class_id)
    {
        $p_rol_id=Session::get('rol_id');
        $sql = "select status".$p_status." st from omp_roles_classes where class_id = :classId and rol_id = :rolId";

        $ret=$this->get_one($sql, ['classId' => $p_class_id, 'rolId' => $p_rol_id]);
        if (!$ret) {
            return 0;
        }

        if ($ret['st']=="Y") {
            return 1;
        }

        return 0;
    }
    
    public function getAccess($p_camp_nom, $param_arr)
    {
        if ($param_arr['param1']!=0) {
            $p_class_id=$param_arr['param1'];
        } else {
            //es una relación, miramos el parent_class_id
            $p_class_id=$param_arr['param10'];
        }
        $p_rol_id=Session::get('rol_id');
        
        $sql = "select ".$p_camp_nom." x_able from omp_roles_classes where class_id = :classId and rol_id = :rolId";

        $ret = $this->get_one($sql, ['classId' => $p_class_id, 'rolId' => $p_rol_id]);

        if (!$ret) {
            return 0;
        }

        if ($ret['x_able'] == "Y" || $ret['x_able'] == "P") {
            return 1;
        }
        
        return 0;
    }


    function getStatusAccess($param_arr)
    {
        global $dbh;
        $p_inst_id=$param_arr['param2'];
        $p_rol_id=Session::get('rol_id');

        $sql = "select i.status st, rc.status1 st1, rc.status2 st2, rc.status3 st3
		from omp_instances i, omp_roles_classes rc
		where i.id = ".mysql_real_escape_string($p_inst_id)."
		and rc.rol_id = ".mysql_real_escape_string($p_rol_id)."
		and rc.class_id = i.class_id;";

        $ret = mysql_query($sql, $dbh);
        if (!$ret) {
            return 0;
        }

        while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
            if ($row['st']=="P" && $row['st1']=="Y") {
                return 1;
            }
            if ($row['st']=="V" && $row['st2']=="Y") {
                return 1;
            }
            if ($row['st']=="O" && $row['st3']=="Y") {
                return 1;
            }
        }
        return 0;
    }

    function redirect_url($url)
    {
        header("Location: ".$url);
        die();
    }

    function change_password($user_id, $password)
    {
        $hasher = new BcryptHasher();
        if (defined('HASHED_PASSWORDS') && HASHED_PASSWORDS == 1) {
            $password = $hasher->make($password);
            $sql = 'UPDATE omp_users SET password="'.$password.'" , hashed_password = 1 WHERE id="'.$user_id.'"';
        } else {
            $sql = 'UPDATE omp_users SET password="'.$password.'" , hashed_password = 0 WHERE id="'.$user_id.'"';
        }
        $ret = $this->update_one($sql);

        if (!$ret) {
            return 0;
        } else {
            return 1;
        }
    }

    function check_change_password($user_id, $password)
    {
        $hasher = new BcryptHasher();

        $sql = "select u.password password from omp_users u where id = :userID";

        $ret = $this->get_one($sql, ['userID' => $user_id]);

        if (!$ret) {
            return 0;
        }

        $flag = 0;
        if (defined('HASHED_PASSWORDS') && HASHED_PASSWORDS == 1) {
            if ($hasher->check($password, $ret['password'])) {
                $flag  = 1;
            }
        } else {
            if ($password == $ret['password']) {
                $flag = 1;
            }
        }
        return $flag;
    }
}
