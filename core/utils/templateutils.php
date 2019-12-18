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


function tpl_get_text_tag_title(&$haystack, $clean = true){
    $match = '/\[TITLE\](.*)\[\/TITLE\](?:(\r\n|\n))/mi';
    $groups = NULL;
    preg_match_all($match, $haystack, $groups);

    if(!empty($groups)){
        
        //remove the title tag just ONCE

        if($clean){
            $haystack = preg_replace($match, '', $haystack,1);
            // remove the remaining paragraph  exists or is on HTML
            $haystack = preg_replace('/\<p\>\<\/p\>(?:(\r\n|\n))/mi', '', $haystack,1);
        }
        return $groups[1][0] == '' ? null : $groups[1][0];
    }
    
    return null;
}

function tpl_get_text_tag_author(&$haystack, $clean = true){
    $match = '/\[AUTHOR\](.*)\[\/AUTHOR\](?:(\r\n|\n))/mi';
    $groups = NULL;
    preg_match_all($match, $haystack, $groups);

    if(!empty($groups)){
        
        //remove the title tag just ONCE

        if($clean){
            $haystack = preg_replace($match, '', $haystack,1);
            // remove the remaining paragraph  exists or is on HTML
            $haystack = preg_replace('/\<p\>\<\/p\>(?:(\r\n|\n))/mi', '', $haystack,1);
        }
        return $groups[1][0] == '' ? null : $groups[1][0];
    }
    
    return null;
}


function tpl_get_text_tag_date(&$haystack, $clean = true){
    $match = '/\[DATE\](.*)\[\/DATE\](?:(\r\n|\n))/mi';
    $groups = NULL;
    preg_match_all($match, $haystack, $groups);

    if(!empty($groups)){
        
        //remove the title tag just ONCE
        if($clean){
            $haystack = preg_replace($match, '', $haystack,1);
            $haystack = preg_replace('/\<p\>\<\/p\>(?:(\r\n|\n))/mi', '', $haystack,1);
        }

       
        // remove the remaining paragraph  exists or is on HTML
        return $groups[1][0] == '' ? null : $groups[1][0];
    }
    
    return null;
}


function tpl_get_text_tag_introtext(&$haystack, $clean = true){
    $match = '/\[INTROTEXT\](.*)\[\/INTROTEXT\](?:(\r\n|\n))/mi';
    $groups = NULL;
    preg_match_all($match, $haystack, $groups);

    if(!empty($groups)){
        
        //remove the title tag just ONCE

        if($clean){
            $haystack = preg_replace($match, '', $haystack,1);
            // remove the remaining paragraph  exists or is on HTML
            $haystack = preg_replace('/\<p\>\<\/p\>(?:(\r\n|\n))/mi', '', $haystack,1);
        }
        return $groups[1][0] == '' ? null : $groups[1][0];
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
    $post_date = tpl_get_text_tag_date($draft);


    // END////////////////////////////////////////////////////////

    // convert the markup to HTML
    $draft = parse_draft_to_html($draft);


    // load the template
    $header = tpl_load_template_var("news/header.php");
    $footer = tpl_load_template_var("news/footer.php");
    $body   = tpl_load_template_var("news/body.php");
    // end load of template

    //parse the markup to html


    // end markup

    // extract the title from the draft
    //then remove from HTML



    //overwrite static variables in the template values
    
    tpl_overwrite_variable($header, "TITLE", $config['TITLE']);
    tpl_overwrite_variable($body, "POST", $draft);

    if($title){
        tpl_overwrite_variable($draft, 'NEWS_TITLE', $title);
        tpl_overwrite_variable($header, 'NEWS_TITLE', $title);
        tpl_overwrite_variable($body, 'NEWS_TITLE', $title);
    }
    
    if(!$author){
        printf("Author Not Found\n\n");
    }else{
        printf("author: %s\n\n", $author);
        tpl_overwrite_variable($body, "AUTHOR", $author);
    }

    if($post_date){
        $dt = DateTime::createFromFormat("d/m/Y", $post_date, new DateTimeZone($config['TIMEZONE']));
        tpl_overwrite_variable($body, 'DATE', $dt->format("d/m/Y H:i:s"));
        printf("%s - Post Date", $dt->format("d/m/Y H:i:s"));
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


function tpl_create_post_links($root){
    global $config;
    global $blog_conf;
     

    $post_links = tpl_load_template_var("posts_links.php");
    $news = null;
    load_news_tracking($root, $news);

    $out_dir =  $root . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'links.html';

    $out = fopen($out_dir,"wb");

  
    foreach($news['posts'] as $post){
        $tmp_link = $post_links;
        tpl_overwrite_variable($tmp_link, 'LINK', $config['URI'] . $post['id']);
        tpl_overwrite_variable($tmp_link, 'ANCHOR_LINK', $post['title']);
        fwrite($out, $tmp_link);
        unset($tmp_link);
    }

    printf("\n\nlinks generated to links.html\n");

    fclose($out);
    
}


function tpl_create_site($root){
    global $blog_conf;

    $news = null;
    load_news_tracking($root, $news);


        // load the template
    $header = tpl_load_template_var("header.php");
    $footer = tpl_load_template_var("footer.php");
    $body   = tpl_load_template_var("body.php");



}

function  tpl_generate_index($root)
{
    $news_tpl = tpl_load_template_var("news_item.php");

    $header = tpl_load_template_var("header.php");
    $footer = tpl_load_template_var("footer.php");
    //$body   = tpl_load_template_var("body.php");

    $news = null;
    load_news_tracking($root, $news);

    $out_dir =  $root . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.html';

    $out = fopen($out_dir,"wb");

    tpl_overwrite_variable($header, 'TITLE', 'Página Inicial');

    fwrite($out, $header);

    foreach($news['posts'] as $post){
        $tmp = $news_tpl;
        $intro = tpl_get_text_tag_introtext($tmp);

        if($intro){
            tpl_overwrite_variable($tmp, 'INTROTEXT', $intro);
        }

        if($post['title'] != ''){
            tpl_overwrite_variable($tmp, 'TITLE', $post['title']);
        }

        fwrite($out, $tmp);
    }
    fwrite($out, $footer);
    fclose($out);
}