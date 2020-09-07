<?php

namespace Omatech\Editora\app\Repositories\Eloquent;

use Illuminate\Support\Facades\Session;
use Omatech\Editora\app\Repositories\Interfaces\InstancesRepositoryInterface;
use Omatech\Editora\app\Models\InstancesModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class InstancesRepository.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class InstancesRepository extends InstancesModel
{
    public static function getClassInstances($class)
    {
        $instances = self::where('class_id', $class)->get();

        return $instances;
    }

    public static function getLastInstances($limit=40)
    {
        $instances = self::orderBy('update_date', 'DESC')->limit($limit)->get();
        
        return $instances;
    }

    public static function getFavorites()
    {

        // $instances = self::users_instances()->where('users_instances_id', 7)->get();
        // return $instances;
        // dd($instances);
        //Session::get('user_id'), 'F', 'asc', 30
        //die(Session::get('user_id'));
        // $instances = self::select()->where()
        $p_user_id = 7;
        $p_tipo = 'F';
        $order = 'asc';
        $limit = 30;

        $sql = "select i.id, i.status, i.class_id, i.key_fields, max(ui.fecha) fecha
		from omp_instances i
		, omp_user_instances ui
		where ui.user_id = ".$p_user_id."
		and ui.tipo_acceso='".$p_tipo."'
		and ui.inst_id = i.id
		group by i.id, i.status, i.class_id, i.key_fields
		order by fecha ".$order."
		limit ".$limit;

        $ret=parent::get_data($sql);
        if (!$ret) {
            return array();
        }else{
            $instances = [];
            foreach ($ret as $item){
                $instances[$item['id']]= $item;
            }

            return $instances;
        }

    }
}
