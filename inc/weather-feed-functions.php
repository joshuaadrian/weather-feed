<?php

/************************************************************************/
/* SET CUSTOM EVENT SCHEDULES
/************************************************************************/

add_filter('cron_schedules', 'wf_cron_schedules');

function wf_cron_schedules() {
	return array(
		'every_minute' => array(
			'interval' => 60 * 1,
			'display' => 'Every minute'
		),
		'every_five_minutes' => array(
			'interval' => 60 * 5,
			'display' => 'Every five minutes'
		),
		'every_fifteen_minutes' => array(
			'interval' => 60 * 15,
			'display' => 'Every fifteen minutes'
		),
		'every_half_hour' => array(
			'interval' => 60 * 30,
			'display' => 'Every half hour'
		)
	);
}

/************************************************************************/
/* SET EVENT SCHEDULE
/************************************************************************/

add_action('wf_weather_event', 'wf_weather_call');

function wf_weather_activation() {
	_log('Here1');
	if ( !wp_next_scheduled('wf_weather_event') ) {
		_log('Here2');
		wp_schedule_event( current_time('timestamp'), 'every_fifteen_minutes', 'wf_weather_event');
	}
}

add_action('wp', 'wf_weather_activation');