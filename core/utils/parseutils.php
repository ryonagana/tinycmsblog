<?php



function parse_load_draft($draft_name)
{
  
    global $config;
 
    $fp = fopen($draft_name, "rb");
    $result = stream_get_contents($fp,-1);
    fclose($fp);
    return $result;
    
}

function parse_draft_to_html($content){
    global $config;
    return $config['PARSER']->text($content);
}


