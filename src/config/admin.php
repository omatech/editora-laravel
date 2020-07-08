<?php


/**
 * 
 *     $array_special_classes = config('editora.special_classes',
    if(isset($array_special_classes) && !empty($array_special_classes)){
        //[class_id=>instance_id]
        $GLOBALS['special_classes'] = $array_special_classes,
    }else{
        $GLOBALS['special_classes'] = array(CLASSHOME=>HOMEID=> CLASSGLOBAL=>GLOBALID,
    }

       global $array_langs,
    $array_langs = config('editora.languages',
 */

return [
    'ERROR_LEVEL'=> 7,
    'HOST_URL'=> url('/'),
    'ADMIN_URL'=> url(config('editora-admin.route.prefix')),
    'DIR_APLI_ADMIN'=> __DIR__.'/../',
    'DIR_APLI'=> __DIR__.'/../',
    'PUBLIC_PATH'=> public_path(),
    "DOMAIN_SERVER"=> url('/'),
    "IP_SERVER"=> url('/'),
    "URL_APLI"=> request()->url(),
    "UPLOAD_FOLDER_NAME"=> "uploads",
    "FRONT_END_URL"=> url('/'),

    "FRONT_END_UPLOAD_URL"=> '/uploads/',
    "FRONT_END_DIR"=> base_path(),
    "FRONT_END_UPLOAD_DIR"=> base_path()."/public/uploads/",
    "APP_BASE"=> url('/').'/'.config('editora-admin.route.prefix'),

    //CLASSES ID
    "CLASSHOME"=> 10,
    "CLASSGLOBAL"=> 1,

    //INSTANCES ID
    "HOMEID"=> 1,
    "GLOBALID"=> 2,

/*
    $GLOBALS['special_classes'] array de clases en las que solo hay una instancia
    y en el menú no queremos que aparezca la opción de crar nuevo ni lista=>
    simplemente irá a la instancia
*/


    "EDITORA_NAME"=> 'OMATECH',
 


    //Flags
    "INST_PERM"=> 1,
    "USERINSTANCES"=> 300,
    "SUPERROLID"=> 1,
    "GD_LIB"=> true,
    "MAIN_OBJECT"=> 1,
    "MULTI_LANG"=> 1,

    "CSSPREFIX"=> "csss/",
    "IMGPREFIX"=> "/images/",
    "DIR_UTILS"=> "utils/",
    "DIR_UPLOADS"=> base_path()."/public/uploads/",
    "DIR_LANGS"=> "langs/",
    "DIR_VIEWS"=> "views/",
    "DIR_ACCIONS"=> "accions/",
    "URL_UPLOADS"=> '/uploads/',
    "URL_PREVIEW_PREFIX"=> "http://".url('/')."/",
    "URL_STYLES"=> "http://".url('/')."/styles/",

    //Other formats
    "STANDARD_DATE_FORMAT"=> "%d/%m/%Y %T",
    "ROWS_PER_PAGE"=> 40,
    "FIELD_DEFAULT_LENGTH"=> 40,
    "TEXTAREA_DEFAULT_LENGTH"=> 39,
    "TEXTAREA_CKEDITOR_DEFAULT_LENGTH"=> 120,
    "TEXTAREA_DEFAULT_HEIGHT"=> 15,
    "ICONO_PERM"=> url('/').'/'.config('editora-admin.route.prefix').'/images/'."user.gif",

    //Google Maps
    "GOOGLE_MAPS_API_KEY"=> "",

    //admin login with hashed passwords
    "HASHED_PASSWORDS"=> 1,
    "INSTANCES_CACHE"=> false,//instances cache for old frontend editora
    "ACTIVATE_CLONE"=> false,//activate clone instance for users
    "ACTIVATE_RECURSIVE_CLONE"=> false,//activate recursive clone instance for users
];