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


function tpl_get_text_tag_title(&$haystack){
    $match = '/\[TITLE\](.*)\[\/TITLE\](?:(\r\n|\n))/mi';
    $groups = NULL;
    preg_match_all($match, $haystack, $groups);

    if(!empty($groups)){
        
        //remove the title tag just ONCE
        $haystack = preg_replace($match, '', $haystack,1);
        // remove the remaining paragraph  exists or is on HTML
        $haystack = preg_replace('/\<p\>\<\/p\>(?:(\r\n|\n))/mi', '', $haystack,1);
        return $groups[1][0];
    }
    
    return $groups;
}

function tpl_get_text_tag_author(&$haystack){
    $match = '/\[AUTHOR\](.*)\[\/AUTHOR\](?:(\r\n|\n))/mi';
    $groups = NULL;
    preg_match_all($match, $haystack, $groups);

    if(!empty($groups)){
        
        //remove the title tag just ONCE
        $haystack = preg_replace($match, '', $haystack,1);
        // remove the remaining paragraph  exists or is on HTML
        $haystack = preg_replace('/\<p\>\<\/p\>(?:(\r\n|\n))/mi', '', $haystack,1);
        return $groups[1][0];
    }
    
    return null;
}


function tpl_generate_page($draft_path){
    global $config;

    if(!file_exists($draft_path)){
        show_error(CRITICAL, sprintf("Invalid DRAFT Name %s", $draft_path));
        exit;
    }

    //load  the draft file
    $draft = parse_load_draft($draft_path);

    //DO EVERYTHING BEFORE PARSING //////////////////////////////////
    $title = tpl_get_text_tag_title($draft); // extract title;
    $author = tpl_get_text_tag_author($draft);

   


    // END////////////////////////////////////////////////////////

    $draft = parse_draft_to_html($draft);

    // load the template
    $header = tpl_load_template_var("header.php");
  


    $footer = tpl_load_template_var("footer.php");
    $body   = tpl_load_template_var("body.php");
    // end load of template

    //parse the markup to html


    // end markup

    // extract the title from the draft
    //then remove from HTML



    //overwrite static variables in the template values
    tpl_overwrite_variable($header, 'NEWS_TITLE', $title);
    tpl_overwrite_variable($header, "TITLE", $config['TITLE']);
    tpl_overwrite_variable($body, "POST", $draft);
    
    if(!$author){
        printf("Author Not Found\n\n");
    }else{
        printf("author: %s\n\n", $author);
        tpl_overwrite_variable($body, "AUTHOR", $author);
    }


    //write the template
    $out_dir = getcwd() . DIRECTORY_SEPARATOR . 'posts' . DIRECTORY_SEPARATOR . explode('.',basename($draft_path))[0] . ".src.html";

    $fp = fopen($out_dir, "wb");

    fwrite($fp, $header);
    fwrite($fp, $body);
    fwrite($fp, $footer);

    fclose($fp);
    

}

function tpl_bulk_generate_pages($root, $posts){
    
    $dir = $root . DIRECTORY_SEPARATOR . 'drafts';
    
    foreach($posts as $p){
        $id   = $p['id'];
        $name = $p['draft'];
        tpl_generate_page( $dir . DIRECTORY_SEPARATOR . $name);
        printf("Processed: %s..\n\n", $name);
    }
}