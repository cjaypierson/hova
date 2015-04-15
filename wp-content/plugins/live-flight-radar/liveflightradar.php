<?php
/*
Plugin Name: Live Flight Radar
Version: 1.0
Plugin URI: http://www.luciaintelisano.it
Description: Flight Radar 24 Shortcode
Author: Lucia Intelisano
Author URI: http://www.luciaintelisano.it/
*/

 

  function theme_name_scripts() {
   	wp_enqueue_script('jquery-ui-core');
   	wp_enqueue_script( 'jquery-ui-autocomplete' );
 	wp_enqueue_style('jquery-ui-smoothness', plugins_url('jquery-ui.css', __FILE__), false, null);
	wp_enqueue_script( 'default_scripts', plugins_url('script.js', __FILE__), array(), '1.0.2', true );
}

add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
		  

function luciaintelisano_liveflightradar($atts, $content=null) {
  extract(shortcode_atts(array(
   'code' => 'fci',
   'lat' => '',
   'lng' => '',
   'zoom' => '8',
   'width' => '100%',
   'height' => '350',
   'align' => 'center'
     ), $atts));

   
    if ($lat == "") {

      /* Check to see if airport code saved */
      $latlng = luciaintelisano_liveflightradar_get_option('luciaintelisano_liveflightradar_options', $code, $default = false);

     	$geodata = explode(",", $latlng);
	$lat = $geodata[0];
	$lng = $geodata[1];

          if (empty($latlng)) {

             $xml = simplexml_load_file('http://api.flight.org/airports/xml/' . $code . '/airport.xml');

	      if ($xml->status->attributes()->{'code'} == '200') {

		$lat = (string) $xml->data->data->attributes()->{'lat'};
		$lng = (string) $xml->data->data->attributes()->{'lng'};
		$dbValue = "$lat,$lng";

		luciaintelisano_liveflightradar_save_option($luciaintelisano_liveflightradar_options='luciaintelisano_liveflightradar_options', $code, $dbValue);
      
                } else {

 
		$return .= "ERROR 404/420<br><br>";
		} 
	  }
    }
 	$str = "";
 	
   $str .= "<script>var urlGetCode = '".plugins_url('getCode.php', __FILE__)."'; var urlGetIframe = '".plugins_url('getIframe.php', __FILE__)."'; </script>";
 
   $str .= "Digita il nome di un aeroporto:<br><input type=\"text\" id=\"airportCode\" size=75><br><input type=\"hidden\" id=\"airportId\"><div id=\"airportLocation\"></div><br>";
   
    if ( ($lat) && ($lng) ) {
	$return .= $str.'<div id="contentRadar" align="' . $align . '"><iframe src="http://www.flightradar24.com/simple_index.php?lat=' . $lat . '&lon=' . $lng . '&z=' . $zoom . '" width="' . $width . '" height="' . $height . '"></iframe></div>';
	   } else {
	$return .= $str.'<div id="contentRadar"  align="' . $align . '"><iframe src="http://www.flightradar24.com/simple_index.php?z=' . $zoom . '" width="' . $width . '" height="' . $height . '"></iframe></div>';
    }

 return $return;
}
add_shortcode('liveflightradar', 'luciaintelisano_liveflightradar');

function RandomString()
{
    $characters = ’0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ’;
    $randstring = '';
    for ($i = 0; $i < 10; $i++) {
        $randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}
 


function luciaintelisano_liveflightradar_save_option($luciaintelisano_liveflightradar_options, $key, $value) {

    $options = get_option($luciaintelisano_liveflightradar_options);

	if ( !$options ) {
	  
	  add_option('luciaintelisano_liveflightradar_options', array($key => $value) );
	} else {
 
	  $options[$key] = $value;
	  update_option('luciaintelisano_liveflightradar_options', $options );
	}
}


/*
	Retrieve airport lat/lng data
*/


function luciaintelisano_liveflightradar_get_option($luciaintelisano_liveflightradar_options, $key, $default = false) {
	
    $options = get_option( $luciaintelisaluciaintelisano_no_liveflightradar_options );

	if ( $options ) {
	  return (array_key_exists( $key, $options )) ? $options[$key] : $default;
	}

   return $default;
}


/*
	Menu Links
*/


function luciaintelisano_liveflightradar_action_links($links, $file) {
  static $this_plugin;
  if (!$this_plugin) {
   $this_plugin = plugin_basename(__FILE__);
  }

  if ($file == $this_plugin) {
	$links[] = '<a href="http://www.luciaintelisano.it/radar-di-volo-plugin-wordpress" target="_blank">Plugin page</a>';
 
  }
 return $links;
}
add_filter('plugin_action_links', 'luciaintelisano_liveflightradar_action_links', 10, 2);



/*
	Delete Option Data on Deactivation
*/

	
function remove_luciaintelisano_liveflightradar_options() {
  global $wpdb;
   $wpdb->query("DELETE FROM $wpdb->options WHERE `option_name` = ('luciaintelisano_liveflightradar_options')" );
}
register_deactivation_hook( __FILE__, 'remove_luciaintelisano_liveflightradar_options' );


function add_my_media_button() {
    echo '<a href="javascript:wp.media.editor.insert(\'[liveflightradar]\');" id="insert-my-media" class="button">Add live flight radar</a>';
}
add_action('media_buttons', 'add_my_media_button');