<?php

namespace Omatech\Editora\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Interface InstancesRepositoryInterface.
 *
 * @package namespace App\Repositories\Interfaces;
 */
interface InstancesRepositoryInterface
{
    public function get_last_instances($limit);
}
