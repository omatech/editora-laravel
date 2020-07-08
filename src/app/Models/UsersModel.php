<?php

namespace Omatech\Editora\app\Models;


use App;
use Illuminate\Database\Eloquent\Model;

class UsersModel extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'omp_users';

    public static function getCategories($p_username){
        $user = self::where('username', $p_username)->first();

        return $user;
    }



}