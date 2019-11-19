<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\statictext;
use Illuminate\Support\Facades\Session;

class AdminEditStaticText extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params = get_params_info();
        $menu = [];

        if (Session::has('rol_id')) {
            $instances = new Instances;
            $st = new statictext();

            $params = get_params_info();
            $params['p_mode'] = 'V';
            $params['param1'] = 4;
            $key = $_REQUEST['key'];

            $title = EDITORA_NAME;


            if (isset($_REQUEST['hiddencheck'])) {
                if ($st->set_static_text($_POST, $key)) {
                    Session::put('flashmessage', 'Se ha guardado ok');
                    $security->redirect_url(APP_BASE . '/static_text');
                }
            }

            $languages = $st->get_static_text_languages();
            $stext_lg = array();
            foreach ($languages as $lang) {
                array_push($stext_lg, $st->get_one_static_text($key, $lang) ?? [
                        'id' => null,
                        'text_key' => $key,
                        'language' => $lang,
                        'text_value' => null
                    ]);
            }
            $menu = $this->loadMenu($instances, $params);
        }

        $viewData = array_merge($menu, [
            'key' => $key,
            'stext_lg' => $stext_lg,
            'body_class' => 'edit-view',
            'title' => $title,
        ]);

        return response()->view('editora::pages.static_texts_edit', $viewData);
    }
}
