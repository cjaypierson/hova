<?php
/*
Plugin Name: Flight Radar
Version: 0.1
Plugin URI: http://www.beliefmedia.com/wp-plugins/flightradar.php
Description: Flight Radar 24 Shortcode
Author: Marty Khoury
Author URI: http://www.beliefmedia.com/
*/


function beliefmedia_flightradar($atts, $content=null) {
  extract(shortcode_atts(array(
   'code' => 'syd',
   'lat' => '',
   'lng' => '',
   'zoom' => '8',
   'width' => '100%',
   'height' => '350',
   'align' => 'center'
     ), $atts));

    /* If lat empty, query the flight.org API (after db check) and get lat/long data for a code */
    if ($lat == "") {

      /* Check to see if airport code saved */
      $latlng = beliefmedia_flightradar_get_option('beliefmedia_flightradar_options', $code, $default = false);

     	$geodata = explode(",", $latlng);
	$lat = $geodata[0];
	$lng = $geodata[1];

          if (empty($latlng)) {

             $xml = simplexml_load_file('http://api.flight.org/airports/xml/' . $code . '/airport.xml');

	      if ($xml->status->attributes()->{'code'} == '200') {

		$lat = (string) $xml->data->data->attributes()->{'lat'};
		$lng = (string) $xml->data->data->attributes()->{'lng'};
		$dbValue = "$lat,$lng";

		beliefmedia_flightradar_save_option($beliefmedia_flightradar_options='beliefmedia_flightradar_options', $code, $dbValue);
      
                } else {

		/* Code 404, 420 et al. Generate Error */
		$return .= "ERROR 404/420<br><br>";
		} 
	  }
    }

    /* Render Radar Plot */

    if ( ($lat) && ($lng) ) {
	$return .= '<p align="' . $align . '"><iframe src="http://www.flightradar24.com/simple_index.php?lat=' . $lat . '&lon=' . $lng . '&z=' . $zoom . '" width="' . $width . '" height="' . $height . '"></iframe></p>';
	   } else {
	$return .= '<p align="' . $align . '"><iframe src="http://www.flightradar24.com/simple_index.php?z=' . $zoom . '" width="' . $width . '" height="' . $height . '"></iframe></p>';
    }

 return $return;
}
add_shortcode('flightradar', 'beliefmedia_flightradar');


/*
	Add option array to database.
	Store airport data here ..
*/


function beliefmedia_flightradar_save_option($beliefmedia_flightradar_options, $key, $value) {

    $options = get_option($beliefmedia_flightradar_options);

	if ( !$options ) {
	  // No Options Yet ..
	  add_option('beliefmedia_flightradar_options', array($key => $value) );
	} else {
	  // Update Options
	  $options[$key] = $value;
	  update_option('beliefmedia_flightradar_options', $options );
	}
}


/*
	Retrieve airport lat/lng data
*/


function beliefmedia_flightradar_get_option($beliefmedia_flightradar_options, $key, $default = false) {
	
    $options = get_option( $beliefmedia_flightradar_options );

	if ( $options ) {
	  return (array_key_exists( $key, $options )) ? $options[$key] : $default;
	}

   return $default;
}


/*
	Menu Links
*/


function beliefmedia_flightradar_action_links($links, $file) {
  static $this_plugin;
  if (!$this_plugin) {
   $this_plugin = plugin_basename(__FILE__);
  }

  if ($file == $this_plugin) {
	$links[] = '<a href="http://www.beliefmedia.com/" target="_blank">Mayday</a>';
	$links[] = '<a href="http://www.flight.org/flight-radar-24-wordpress-plugin" target="_blank">Flight</a>';
  }
 return $links;
}
add_filter('plugin_action_links', 'beliefmedia_flightradar_action_links', 10, 2);



/*
	Delete Option Data on Deactivation
*/

	
function remove_beliefmedia_flightradar_options() {
  global $wpdb;
   $wpdb->query("DELETE FROM $wpdb->options WHERE `option_name` = ('beliefmedia_flightradar_options')" );
}
register_deactivation_hook( __FILE__, 'remove_beliefmedia_flightradar_options' );