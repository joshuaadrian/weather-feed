<?php

/************************************************************************/
/* GET EXTERNAL CONTENT
/************************************************************************/

if ( false === ( $wf_cache = get_transient( 'wf_forecast' ) ) ) {

	$api_key   = $weather_feed_options['forecastio_api_key'];
	$lattitude = $weather_feed_options['weather_lattitude'];
	$longitude = $weather_feed_options['weather_longitude'];

	if ( $api_key && $lattitude && $longitude ) {

		$wf_url    = 'https://api.forecast.io/forecast/' . $api_key . '/' . $lattitude . ',' . $longitude;

		$wf_forecast_args = array(
			'timeout' => 120
		);

		$wf_result = wp_remote_get( $wf_url, $wf_forecast_args );

		if ( is_wp_error( $wf_result ) ) {

		  $error_message = $wf_result->get_error_message();
		  if ( $weather_feed_options['debug'] ) _log("Weather Feed API Error => $error_message");

		} else {

			set_transient( 'wf_forecast', serialize( json_decode( $wf_result['body'], true, 100 ) ), 60 * 15 );
		
		}

	}

}