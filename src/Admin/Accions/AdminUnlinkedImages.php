<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;

class AdminUnlinkedImages extends AuthController
{
    public function render()
    {
        $instances = new Instances;
        $params=get_params_info();
        $params['p_mode']='V';

        $title = EDITORA_NAME;
        $items = $instances->unlinkedImages();
        $menu = $this->loadMenu($instances, $params);

        $viewData = array_merge($menu, [
            'title' => $title,
            'instances' => $items,
        ]);

        return response()->view('editora::pages.unlinked_images', $viewData);
    }
}
