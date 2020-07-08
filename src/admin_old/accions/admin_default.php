<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;

class AdminDefault extends BaseController
{
    public function render()
    {
        $viewData = [
            'arrayLangs' => config('editora-admin.languages'),
            'title' => EDITORA_NAME
        ];

        return response()->view('editora::pages.login', $viewData);
    }
}
