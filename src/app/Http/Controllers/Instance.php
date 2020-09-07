<?php

namespace Omatech\Editora\app\Http\Controllers;

use App;
use Illuminate\Http\Request;

// use Omatech\Editora\app\Models\InstancesModel;
// use Omatech\Editora\Templates\InstancesTemplate;
// use Omatech\Editora\app\Repositories\Interfaces\InstancesRepositoryInterface;

// use Omatech\Editora\app\Repositories\Eloquent\InstancesRepository;
use Omatech\Editora\app\Models\InstancesModel;
use Illuminate\Support\Facades\Session;

class Instance extends Controller {

    private $instances;
    private $instacesTemplate;

    /** @var InstancesRepositoryInterface */
    // private $repository;

    public function __construct(InstancesModel $instances)
    {
        Session::put('user_id', 3);
        
        parent::__construct();
        // $this->repository = $repository;
        $this->instances = new InstancesModel;
        // $this->instacesTemplate = new InstancesTemplate;
    }
    
    public function get_main(Request $request) 
    {
        $params['p_mode'] = $p_mode = 'V';
        $page = 1;

        $title = 'EDITORA_NAME';

        $last_instances = $this->instances->instanceList($params);

        $count = -1;

        $menu = $this->loadMenu($this->instances);
        $viewData = array_merge($menu, [
            'p_mode' => $p_mode,
            'title' => $title,
            'instances' => $last_instances,
            'count' => $count,
            'page' => $page
        ]);

        return response()->view('editora::pages.home', $viewData);
    }
    
    public function view_class_instances(Request $request) 
    {
        $params['mode'] = $p_mode = 'V';
        $params['class_id'] = $request['class_id'];
        
        $page = 1;

        $title = 'EDITORA_NAME';

        $last_instances = $this->instances->instanceList($params);

        $count = -1;

        $menu = $this->loadMenu($this->instances);
        $viewData = array_merge($menu, [
            'p_mode' => $p_mode,
            'title' => $title,
            'instances' => $last_instances,
            'count' => $count,
            'page' => $page
        ]);

        return response()->view('editora::pages.home', $viewData);
    }
    
    public function view_instance(Request $request){
        
        $menu = [];

        if (Session::get('rol_id') == 1 || $security->getAccess('browseable', $params)) {
        
            //$at=new attributes();

            $params['p_mode']= $p_mode = 'V';
            $params['p_acces_type']='A';
            $params['class_id'] = $request['class_id'];
            $params['inst_id'] = $request['inst_id'];

            $title='';//EDITORA_NAME." -> ".__('editora_lang::messages.info_view_object');
            //$instances->logAccess($params);
            die('000');
            $menu = $this->loadMenu($this->instances);
            $instance = $at->getInstanceAttributes($p_mode, $params);

            $parents = $instances->getDistinctParents($params);

            $view = 'editora::pages.instance';
        }else{
            
            $instance['instance_info']=null;
            $title = __('editora_lang::messages.error_role_privileges');
            $view ='editora::pages.permission_denied';
        }

        $viewData = array_merge($menu, [
            'instance' => $instance['instance_info'],
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
            'parents' => $parents,
            'status_list' => isset($instance['status_list']) ? $instance['status_list'] : null

        ]);

        return response()->view( $view, $viewData);
    }

}