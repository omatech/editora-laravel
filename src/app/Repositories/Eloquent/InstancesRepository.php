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
class InstancesRepository implements InstancesRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return InstancesModel::class;
    }


    public static function getClassInstances($class){
        $instances = $this->model()->where('class_id', $class)
            ->get();

        return $instances;
    }

    public static function getLastInstances($limit=40){
        $instances = self::orderBy('update_date', 'DESC')->limit($limit)->get();
        return $instances;
    }

    


}
