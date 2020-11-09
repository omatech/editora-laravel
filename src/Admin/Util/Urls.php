<?php

namespace Omatech\Editora\Admin\Util;

use Omatech\Editora\Admin\Models\Security;

class Urls
{
    function my_urlencode($string)
    {
        return urlencode(str_replace("/","-", $string));
    }

    function extract_url_info()
    {
        $url = $_SERVER['REQUEST_URI'];
        $question_pos=strrpos($url, '?');
        if ($question_pos>0) $url=substr($url, 0, $question_pos);

        $amp_pos=strrpos($url, '&');
        if ($amp_pos>0) $url=substr($url, 0, $amp_pos);

        $laurl = explode ('/', trim($url,'/'));

        if(APP_BASE != "") {
            if (!isset($laurl[1]) || $laurl[1]=='' || $laurl[1]=='controller.php') { // First slash empty
                $sc=new Security();
                if ($sc->testSession()==0) $laurl[1]='default';//HOME
                else $laurl[1]='get_main';
            }
            $_REQUEST['action']=$laurl[1];
        } else {
            if (!isset($laurl[0]) || $laurl[0]=='' || $laurl[0]=='controller.php') { // First slash empty
                $sc=new Security();
                if ($sc->testSession()==0) $laurl[0]='default';//HOME
                else $laurl[0]='get_main';
            }
            $_REQUEST['action']=$laurl[0];
        }
    }

    function notfound()
    {
        header("HTTP/1.0 404 Not Found");
        header('Content-Type: text/html; charset=UTF-8', true);
        require_once(DIR_APLI_ADMIN.'/views/notfound.php');
    }

    function extract_url_info_ajax()
    {
        $url = $_SERVER['REQUEST_URI'];

        $question_pos=strrpos($url, '?');
        if ($question_pos>0) $url=substr($url, 0, $question_pos);

        $amp_pos=strrpos($url, '&');
        if ($amp_pos>0) $url=substr($url, 0, $amp_pos);

        $laurl = explode ('/', trim($url,'/'));
        if( !isset($laurl[0]) || $laurl[0]=='' || $laurl[0]=='controller.php') $laurl[0]='default';//HOME

        $_REQUEST['action']=$laurl[0];
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    function generate_url_params( $p_search_query=null
    , $p_fecha_ini=null
    , $p_fecha_fin=null
    , $p_order_by=null
    , $p_search_state=null
    , $p_relation_id=null
    , $p_parent_inst_id=null
    , $p_parent_class_id=null
    , $p_child_class_id=null
    , $p_tab=null) {
        $url='';

        $tmp_tab='';
        if ($p_tab<>null && $p_tab<>'') $tmp_child_class_id='&amp;p_tab='.$p_tab;
        $url=$tmp_tab.$url;

        $tmp_child_class_id='';
        if ($p_child_class_id<>null && $p_child_class_id<>'') $tmp_child_class_id='&amp;p_child_class_id='.$p_child_class_id;
        $url=$tmp_child_class_id.$url;

        $tmp_parent_class_id='';
        if ($p_parent_class_id<>null && $p_parent_class_id<>'') $tmp_parent_class_id='&amp;p_parent_class_id='.$p_parent_class_id;
        $url=$tmp_parent_class_id.$url;

        $tmp_parent_inst_id='';
        if ($p_parent_inst_id<>null && $p_parent_inst_id<>'') $tmp_parent_inst_id='&amp;p_parent_inst_id='.$p_parent_inst_id;
        $url=$tmp_parent_inst_id.$url;

        $tmp_inst_id='';
        if ($p_parent_inst_id<>null && $p_parent_inst_id<>'') $tmp_inst_id='&amp;p_inst_id='.$p_parent_inst_id;
        $url=$tmp_inst_id.$url;

        $tmp_relation_id='';
        if ($p_relation_id<>null && $p_relation_id<>'') $tmp_relation_id='&amp;p_relation_id='.$p_relation_id;
        $url=$tmp_relation_id.$url;

        $tmp_search_state='';
        if ($p_search_state<>null && $p_search_state<>'') $tmp_search_state='&amp;p_search_state='.$p_search_state;
        $url=$tmp_search_state.$url;

        $tmp_order_by='';
        if ($p_order_by<>null && $p_order_by<>'') $tmp_order_by='&amp;p_order_by='.$p_order_by;
        $url=$tmp_order_by.$url;

        $tmp_fecha_fin='';
        if ($p_fecha_fin<>null && $p_fecha_fin<>'') $tmp_fecha_fin='&amp;p_fecha_fin='.my_urlencode($p_fecha_fin);
        $url=$tmp_fecha_fin.$url;

        $tmp_fecha_ini='';

        if ($p_fecha_ini<>null && $p_fecha_ini<>'') $tmp_fecha_ini='&amp;p_fecha_ini='.my_urlencode($p_fecha_ini);
        $url=$tmp_fecha_ini.$url;

        $tmp_search_query='';
        if ($p_search_query<>null && $p_search_query<>'') $tmp_search_query='&amp;p_search_query='.urlencode($p_search_query);
        $url=$tmp_search_query.$url;

        return $url;
    }
}
