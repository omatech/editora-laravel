<?php

namespace Omatech\Editora\app\Models;

use Illuminate\Database\Eloquent\Model;

class InstancesModel extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'omp_instances';




    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->hasOne(ClassesModel::class);
    }


    

    // public static function getClassInstances($class){
    //     $instances = self::where('class_id', $class)
    //         ->get();

    //     return $instances;
    // }

    // public static function getLastInstances($limit=40){
    //     $instances = self::orderBy('update_date', 'DESC')->limit($limit)->get();
    //     return $instances;
    // }

}