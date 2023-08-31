<?php 

/*
  Plugin Name: Out Test Plugin
  Description: A truly amazing plugin
  Version: 1.0
  Author: Dan
  Author URI: https://articole-smart.eu
  Text Domain: wcpdomain
  Domain Path: /languages
*/

class WordCountAndTimePlugin {

  // CONSTRUCTOR 
  function __construct(){ // function when calling a class
    add_action('admin_menu', array($this, 'adminPage'));
    add_action('admin_init', array($this, 'settings'));
    add_filter('the_content', array($this, 'ifWrap')); // Filter if WordCount is needed
    add_action('init', array($this, 'languages'));
  }
  // END CONSTRUCTOR 

  // LANGUAGES
  function languages() {
    load_plugin_textdomain('wcpdomain', false, dirname(plugin_basename(__FILE__)).'/languages');
  }

  // Filter if WordCount is needed
  function ifWrap($content) {
    if(is_main_query() AND is_single() AND // if post type
      (get_option('wcp_wordcount', '1') // wordcount is checked
        OR get_option('wcp_charactercount', '1')
        OR get_option('wcp_readtime', '1')
      )) {
        return $this->createHTML($content);
    }
    return $content;
  }

  function createHTML($content) {
    $html = '<h3>'. esc_html(get_option('wcp_headline', 'Post Statistics')).'</h3><p>';

    // Calculate WordCount
    if(get_option('wcp_wordcount', '1') OR get_option('wcp_readtime', '1')) {
      $wordCount = str_word_count(strip_tags($content));
    }

    if(get_option('wcp_wordcount', '1') ) {
      $html .=  esc_html__('This post has', 'wcpdomain'). ' '.$wordCount. ' '.__('words', 'wcpdomain').'.<br>';
    }
    if(get_option('wcp_charactercount', '1') ) {
      $html .= 'This post has '.strlen(strip_tags($content)). ' characters.<br>';
    }
    if(get_option('wcp_readtime', '1') ) {
      $html .= 'This post will take '.round($wordCount/225). ' minute(s) to read.<br>';
    }
    $html .= '</p>';
    
    // add to the content of post
    if(get_option('wcp_location', '0') == '0') {
       // add to the begginning of post
       return $html .$content;
    } return $content.$html;

  }

  function settings() {
    add_settings_section(
      'wcp_first_section',
      null, //subtitle
      null, // content describing the value
      'word-count-settings-page', //slug
    );

    // First input SELECT //
    add_settings_field( // how the field is displayed on the backend
       'wcp_location', 
       'Display Location', // label
       array($this, 'locationHTML'), // the HTML for the input type
       'word-count-settings-page', //slug
       'wcp_first_section' // the Section where the field is added
     ); // HTML input field
    register_setting(
       'wordcountplugin',
       'wcp_location',
       array(
         'sanitize_callback' => array($this, 'sanitizeLocation'),
         'default' => '0'
       )
      );

      // Second input INPUT //
     add_settings_field( // how the field is displayed on the backend
       'wcp_headline', 
       'Headline Text', // label
       array($this, 'headlineHTML'), // the HTML for the inout type
       'word-count-settings-page', //slug
       'wcp_first_section' // the Section where the field is added
     ); 
     register_setting(
       'wordcountplugin',
       'wcp_headline',
       array(
         'sanitize_callback' => 'sanitize_text_field',
         'default' => 'Post Statistics'
       )
      );

      // Third input CHECKBOX Word Count //
     add_settings_field( // how the field is displayed on the backend
       'wcp_wordcount', 
       'Word Count', // label
       array($this, 'checkboxHTML'), // the HTML for the inout type
       'word-count-settings-page', //slug
       'wcp_first_section', // the Section where the field is added
       array('theName' => 'wcp_wordcount')
     ); 
     register_setting(
       'wordcountplugin',
       'wcp_wordcount',
       array(
         'sanitize_callback' => 'sanitize_text_field',
         'default' => '1' // CHECKED
       )
      );

      // 4th input CHECKBOX Character Count //
     add_settings_field( // how the field is displayed on the backend
       'wcp_charactercount', 
       'Character Count', // label
       array($this, 'checkboxHTML'), // the HTML for the inout type
       'word-count-settings-page', //slug
       'wcp_first_section', // the Section where the field is added
       array('theName' => 'wcp_charactercount')
     ); 
     register_setting(
       'wordcountplugin',
       'wcp_charactercount',
       array(
         'sanitize_callback' => 'sanitize_text_field',
         'default' => '1' // CHECKED
       )
      );
      
      // 5th input CHECKBOX Read Time //
     add_settings_field( // how the field is displayed on the backend
       'wcp_readtime', 
       'Read Time', // label
       array($this, 'checkboxHTML'), // the HTML for the inout type
       'word-count-settings-page', //slug
       'wcp_first_section', // the Section where the field is added
       array('theName' => 'wcp_readtime')
     ); 
     register_setting(
       'wordcountplugin',
       'wcp_readtime',
       array(
         'sanitize_callback' => 'sanitize_text_field',
         'default' => '1' // CHECKED
       )
      );
  }

  // Sanitize input from malicious input stuff
  function sanitizeLocation($input) {
     if($input != '0' AND $input != '1') { // not the Options available
        add_settings_error(
          'wcp_location', // the option
          'wcp_location_error', // the html
          'Display location must be beginning or end'       
       );
       return get_option('wcp_location');       
     } return $input;
  }

  ///////////////////////////////////
  // Reusable Checkbox HTML function
  function checkboxHTML($args) { ?>
   <input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked(get_option($args['theName']), '1') ?>>
 <?php }

  
  // Headline Text
  function headlineHTML() { ?>
    <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')); ?>">

  <?php }

  // INPUT Select Html
  function locationHTML() { ?>
     <select name="wcp_location"> <!-- 'add_settings_field' name -->
        <option value="0" <?php selected(get_option('wcp_location'), '0') ?>>Beginning of post</option>
        <option value="1" <?php selected(get_option('wcp_location'), '1') ?>>End of post</option>
     </select>
   <?php
  }


  function adminPage() { 
   add_options_page(
    'Word Count Settings',
    __('Word Count', 'wcpdomain'), // the title in the settings menu (on the page menu)
    'manage_options', //for admins
    'word-count-settings-page', // slug name (url after /wpadmin)
    array(
     $this,
     'ourHTML'
    )
     );
   }

   // HTMl Displayed
   function ourHTML () {
     ?>
       <div class="wrap">
        <h1>Word Count Settings</h1>
         <form action="options.php" method="POST">
           <?php
             settings_fields('wordcountplugin');  // register_setting(), group name of plugin
             do_settings_sections('word-count-settings-page'); // read this file functions
             submit_button();
           ?>
         </form>
       </div>
     <?php
   }
   // End HTMl Displayed
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();





