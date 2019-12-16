<?php
require "core/bootstrap.php";

if($config['TIMEZONE'] == NULL || empty($config['TIMEZONE'])){
    date_default_timezone_set('America/New_York');
}else {
    date_default_timezone_set($config['TIMEZONE']);
}
//var_dump($config['DRAFTS_FOLDER']);
//$tpl = tpl_load_template_var("header.php");
//$tpl = tpl_overwrite_variable($tpl, 'TITLE', 'Teste');


//tpl_generate_page("1-example.txt");


?>