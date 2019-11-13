<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

if (!function_exists('_attributeName')) {
    function _attributeName($attribute)
    {
        if ($attribute['mandatory']=='Y') {
            $name = "atr_M";
        } else {
            $name = "atr_O";
        }

        $name .= $attribute['type'].'_'.$attribute['id'];

        if ($attribute['ordre_key']) {
            $name .= "_".$attribute['ordre_key'];
        }

        return $name;
    }
}

if (!function_exists('_selectedLookup')) {
    function _selectedLookup($attribute, $value)
    {
        if (isset($attribute['lookup_info']['selected_values'][0]) && $attribute['lookup_info']['selected_values'][0]['id']==$value) {
            $selected = true;
        } elseif ($attribute['lookup_info']['info']['default_id']==$value) {
            $selected = true;
        } else {
            $selected = false;
        }

        if ($selected) {
            return 'selected="selected"';
        } else {
            return '';
        }
    }
}

if (!function_exists('_videoembed')) {
    function _videoembed($video)
    {
        $arg_vid = explode(':', $video);
        $id_video = $arg_vid[1];

        switch ($arg_vid[0]) {
            case 'youtube':
                $embed = "https://www.youtube.com/embed/" . $id_video;
                break;
            case 'vimeo':
                $embed = "https://player.vimeo.com/video/" . $id_video . "?color=#E9AF2A&portrait=0";
                break;
            default:
                $embed = $video;
                break;
        }
        return $embed;
    }
}

if (!function_exists('_activeDate')) {
    function _activeDate($date_init, $date_end = null)
    {
        $date_init = str_replace('/', '-', $date_init);
        $init_timestamp = strtotime($date_init);
        $now = time();

        if ($init_timestamp>=$now) {
            return 'clr-danger';
        } else {
            if ($date_end!=null) {
                $date_end = str_replace('/', '-', $date_end);
                $end_timestamp = strtotime($date_end);
                if ($now>=$end_timestamp) {
                    return 'clr-danger';
                }
            }
        }

        return 'clr-default';
    }

}

if (!function_exists('_getFileSize')) {
    function _getLocalFileSize($file)
    {
        return filesize($file);
    }
    function _getRemoteFileSize($file)
    {
        $head = array_change_key_case(get_headers($file, 1));
        $size = isset($head['content-length']) ? $head['content-length'] : 0;
        return $size;
    }

    function _getFileSize($file)
    {
        $size = 0;

        if (!filter_var($file, FILTER_VALIDATE_URL)) {
            $size = _getLocalFileSize(public_path($file));
        } else {
            $size = _getRemoteFileSize($file);
        }
        
        if ($size > 0) {
                $size = (int)$size;
                $base = log($size) / log(1024);
                $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
                return round(pow(1024, $base - floor($base)), 2) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }
}



if (!function_exists('_fileExtension')) {
    function _fileExtension($file)
    {
        if (!filter_var($file, FILTER_VALIDATE_URL)) {
            $file = public_path($file);
            if (!file_exists($file)) {
                return '';
            }
        }
        
        $file_info = pathinfo($file);
        $extension = strtoupper($file_info['extension']);

        return $extension;
    }
}

if (!function_exists('_imageAutoResize')) {
    function _imageAutoResize($attribute)
    {
        $width = $height = null;
        $resize = '';

        if (isset($attribute['img_w']) && !empty($attribute['img_w'])) {
            $width = $attribute['img_w'];
        }
        if (isset($attribute['img_h']) && !empty($attribute['img_h'])) {
            $height = $attribute['img_h'];
        }

        if ($width!=null && $height==null) {
            $resize = "resizeWidth: ".$width.",";
        } elseif ($width==null && $height!=null) {
            $resize = "resizeHeight: ".$height.",";
        }

        return $resize;
    }
}

if (!function_exists('_isFavorited')) {
    function _isFavorited($item, $favorites=[])
    {

        if (array_key_exists($item, $favorites)){
            return 'favorited';
        }else{
            return '';
        }
    }
}

if (!function_exists('_instanceStatus')) {
    function _instanceStatus($item_status)
    {
        switch ($item_status){
            case "O":
                $status = '<span class="status-ball clr-published"></span>';
                break;
            case "V":
                $status = '<span class="status-ball clr-pending"></span>';
                break;
            default:
                $status = '<span class="status-ball clr-unpublished"></span>';
                break;
        }
        return $status;
    }
}

if (!function_exists('_attributeInfo')) {
    function _attributeInfo($id, $name, $type)
    {
        $info ='';
        if(Session::get('user_type')=='O' && Session::get('rol_id')==1 ) {
            $info ='<a class="clr-default" data-toggle="popover" data-placement="top" data-trigger="hover" data-content="<p>ID: '.$id.' - Name: '.$name.' - Type: '.$type.'</p>">
                        <i class="icon-information-outline"></i><span class="hide-txt">Info</span>
                    </a>';

            }
        return $info;
    }
}
