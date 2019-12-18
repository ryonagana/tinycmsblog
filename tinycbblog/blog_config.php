<?php

$blog_conf = array();


$blog_conf['MAX_NEWS_PER_PAGE'] = 5;
$blog_conf['SHOW_PAGINATION'] = true;
$blog_conf['TOTAL_POSTS'] = 0;


load_news_tracking(APPLICATION_DIR, $blog_conf['TOTAL_POSTS']);
$blog_conf['TOTAL_POSTS'] = count($blog_conf['TOTAL_POSTS']['posts']);


$blog_conf['PAGE'] = 1;
$blog_conf['NEWS_REMAINING'] = $blog_conf['TOTAL_POSTS'] - ( $blog_conf['PAGE'] * $blog_conf['MAX_NEWS_PER_PAGE']);


