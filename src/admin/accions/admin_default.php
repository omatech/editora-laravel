<?php

namespace Omatech\Editora\Admin\Accions;

use Illuminate\Support\Facades\Session;

class AdminDefault extends BaseController
{
    public function render()
    {
        $viewData = [
            'arrayLangs' => config('editora-admin.languages')
        ];

        return response()->view('editora::pages.login', $viewData);
    }
}
