<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Events\AdminDeleteRelationInstanceEvent;
use Omatech\Editora\Admin\Models\attributes;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Relations;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Event;

class AdminDeleteRelationInstance extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();

        if(Session::get('rol_id')==1 || $security->getAccess('browseable',$params)) {
            $at=new attributes();
            $re=new relations();

            $re->deleteRelationInstance($params);
            $rel_id=$_REQUEST['p_rel_id'];
            $parent_inst_id=$_REQUEST['parent_inst_id'];

            $rows=$at->getRelatedInstances($rel_id, $parent_inst_id);

            $viewData['attribute'] = $rows;
            $viewData['instance']['id'] = $parent_inst_id;
            $viewData['attribute']['id'] = $rel_id;

            $this->dispatchEvent($parent_inst_id);

            return view('editora::templates.relation_ajax_list', $viewData);

        }
       die();
    }

    private function dispatchEvent(int $instanceId): void
    {
        try {
            if ($instanceId !== 0) {
                Event::dispatch(new AdminDeleteRelationInstanceEvent($instanceId));
            }
        }catch (\Exception $exception) {}
    }
}

