<?php


function create_valid_paths($dst, $cli = false){
    global $config;

    if($cli){
 
        $root =  dirname(realpath(__DIR__)) . DIRECTORY_SEPARATOR . $config['SUBFOLDER'];
        return join(DIRECTORY_SEPARATOR, array($root, $dst));
    }
    
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


function get_root_path($cli = false){

    if($cli){
        return dirname(realpath(__DIR__));
    }

    if(!array_key_exists('DOCUMENT_ROOT', $_SERVER)){
        return dirname(realpath(__DIR__));
    }

    return $_SERVER['DOCUMENT_ROOT'];
}

function get_path($root, $p){

    return  $root . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $p);
}


define('LOG',1);
define('WARNING',2);
define('CRITICAL',3);


function show_error($type, $msg){
    switch($type){
        case LOG:
            printf("\n===============\n");
            printf("\nLOG: %s\n", $msg);
            printf("===============\n");
        break;
        case WARNING:
            printf("\n===============\n");
            echo sprintf("\nWARNING: %s\n", $msg);
            printf("===============\n");
        break;
        case CRITICAL:
            printf("===============\n");
            printf("\nCRITICAL: %s\n", $msg);
            printf("===============\n");
        break;
    }
}

function strip_filename(&$path)
{
    $path = trim($path);
    $$path = preg_replace('/\s+/', '', $path);
}

function read_all_news($root, &$result){
    $path = $root . DIRECTORY_SEPARATOR . 'drafts';

    foreach( glob($path .  DIRECTORY_SEPARATOR . '*') as $f){
        //printf("\n%s\n", $f);
        $id = (int) explode('-',basename($f))[0];

        //tpl_get_text_tag_title()

        $draft_data = file_get_contents($f);
        $title = tpl_get_text_tag_title($draft_data, false);
        $dt = tpl_get_text_tag_date($draft_data, false);


        $result['posts'][$id] = array(
                                    'id' => (int) explode('-',basename($f))[0],
                                     'draft' => basename($f),
                                     'date' => $dt,
                                     'title' => $title
                                    
                                    );
    }

}

function check_news_exists_by_id($root,$id){
    $news = null;
    read_all_news($root, $news);

    foreach($news['posts'] as $n){
        
        if ((int)$n['id'] === $id) return $n;
        continue;
    }
    return null;
}

function force_generate_news_tracking($root){
    $fullpath = $root . DIRECTORY_SEPARATOR . 'tinycbblog' . DIRECTORY_SEPARATOR . 'tracking.json';

    $news  = null;
    if(file_exists($fullpath)){
        read_all_news($root, $news);

        $json = json_encode($news, JSON_PRETTY_PRINT);
        file_put_contents($fullpath, $json);
        load_news_tracking($root, $news);
        return true; 
    }

    return false;
}

function generate_news_tracking($root){
    
    $fullpath = $root . DIRECTORY_SEPARATOR . 'tinycbblog' . DIRECTORY_SEPARATOR . 'tracking.json';

    $news  = null;
    read_all_news($root, $news);

    if(!file_exists($fullpath)){
        $json = json_encode($news, JSON_PRETTY_PRINT);
        file_put_contents($fullpath, $json);
        load_news_tracking($root, $news);
    }

    load_news_tracking($root, $news );
}

function load_news_tracking($root, &$tracking){
    $fullpath = $root . DIRECTORY_SEPARATOR . 'tinycbblog' . DIRECTORY_SEPARATOR . 'tracking.json';
    $data = null;

    if(file_exists($fullpath)){
        $size = filesize($fullpath);
        $fp = fopen($fullpath, "rb");
        $data = fread($fp, $size);
        fclose($fp);

        $tracking = json_decode($data, true);
        return true;
    }

    return false;
}
