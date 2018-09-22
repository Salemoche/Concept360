<?php

//
// Load Parent Styles
//

  add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

  function enqueue_parent_styles() {
     wp_enqueue_style( 'parent-style', get_template_directory_uri().'/assets/css/app.css' );
  }


?>
