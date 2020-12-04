<?php

function autoLoadQueryBuilder($className) {
    $folders = array_filter(glob(dirname(__FILE__)."/*"), "is_dir");
    foreach($folders as $folder) {
        $path = $folder . "/" . $className . ".php";
        if(file_exists($path)) {
            include_once $path;
            return true;
        }
    }

    $fileName = $className . ".php";
    if(!file_exists($fileName)) {
        echo "nooooo class found";
        return false;
    }

    include_once $fileName;
    return true;
}

spl_autoload_register("autoLoadQueryBuilder");