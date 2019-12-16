<?php 


$config = array();

//CUSTOMIZABLE OPTIONS
$config['TITLE'] = "My Example Title MuthaFucka!!!";
$config['THEME'] = 'default';
$config['CUSTOM_THEME']  = '';
$config['SUBFOLDER'] = 'tinycbblog';
$config['URI'] = '10.0.3.239/tinycbblog/';
$config['TIMEZONE'] = 'America/Sao_Paulo';



// DO NOT EDIT THESE UNLESS YOU KNOW WHAT  R U DOING!
$config['ROOT'] = get_root_path() . DIRECTORY_SEPARATOR . $config['SUBFOLDER'];
$config['DRAFTS_FOLDER'] = create_valid_paths('drafts');
$config['POSTS_FOLDER'] = create_valid_paths('posts');
$config['CORE_FOLDER'] = create_valid_paths('core');
$config['TEMPLATE_FOLDER'] = create_valid_paths('tpl');
$config['THEME_FOLDER'] = create_valid_paths('tpl/' . $config['THEME']);
$config['PARSER'] = new Parsedown();




