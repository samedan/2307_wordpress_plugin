<?php 

/*
  Plugin Name: Out Test Plugin
  Description: A truly amazing plugin
  Version: 1.0
  Author: Dan
  Author URI: https://articole-smart.eu
*/

class WordCountAndTimePlugin {
  // CONSTRUCTOR 
  function __construct(){ // function when calling a class
     add_action('admin_menu', array(
        $this, // this class
        'adminPage'
    ));
  }
  // END CONSTRUCTOR 


  function adminPage() { 
   add_options_page(
    'Word Count Settings',
    'Word Count', // the title in the settings menu (on the page menu)
    'manage_options', //for admins
    'word-count-settings-page', // slug name (url after /wpadmin)
    array(
     $this,
     'ourHTML'
    )
     );
   }

   function ourHTML () {
     ?>
       <div class="wrap">
        <h1>Word Count Settings</h1>
       </div>
     <?php
   }
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();





