<?php
/*
Plugin Name: Geopost
Plugin URI: http://www.rampantlogic.com/geopost
Description: Uses Google Maps API to create a map containing markers for posts based on individually assigned geocoded locations.
Version: 1.0
Author: Rampant Logic
Author URI: http://www.rampantlogic.com
License: GPL2
*/

function geopost_icon_dir() {

   return plugins_url('/icons', __FILE__);

}



function geopost_insert_map($atts) {

  extract(shortcode_atts(array(

      'subset' => -1,    // -1 shows all icon types

      'hybrid' => 0,

      'zoom' => 1,

      'lat' => 23,

      'lng' => 12,

      'width' => 520,

      'height' => 300,

  ), $atts));

  echo '<div id="map_canvas" style="width:'.$width.'px; ';

  echo 'height:'.$height.'px"></div>';

  echo '<script>';

  echo 'geopost_map("map_canvas","' . geopost_icon_dir() . '",';

  echo "$hybrid, $zoom, $lat, $lng);";



  $posts = get_posts('numberposts=-1&post_type=any');

  foreach($posts as $post) {

    $id = $post->ID;

    $lat = get_post_meta($id, 'geopost_lat', true);

    $lng = get_post_meta($id, 'geopost_lng', true);

    $icon = get_post_meta($id, 'geopost_icon', true);



    if(($subset >= 0) && ($icon != $subset))

      continue;



    if(($lat != null) && ($lng != null)) {

      echo 'geopost_add_marker("' . get_the_title($id) . '",' 

           . $lat . ',' . $lng . ',"' . get_permalink($id) . '",'

           . $icon . ');';

    }

  }  

  echo '</script>';

}



function geopost_save_metabox_data($post_id) {

  // verify this came from the our screen and with proper authorization,

  // because save_post can be triggered at other times



  if ( !wp_verify_nonce( $_POST['geopost_noncename'], plugin_basename(__FILE__)))

    return $post_id;



  // verify if this is an auto save routine. If it is, our form has not been

  // submitted, so we dont want to do anything

  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 

    return $post_id;

  

  // Check permissions

  if ( 'page' == $_POST['post_type'] ) {

    if ( !current_user_can( 'edit_page', $post_id ) )

      return $post_id;

  } else {

    if ( !current_user_can( 'edit_post', $post_id ) )

      return $post_id;

  }



  // OK, we're authenticated: we need to find and save the data



  $lat = $_POST['geopost_lat'];

  $lng = $_POST['geopost_lng'];

  $icon = $_POST['geopost_icon'];



  // Do something with $mydata 

  // probably using add_post_meta(), update_post_meta(), or 

  // a custom table (see Further Reading section below)



  if( ($lat == null) || ($lng == null)) {

    delete_post_meta($post_id, 'geopost_lat');

    delete_post_meta($post_id, 'geopost_lng');

    delete_post_meta($post_id, 'geopost_icon');

  } else {

    update_post_meta($post_id, 'geopost_lat', $lat);

    update_post_meta($post_id, 'geopost_lng', $lng);

    update_post_meta($post_id, 'geopost_icon', $icon);

  }



  return $location;

}



function geopost_write_metabox($post) {

  // Use nonce for verification

  wp_nonce_field( plugin_basename(__FILE__), 'geopost_noncename' );



  $postid = $post->ID;

  echo '<input type="text" id="geopost_location_field" name="geopost_location_field" value="" size="20" />';

  echo ' <a href="javascript:geopost_geocode(\'preview_canvas\', document.getElementById(\'geopost_location_field\').value);">Search</a>';

  echo ' | <a href="javascript:geopost_set_preview_marker(\'preview_canvas\',null,null,\'0\');">Clear</a>';

  echo '<br><br>';

  echo '<input type="hidden" id="geopost_lat" name="geopost_lat" value="" size="6" />';

  echo '<input type="hidden" id="geopost_lng" name="geopost_lng" value="" size="6" />';  

  echo '<input type="hidden" id="geopost_icon" name="geopost_icon" value="0" size="3" />';  

  echo '<center><div id="preview_canvas" style="width:220px; height:0px"></div></center>';

  echo '<script>';

  echo 'geopost_map("preview_canvas","'. geopost_icon_dir() .'", 1, 0);';

  $lat = get_post_meta($postid, 'geopost_lat', true);

  $lng = get_post_meta($postid, 'geopost_lng', true);

  $icon = get_post_meta($postid, 'geopost_icon', true);

  if(($lat != null) && ($lng != null)) {

    if($icon == null)

      $icon = 0;

    echo 'geopost_set_preview_marker("preview_canvas",' . $lat . ',' . $lng . ',' . $icon . ');';

  }

  echo '</script>';

}



function geopost_register_metabox() {

  add_meta_box('geopost_metabox_id', 'Geopost', 'geopost_write_metabox', 'post', 'side');

  add_meta_box('geopost_metabox_id', 'Geopost', 'geopost_write_metabox', 'page', 'side');

}



function geopost_register_menu() {

  add_options_page('Geopost Options', 'Geopost', 'manage_options', 'geopost-guid', 'geopost_options');

}



function geopost_options() {

  if (!current_user_can('manage_options'))  {

    wp_die( __('You do not have sufficient permissions to access this page.') );

  }



  echo '<div class="wrap">';

  echo '<p>No options available.</p>';

  echo '</div>';

}





// initialization

wp_enqueue_style('geopost', WP_PLUGIN_URL . '/geopost/geopost.css');

wp_register_script('geopost', WP_PLUGIN_URL . '/geopost/geopost-min.js');

wp_enqueue_script('gmaps','http://maps.google.com/maps/api/js?sensor=false');

wp_enqueue_script('geopost');



add_shortcode('geopost', 'geopost_insert_map');



// when Wordpress is rendering meta boxes, register metabox

add_action('admin_menu', 'geopost_register_metabox');

//add_action('admin_menu', 'geopost_register_menu');



// Process and save the data entered in the metabox 

add_action('save_post', 'geopost_save_metabox_data');



?>