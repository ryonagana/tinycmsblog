<?php


function create_valid_paths($dst){
    global $config;

    $root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $config['SUBFOLDER'];

    $dir = join(DIRECTORY_SEPARATOR, array($root, $dst));
    return  is_dir($dir) ? $dir : null;
}

function get_date_to_epoch($epoch)
{
    return gmdate('r',$epoch);
}

function get_epoch_to_date($date){
    $date = new DateTime($date); // format: MM/DD/YYYY
    return $date->format('U');
}


function get_root_path(){
    if(!array_key_exists('DOCUMENT_ROOT', $_SERVER)){
        return dirname(realpath(__DIR__));
    }

    return $_SERVER['DOCUMENT_ROOT'];
}
