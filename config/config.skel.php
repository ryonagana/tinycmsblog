<?php 


$config = array();

$cli_mode = !http_response_code();

//CUSTOMIZABLE OPTIONS
$config['TITLE'] = "My Example Title MuthaFucka!!!";
$config['THEME'] = 'default';
$config['CUSTOM_THEME']  = '';
$config['SUBFOLDER'] = 'tinycbblog';
$config['URI'] = '10.0.3.239/tinycbblog/';
$config['TIMEZONE'] = 'America/Sao_Paulo';



// DO NOT EDIT THESE UNLESS YOU KNOW WHAT  R U DOING!
$config['ROOT'] =  dirname(realpath(__DIR__)) . DIRECTORY_SEPARATOR . $config['SUBFOLDER'];
$config['DRAFTS_FOLDER'] = create_valid_paths('drafts',$cli_mode);
$config['POSTS_FOLDER'] = create_valid_paths('posts',$cli_mode);
$config['CORE_FOLDER'] = create_valid_paths('core', $cli_mode);
$config['TEMPLATE_FOLDER'] = create_valid_paths('tpl', $cli_mode);
$config['THEME_FOLDER'] = create_valid_paths('tpl/' . $config['THEME'], $cli_mode);
$config['PARSER'] = new Parsedown();




