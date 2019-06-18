<?php
//à
require_once(APP_BASE.'/utils/appslib/googleapi/core/playStoreApi.php');
class Appscrapper
{
        public function loaditunesData($url = null)
        {
                   $pattern = '/id+[0-9]+/';
                  preg_match($pattern,$url, $id_app);
                  $id_app=str_replace('id','',$id_app);
                  $content=file_get_contents('http://itunes.apple.com/lookup?id='.$id_app[0].'&term=UTF-8&lang=es_ES');
                  $content = json_decode($content,true);
                  $array = $content;

                  if(!empty($array['results'][0])):
                        return json_encode($array['results'][0]);
                  else:
                      return FALSE;
                  endif;
        }

        public function loadgoogleplayData($url = null)
        {
                $class_init = new PlayStoreApi();

                $parser_out=parse_url($url);
                parse_str($parser_out['query'], $result);
                $item_id =$result['id'];

                $itemInfo =$class_init->itemInfo($item_id);

                //if($itemInfo !== 0 )
                if(!empty($itemInfo['General'][0]->app_title))
                {
                    //Elimino todos los caracteres html del texto. antes de mostralos
                    $swap=strip_tags($itemInfo['General'][0]->html_app_description);
                    $itemInfo['General'][0]->html_app_description=$swap;

                    $data=json_encode($itemInfo);
                }

                return $data;
        }

}
