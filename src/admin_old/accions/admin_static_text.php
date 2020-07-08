<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\statictext;
use Illuminate\Support\Facades\Session;

class AdminStaticText extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        $menu = [];

        if (Session::has('rol_id')) {
            $instances = new Instances;
            $st = new statictext();
            $params=get_params_info();
            $params['p_mode']='V';
            $params['param1']=4;

            $p_mode = 'I';
            $title=EDITORA_NAME;

            $menu = $this->loadMenu($instances, $params);


            $static_texts = $st->get_static_text_keys();

            $languages = $st->get_static_text_languages();

            if (isset($_GET['text_lang'])) {
                $selected_language = $_GET['text_lang'];
                $static_texts = $st->get_static_text_lang($_GET['text_lang']);
            } else {
                $selected_language = 'ALL';
                $static_texts = $st->get_static_text_lang(current($languages));
            }
        }

        $viewData = array_merge($menu, [
            'static_texts' => $static_texts,
            'languages' => $languages,
            'selected_language' => $selected_language,
            'p_mode' => $p_mode,
            'body_class' => 'edit-view',
            'title' => $title,
        ]);

        return response()->view('editora::pages.static_texts', $viewData);
    }
}
