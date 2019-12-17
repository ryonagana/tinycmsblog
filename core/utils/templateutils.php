<?php


function tpl_load_template_var($tpl_file){
    global $config;

  
    $dir  = dirname($config['ROOT']) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . $config['THEME'] . DIRECTORY_SEPARATOR . $tpl_file;


    if(!file_exists($dir)){
        throw new Exception($dir . " Not Found");
    }
    
    $tpl = file_get_contents($dir, true);
    return $tpl;
}


function tpl_overwrite_variable(&$tpl, $variable, $data){
    $re = preg_replace("/(\{{1})(\%{1})".$variable."(\%{1})(\}{1})/m", $data,$tpl);
    $tpl = $re;
    return $tpl;    
}


function tpl_get_text_tag_title($haystack, $tag){
    $match = '\[TITLE\](.*)\[\/TITLE\]';
    $groups = NULL;
    preg_match_all($match, $haystack, $groups);
    return $groups;
}


function tpl_generate_page($draft_path){
    global $config;

    if(!file_exists($draft_path)){
        show_error(CRITICAL, sprintf("Invalid DRAFT Name %s", $draft_path));
        exit;
    }

    $header = tpl_load_template_var("header.php");
    $footer = tpl_load_template_var("footer.php");
    $body   = tpl_load_template_var("body.php");

    $draft = parse_load_draft($draft_path);
    $draft = parse_draft_to_html($draft);
    tpl_overwrite_variable($header, "TITLE", $config['TITLE']);
    tpl_overwrite_variable($body, "POSTS", $draft);



    $out_dir = getcwd() . DIRECTORY_SEPARATOR . 'posts' . DIRECTORY_SEPARATOR . explode('.',basename($draft_path))[0] . ".src.html";

    $fp = fopen($out_dir, "wb");

    fwrite($fp, $header);
    fwrite($fp, $body);
    fwrite($fp, $footer);

    fclose($fp);
    

}