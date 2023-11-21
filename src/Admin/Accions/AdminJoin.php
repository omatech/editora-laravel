<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;

class AdminJoin extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();

        if (Session::get('rol_id') == 1 || $security->getAccess('browseable', $params)) {
            $instances = new Instances;
            $params['p_mode'] = 'R';
            $params['p_acces_type'] = 'A';
            $class = $this->getClassInfo($params);
            if (isset($params['param12']) && $params['param12'] != '') {
                $title = EDITORA_NAME . " -> " . getMessage('info_joinsearch_object') . " " . getClassName($params['param12']) . " " . getMessage('info_word_joinwith') . " " . $class['class_name'];
            } else {
                $title = EDITORA_NAME . " -> " . getMessage('info_joinsearch_object_lite') . " " . getMessage('info_word_joinwith') . " " . $class['class_name'];
            }
            $instances->logAccess($params);
        }
        $count = $instances->instanceList_count($params);
        $page = $params['param3'];
        $parent = $this->getParentInfo($params);
        $paramsRelation = $this->getParamsRelation($params);
        $viewData = array_merge($this->loadMenu($instances, $params), [
            'title' => $title,
            'instances' => $instances->instanceList($params),
            'parents' => $instances->getParents($params),
            'class' => $class,
            'p_mode' => $params['p_mode'],
            'p_action' => $params['p_action'],
            'parent' => $parent,
            'params_relation' => $paramsRelation,
            'count' => $count,
            'paginator' => new LengthAwarePaginator($instances, $count, 40, $page,
                ['pageName' => 'p_pagina', 'path' => '/' . config('editora-admin.route.prefix') . '/join', 'query' => [
                    'p_class_id' => $paramsRelation['class_id'],
                    'p_inst_id' => $parent['inst_id'],
                    'p_relation_id' => $parent['rel_id'],
                    'p_parent_inst_id' => $parent['inst_id'],
                    'p_parent_class_id' => $parent['class_id'],
                    'p_child_class_id' => $paramsRelation['child_class_id'],
                ]]),
        ]);
        return response()->view('editora::pages.list_instances', $viewData);
    }

    private function getParentInfo($params)
    {
        $parent['rel_id'] = $params['param9'];
        $parent['inst_id'] = $params['param11'];
        $parent['class_id'] = $params['param10'];
        return $parent;
    }

    private function getClassInfo($params)
    {
        $class['class_name'] = getClassName($params['param10']);
        $class['class_internal_name'] = getClassNameInternalName($params['param10']);
        return $class;
    }

    private function getParamsRelation($params)
    {
        $params_relation['class_id'] = $params['param1'];
        $params_relation['inst_id'] = $params['param2'];
        $params_relation['relation_id'] = $params['param9'];
        $params_relation['parent_inst_id'] = $params['param11'];
        $params_relation['parent_class_id'] = $params['param10'];
        $params_relation['child_class_id'] = $params['param12'];
        return $params_relation;
    }
}
