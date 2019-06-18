<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Templates\InstancesTemplate;

class AdminDeleteImage2 extends AuthController
{
    private $instances;
    private $instacesTemplate;

    public function __construct()
    {
        parent::__construct();

        $this->instances = new Instances;
        $this->instacesTemplate = new InstancesTemplate;
    }

    public function render()
    {
        $params=get_params_info();
        $params['p_mode']='V';


        $this->instances->deleteImage($_REQUEST['image_full']);

        return redirect(route('editora.action', 'unlinked_images'));
    }
}
