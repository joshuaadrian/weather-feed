<?php

/************************************************************************/
/* WEATHER API CALLS
/************************************************************************/
//wf_weather_call();
function wf_weather_call() {

	// GET PLUGIN OPTIONS
	global $weather_feed_options;

	if ( !isset($weather_feed_options['weather_cache']) ) :
		$weather_feed_options['weather_cache'] = '';
	endif;
	if ( !isset($weather_feed_options['weather_error_log']) ) :
		$weather_feed_options['weather_error_log'] = '';
	endif;

	if ( isset( $weather_feed_options['weather_lattitude'] ) && isset( $weather_feed_options['weather_longitude'] ) && isset( $weather_feed_options['forecastio_api_key'] ) ) {

		require_once('libs/forecastio/forecast.io.php');

		$api_key                   = $weather_feed_options['forecastio_api_key'];
		$lattitude                 = $weather_feed_options['weather_lattitude'];
		$longitude                 = $weather_feed_options['weather_longitude'];
		$forecast                  = new ForecastIO($api_key); _log($forecast);
		$forecast_io_weather       = $forecast->getCurrentConditions($lattitude, $longitude); 
		$forecast_io_weather_cache = get_object_vars($forecast_io_weather->raw_data);
		_log( var_export($forecast_io_weather_cache, true) );
		
		if ( $forecast_io_weather ) {
			$weather_feed_options['weather_cache']   = $forecast_io_weather_cache;
			$weather_feed_options['forecast_io_log'] = date("F j, Y, g:i a") . ' forecast.io success rest call url =>  ' . "\r\n\n";
		} else {
			$weather_feed_options['forecast_io_error_log'] = date("F j, Y, g:i a") . ' forecast.io success rest call url =>  ' . "\r\n\n";
		}

	}

	update_option( 'wf_options', $weather_feed_options );

}

?>