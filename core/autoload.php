<?php 

spl_autoload_register(function($class){
    if (file_exists(dirname(__DIR__)."/".$class.".php")){
        require_once dirname(__DIR__)."/".$class.".php";
        return;
    }

    if (file_exists(dirname(__DIR__)."/core/".$class.".php")){
        require_once dirname(__DIR__)."/core/".$class.".php";
        return;
    }

    if (file_exists(dirname(__DIR__)."/src/models/".$class.".php")){
        require_once dirname(__DIR__)."/src/models/".$class.".php";
        return;
    }

});
