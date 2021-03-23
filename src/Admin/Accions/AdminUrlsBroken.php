<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;

class AdminUrlsBroken extends AuthController
{
    public function render()
    {
        $instances = new Instances;
        $params=get_params_info();
        $menu = [];

        $title = EDITORA_NAME;
        $items = $instances->brokenLinks();
        $menu = $this->loadMenu($instances, $params);


        $viewData = array_merge($menu, [
            'body_class' => 'edit-view',
            'title' => $title,
            'instances' => $items,
        ]);

        return response()->view('editora::pages.broken_links', $viewData);
    }
}
