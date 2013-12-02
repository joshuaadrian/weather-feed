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

	if ( false === ( $wf_cache = get_transient( 'wf_forecast' ) ) )
		return;

	$wf_cache = unserialize( $wf_cache );

	_log( $wf_cache );

	$forecast_icons = array(
		'clear-day'           => 'clear-day',
		'clear-night'         => 'clear-night',
		'rain'                => 'rain',
		'snow'                => 'heavy-snow',
		'sleet'               => 'light-sleet',
		'wind'                => 'windy',
		'fog'                 => 'heavy-fog',
		'cloudy'              => 'cloudy',
		'partly-cloudy-day'   => 'partly-cloudy',
		'partly-cloudy-night' => 'cloudy-night'
	);

	$icon           = array_key_exists( $wf_cache['currently']['icon'], $forecast_icons ) ? $forecast_icons[$wf_cache['currently']['icon']] : 'cloudy';
	$temp           = strstr( $wf_cache['currently']['apparentTemperature'], '.', true );
	$temp_icon      = 'fahrenheit';

	if ( $weather_feed_options['skin'] != 'none' ) {
		$icon      = '<i class="icon-' . $icon . '" ></i>';
		$temp_icon = '<i class="icon-' . $temp_icon . '" ></i>';
	} else {
		$icon      = ucwords( str_replace('-', ' ', $icon) );
		$temp_icon = '°F';
	}

	return '<div class="weather-feed group"><div class="weather-feed-inner"><span class="weather-feed-temp">' . $temp . '<span class="weather-feed-temp-degree">' . $temp_icon . '</span></span>'.$icon.'</div></div>';

}

add_shortcode('weather_feed', 'wf_weather_feed');

?>