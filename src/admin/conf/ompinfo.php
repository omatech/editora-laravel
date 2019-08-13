<?php
    /**
       * @version 6
       * @copyright 2018, omatech
       **/
    define('ERROR_LEVEL', 7);
    define('HOST_URL', url('/'));
    define('ADMIN_URL', url(config('editora-admin.route.prefix')));
    define('DIR_APLI_ADMIN', __DIR__.'/../');
    define('DIR_APLI', DIR_APLI_ADMIN);
    define('PUBLIC_PATH', public_path());
    define("DOMAIN_SERVER", url('/'));
    define("IP_SERVER", DOMAIN_SERVER);
    define("URL_APLI", request()->url());
    define("UPLOAD_FOLDER_NAME", "uploads");
//    define("FRONT_END_URL", request()->getScheme().'://'.request()->getHost());
    define("FRONT_END_URL", url('/'));

    define("FRONT_END_UPLOAD_URL", '/'.UPLOAD_FOLDER_NAME.'/');
    define("FRONT_END_DIR", base_path());
    define("FRONT_END_UPLOAD_DIR", FRONT_END_DIR."/public/".UPLOAD_FOLDER_NAME."/");
    define("APP_BASE", FRONT_END_URL.'/'.config('editora-admin.route.prefix'));

    error_reporting(ERROR_LEVEL);
    //CLASSES ID
    define("CLASSHOME", 10);
    define("CLASSGLOBAL", 1);

    //INSTANCES ID
    define("HOMEID", 1);
    define("GLOBALID", 2);

/*
    $GLOBALS['special_classes'] array de clases en las que solo hay una instancia
    y en el menú no queremos que aparezca la opción de crar nuevo ni lista,
    simplemente irá a la instancia
*/
    $array_special_classes = config('editora.special_classes');
    if(isset($array_special_classes) && !empty($array_special_classes)){
        //[class_id=>instance_id]
        $GLOBALS['special_classes'] = $array_special_classes;
    }else{
        $GLOBALS['special_classes'] = array(CLASSHOME=>HOMEID, CLASSGLOBAL=>GLOBALID);
    }


    define("EDITORA_NAME", 'OMATECH');
    global $array_langs;
    $array_langs = config('editora.languages');


    //Flags
    define("INST_PERM", 1);
    define("USERINSTANCES", 300);
    define("SUPERROLID", 1);
    define("GD_LIB", true);
    define("MAIN_OBJECT", 1);
    define("MULTI_LANG", 1);

    define("CSSPREFIX", "csss/");
    define("IMGPREFIX", "/images/");
    define("DIR_UTILS", "utils/");
    define("DIR_UPLOADS", FRONT_END_UPLOAD_DIR);
    define("DIR_LANGS", "langs/");
    define("DIR_VIEWS", "views/");
    define("DIR_ACCIONS", "accions/");
    define("URL_UPLOADS", FRONT_END_UPLOAD_URL);
    define("URL_PREVIEW_PREFIX", "http://".IP_SERVER."/");
    define("URL_STYLES", "http://".IP_SERVER."/styles/");

    //Other formats
    define("STANDARD_DATE_FORMAT", "%d/%m/%Y %T");
    define("ROWS_PER_PAGE", 40);
    define("FIELD_DEFAULT_LENGTH", 40);
    define("TEXTAREA_DEFAULT_LENGTH", 39);
    define("TEXTAREA_CKEDITOR_DEFAULT_LENGTH", 120);
    define("TEXTAREA_DEFAULT_HEIGHT", 15);
    define("ICONO_PERM", APP_BASE.IMGPREFIX."user.gif");

    //Google Maps
    define("GOOGLE_MAPS_API_KEY", "");

    //admin login with hashed passwords
    define("HASHED_PASSWORDS", 1);
    define("INSTANCES_CACHE", false);//instances cache for old frontend editora
    define("ACTIVATE_CLONE", false);//activate clone instance for users
    define("ACTIVATE_RECURSIVE_CLONE", false);//activate recursive clone instance for users
