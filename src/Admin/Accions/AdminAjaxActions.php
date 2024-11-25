<?php

namespace Omatech\Editora\Admin\Accions;

use Omatech\Editora\Admin\Events\AdminRelationSortEvent;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Session;
use Omatech\Editora\Admin\Models\Security;
use Omatech\Editora\Admin\Models\Instances;
use Omatech\Editora\Admin\Models\Relations;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Event;

class AdminAjaxActions extends AuthController
{
    public function render()
    {
        $security = new Security;
        $params=get_params_info();

        if(Session::has('rol_id')) {
            $is_ajax=true;
            $sended = false;
            if(isset($_REQUEST['ajax'])) {
                if($_REQUEST['ajax'] == 'ajax_order') {
                    $r=new relations();

                    $values = array();
                    $ordered = explode(',', $_REQUEST['ordered']);
                    foreach($ordered as $value) {
                        $value = str_ireplace('id_', '', $value);
                        array_push($values, $value);
                    }
                    if ($r->order_relation($_REQUEST['instance_id'], $values)) {
                        $sended = true;
                    }
                    if ($sended) {
                        $this->dispatchEvent($_REQUEST['instance_id']);
                        echo getMessage('saved');
                    }else {
                        echo getMessage('saved_wrong');
                    }
                }elseif($_REQUEST['ajax'] == 'refresh_view'){
                    
                    if (config('editora-admin.curl-refresh-command')!='') {
                        $curl_command = config('editora-admin.curl-refresh-command');

                        $inst_id = $_REQUEST['inst_id'];

                        $array_langs = config('editora-admin.languages');
                        $instances = new Instances;
                        foreach($array_langs as $language){
                            $lanice= $instances->get_niceurl($inst_id, $language);
                            $niceurl = '/'.$language.'/'.$lanice;

                            $process = new Process($curl_command.$niceurl);
                            $process->run();
                            if (!$process->isSuccessful()) {
                                throw new ProcessFailedException($process);
                            }
                            echo $process->getOutput();
                        }
                        
                        
                    
                    }
                }
            }

            
        }

       die();
    }

    private function dispatchEvent(int $instanceId): void
    {
        try {
            if ($instanceId !== 0) {
                Event::dispatch(new AdminRelationSortEvent($instanceId));
            }
        } catch (\Exception $exception) {}
    }
}
