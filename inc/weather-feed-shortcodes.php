<?php

/************************************************************************/
/* WEATHER FEED SHORTCODE
/************************************************************************/

function wf_weather_feed( $atts, $content = null ) {

	global $weather_feed_options;

	// GET SHORTCODE OPTIONS
	extract( shortcode_atts( array(
		'lattitude' => '',
		'longitude' => ''
	), $atts ));

	if ( !isset( $weather_feed_options['weather_cache'] ) )
		return;

	require_once('libs/forecastio/forecast.io.php');

	$forecast_icons = array('clear-day', 'clear-night', 'rain', 'snow', 'sleet', 'wind', 'fog', 'cloudy', 'partly-cloudy-day', 'partly-cloudy-night');
	$icon           = in_array( $weather_feed_options['weather_cache']['icon'], $forecast_icons ) ? $weather_feed_options['weather_cache']['icon'] : 'cloudy';
	$temp           = strstr( $weather_feed_options['weather_cache']['apparentTemperature'], '.', true );

	if ( $weather_feed_options['skin'] != 'none' ) {
		$icon      = '<img src="' . WF_URL_PATH . '/css/skins/'.$weather_feed_options['skin'].'/icons/svg/' . $icon . '.svg" alt="" />';
		$temp_icon = '<img src="' . WF_URL_PATH . '/css/skins/'.$weather_feed_options['skin'].'/icons/svg/farenheit.svg" alt="" />';
	} else {
		$icon      = ucwords( str_replace('-', ' ', $icon) );
		$temp_icon = '°F';
	}

	return '<div class="weather-feed group"><div class="weather-feed-inner"><span class="weather-feed-temp">' . $temp . '<span class="weather-feed-temp-degree">' . $temp_icon . '</span></span>'.$icon.'</div></div>';

}

add_shortcode('weather_feed', 'wf_weather_feed');

?>