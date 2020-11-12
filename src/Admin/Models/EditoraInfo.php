<?php

namespace Omatech\Editora\Admin\Models;

class EditoraInfo extends Model{


    function getUsersInfo(){

        $sql = "SELECT U.id, U.username, U.complete_name, U.tipus, U.rol_id, R.rol_name
                FROM omp_users AS U INNER JOIN omp_roles AS R
                WHERE U.rol_id = R.id";

        $ret = $this->get_data($sql);

        if (!$ret) {
            return array();
        }
        return $ret;
    }


    function getRoles(){

        $sql = "SELECT *
                FROM omp_roles";

        $ret = $this->get_data($sql);

        if (!$ret) {
            return array();
        }

        $roles = [];
        foreach($ret as $item){
            $roles[$item['id']] = $item ['rol_name'];
        }

        return $roles;
    }



    function getClassesRoles(){

        $sql = 'SELECT C.name, C.id as class_id, RC.rol_id, RC.browseable, RC.insertable, RC.editable, RC.deleteable, RC.permisos, RC.status1, RC.status2, RC.status3, RC.status4, RC.status5
        FROM omp_classes AS C INNER JOIN omp_roles_classes AS RC
        WHERE C.id = RC.class_id
        ORDER BY RC.rol_id, C.id ASC;';

        $ret = $this->get_data($sql);

        if (!$ret) {
            return array();
        }

        $roles = [];
        foreach($ret as $item){
            if(!isset($roles[$item['rol_id']])){
                $roles[$item['rol_id']] = [];
            }
            array_push($roles[$item['rol_id']], $item);
        }


        return $roles;

    }
   
}
