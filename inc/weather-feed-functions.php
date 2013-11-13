<?php

/************************************************************************/
/* SET CUSTOM EVENT SCHEDULES
/************************************************************************/

add_filter('cron_schedules', 'wf_cron_schedules');

function wf_cron_schedules( $schedules ) {

	if ( !array_key_exists('every_minute', $schedules) ) {
		$schedules['every_minute'] = array(
			'interval' => 60 * 1,
			'display' => __( 'Every minute' )
		);
	}

	if ( !array_key_exists('every_five_minutes', $schedules) ) {
		$schedules['every_five_minutes'] = array(
			'interval' => 60 * 5,
			'display' => __( 'Every five minutes' )
		);
	}

	if ( !array_key_exists('every_fifteen_minutes', $schedules) ) {
		$schedules['every_fifteen_minutes'] = array(
			'interval' => 60 * 15,
			'display' => __( 'Every fifteen minutes' )
		);
	}

	if ( !array_key_exists('every_half_hour', $schedules) ) {
		$schedules['every_half_hour'] = array(
			'interval' => 60 * 30,
			'display' => __( 'Every half hour' )
		);
	}

	return $schedules;
}

/************************************************************************/
/* SET EVENT SCHEDULE
/************************************************************************/

add_action('wp', 'wf_weather_activation');

function wf_weather_activation() {
	if ( !wp_next_scheduled('wf_weather_event') ) {
		wp_schedule_event( current_time('timestamp'), 'every_fifteen_minutes', 'wf_weather_event');
	}
}

add_action('wf_weather_event', 'wf_weather_call');