<?php


function tpl_load_template_var($tpl_file){
    global $config;

  
    $dir  = join(DIRECTORY_SEPARATOR, array($config['TEMPLATE_FOLDER'], $config['THEME'],  $tpl_file));
    
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


function tpl_generate_page($draft_name){
    global $config;

    $draft_path = $config['DRAFTS_FOLDER'] . DIRECTORY_SEPARATOR . $draft_name;


    if(!file_exists($draft_path)){
        exit('invalid draft name');
    }

    $header = tpl_load_template_var("header.php");
    $footer = tpl_load_template_var("footer.php");

    $body =    tpl_load_template_var("body.php");
    

    $draft = parse_load_draft($draft_path);
    $draft = parse_draft_to_html($draft);
    tpl_overwrite_variable($header, "TITLE", $config['TITLE']);
    tpl_overwrite_variable($body, "POSTS", $draft);

    $fp = fopen($config['POSTS_FOLDER'] . DIRECTORY_SEPARATOR . $draft_name . ".src.html", "wb");

    fwrite($fp, $header);
    fwrite($fp, $body);
    fwrite($fp, $footer);

    fclose($fp);

}