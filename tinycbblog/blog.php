#!/usr/bin/php
<?php 

define('ROOT_PATH', dirname(realpath(__DIR__)));



// allow invoking by CLI only
if(http_response_code()){
    exit;  //  do not how anything as error just let a blank page as error
}

require ROOT_PATH . DIRECTORY_SEPARATOR . 'core/bootstrap.php'; 

$opt = getopt("d:augt");

global $config;

chdir("../");
define('APPLICATION_DIR', getcwd());

$os = php_uname("s");


require_once APPLICATION_DIR . DIRECTORY_SEPARATOR . 'tinycbblog' . DIRECTORY_SEPARATOR . 'blog_config.php';

function copyfile($dst,$src, $verbose = false)
{
    global $os;

    $verbose = "-v";
    $ret = null;

    if($os == "Linux"){
        system(sprintf("cp -r %s  %s %s", $verbose, $src, $dst), $ret);
    }
    else if($os == "Windows") {
        system(sprintf("copy %s %s", $src, $dst), $ret);
    }else {
        copy($src,$dst);
    }

    return $ret;
}

// trying to make usabe in all OS
function delete_dir($dir, $filter = "*.html"){

    foreach(glob($dir . DIRECTORY_SEPARATOR . $filter) as $f){
        printf("\n->delete %s\n\n", $f);
        unlink($f);
    }

    if(is_dir($dir)){
        rmdir($dir);
    }else {
        printf("\nFolder not Found\n\n");
    }
}


function usage(){
    echo "TINY BLOG CONTROL PANEL\n";
    echo "\tblog.php <d:augt>\n";
    echo "\tblog.php -d <draft name> - Create a new Draft to Post\n";
    echo "\tblog.php -a - process all drafts and update all\n";
    echo "\tblog.php -u - update the news tracker \n";
    echo "\tblog.php -g - generate site layout \n";
    echo "\n"; 
}

try {

    if(!$opt){
        usage();
        exit;
    }

    if(array_key_exists('a', $opt)){

        if(!is_dir(APPLICATION_DIR . DIRECTORY_SEPARATOR . 'posts')){
            mkdir(APPLICATION_DIR . DIRECTORY_SEPARATOR . 'posts');
        }

        force_generate_news_tracking(APPLICATION_DIR);
        generate_news_tracking(APPLICATION_DIR);
        load_news_tracking(APPLICATION_DIR, $config['POSTS']);
        
        tpl_bulk_generate_pages(APPLICATION_DIR, $config['POSTS']['posts']);

        
    }

    if(array_key_exists('u', $opt)){
 
        force_generate_news_tracking(APPLICATION_DIR);
      
        printf("\n\nGenerating the news tracker only\n\n");
    }

    if(array_key_exists('d', $opt)){
        $file = $opt['d'];
        $draft_path = get_path(APPLICATION_DIR, array('drafts', $file));
        strip_filename($file);

        $draft = check_news_exists_by_id(APPLICATION_DIR, (int)$file);

       
        if(!$draft){
            if(!file_exists($draft_path)){
                show_error(CRITICAL,sprintf("File [%s] doesnt exists\n\n",$draft_path));
                exit;
            }
    
            $name = explode('.', $file);
            tpl_generate_page($draft_path);
            printf("\nCREATED: %s\n\n", $name[0] . '.src.html');
        }

        $draft_path = get_path(APPLICATION_DIR, array('drafts', $draft['draft']));
        $name = explode('.', $file);
        tpl_generate_page($draft_path);


        //load_news_tracking(APPLICATION_DIR, $config['POSTS']);
        //var_dump($config['POSTS']);

       
    }

    if(array_key_exists('g', $opt)){

        $dir =  APPLICATION_DIR . DIRECTORY_SEPARATOR . 'public';
        //var_dump($dir, APPLICATION_DIR . DIRECTORY_SEPARATOR . 'posts');
        delete_dir(APPLICATION_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'posts');
        $ret = copyfile($dir, APPLICATION_DIR . DIRECTORY_SEPARATOR . 'posts');
        tpl_create_post_links(APPLICATION_DIR);
        //printf("\n%s\n\n", $ret);

    }

    if(array_key_exists('t', $opt)){
        $n =  null;
        read_all_news(APPLICATION_DIR,$n);

    }


}catch(Exception $ex){
    echo "Exception Caught: " . $ex->getMessage();
}