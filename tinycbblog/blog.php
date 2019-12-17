#!/usr/bin/php
<?php 

define('ROOT_PATH', dirname(realpath(__DIR__)));



// allow invoking by CLI only
if(http_response_code()){
    exit;  //  do not how anything as error just let a blank page as error
}

require ROOT_PATH . DIRECTORY_SEPARATOR . 'core/bootstrap.php'; 

$opt = getopt("d:au");

global $config;

chdir("../");
define('APPLICATION_DIR', getcwd());


function usage(){
    echo "TINY BLOG CONTROL PANEL\n";
    echo "\tblog.php <d:a>\n";
    echo "\tblog.php -d <draft name> - Create a new Draft to Post\n";
    echo "\tblog.php -a - process all drafts and update all\n";
    echo "\tblog.php -u - update the news tracker \n";
    echo "\n"; 
}

try {

    if(!$opt){
        usage();
        exit;
    }

    if(array_key_exists('a', $opt)){
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
        exit;
         

       
    }

}catch(Exception $ex){
    echo "Exception Caught: " . $ex->getMessage();
}