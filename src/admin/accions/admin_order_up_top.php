<?php
//à
use Illuminate\Support\Facades\Session;

    $sc=new security();
if ($sc->testSession()==0) {
    $sc->endSession();
} else {
    $params=get_params_info();

    $ly=new layout();
    $in=new instances();
    $at=new attributes();
    $re=new relations();
    $ly_t=new layout_template();
    $at_t=new attributes_template();
    $params['param1'] = $params['param10'];
    $params['param2'] = $params['param11'];

    $message=html_message_ok($re->relationInstanceUpTop($params));
    $in->refreshCache($params);
        
    $title=EDITORA_NAME." -> ".getMessage('info_view_object');
    $ly_t->pinta_CommonLayout($top_menu, $buscador, $last_accessed, $favorites, $special, $ly, $in, $lg, $params);
    $body=$at_t->instanceAttributes_view($at->getInstanceAttributes('V', $params), $params);
    $parents=$ly_t->paintParentsList($in->getParents($params), $params);

    $_REQUEST['view']='container';
}
