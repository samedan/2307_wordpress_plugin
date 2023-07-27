<?php 

/*
  Plugin Name: Out Test Plugin
  Description: A truly amazing plugin
  Version: 1.0
  Author: Dan
  Author URI: https://articole-smart.eu
*/

add_filter('the_content', 'addToEndOfPost');

function addToEndOfPost($content) {
  if(is_single() && is_main_query()) { // just seeing the single post, not without other posts or sidebars
    return $content . '<p>My Name is Dan.</p>'; 
  } return $content;
  
}
