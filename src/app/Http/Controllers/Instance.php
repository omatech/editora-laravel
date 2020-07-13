<?php

namespace Omatech\Editora\app\Http\Controllers;

use App;
use Illuminate\Http\Request;

// use Omatech\Editora\app\Models\InstancesModel;
// use Omatech\Editora\Templates\InstancesTemplate;
// use Omatech\Editora\app\Repositories\Interfaces\InstancesRepositoryInterface;
use Omatech\Editora\app\Repositories\Eloquent\InstancesRepository;

class Instance extends Controller {

    private $instances;
    private $instacesTemplate;

    /** @var InstancesRepositoryInterface */
    private $repository;

    public function __construct(InstancesRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
        // $this->instances = new Instances;
        // $this->instacesTemplate = new InstancesTemplate;
    }
    
    public function get_main(Request $request) 
    {
        
        $params['p_mode'] = $p_mode = 'V';
        $page = 1;

        $title = 'EDITORA_NAME';

        $instances = $this->repository->getLastInstances(40);
        $count = -1;

        $menu = [];//$this->loadMenu($this->instances, $params);

        $viewData = array_merge($menu, [
            'p_mode' => $p_mode,
            'title' => $title,
            'instances' => $instances,
            'count' => $count,
            'page' => $page
        ]);
        return response()->view('editora::pages.home', $viewData);


	}

}