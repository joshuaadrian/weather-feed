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

	return var_export( $weather_feed_options['weather_cache'], true);

	// SET UP OUTPUT AND GET CACHED weather FEED FILE
	$weather_output = '';
	$tweets         = $weather_feed_options['weather_cache'];

	if ( $count > 20 || $count < 1 )
		$count = 4;

	if ( count($tweets) < $count )
		$count = count($tweets);

	for ( $i = 0; $i < $count; ++$i ) {

		$weather_output .= '<li id="tweet-'.$i.'" class="tweet">';
		$weather_output .= linkify_weather_status($tweets[$i]['text']);
		$weather_output .= ' <span class="tweet-time">';
		$weather_output .= date( "F j, Y", strtotime($tweets[$i]['created_at']));
		$weather_output .= '</span></li>';

	}

	return '<h4><a href="http://weather.com/' . $weather_feed_options['weather_username'] . '">@' . $weather_feed_options['weather_username'] . '</a></h4><ul class="tweets group">' . $weather_output . '</ul>';

}

add_shortcode('weather_feed', 'wf_weather_feed');

?>