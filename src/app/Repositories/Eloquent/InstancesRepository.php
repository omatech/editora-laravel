<?php

namespace Omatech\Editora\app\Repositories\Eloquent;

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
    public static function getClassInstances($class){
        $instances = self::where('class_id', $class)->get();

        return $instances;
    }

    public static function getLastInstances($limit=40){
        $instances = self::orderBy('update_date', 'DESC')->limit($limit)->get();
        
        return $instances;
    }

    


}
