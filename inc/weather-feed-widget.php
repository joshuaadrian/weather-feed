<?php

/************************************************************************/
/* WEATHER FEED WIDGET
/************************************************************************/

add_action( 'widgets_init', 'weather_feed_widget' );

function weather_feed_widget() {
	register_widget( 'weather_feed_widget' );
}

class weather_feed_widget extends WP_Widget {

	function weather_feed_widget() {

		$widget_ops  = array( 'classname' => 'weather-feed', 'description' => __('A widget that displays your weather feed', 'weather_feed') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'weather-feed-widget' );
		
		$this->WP_Widget( 'weather-feed-widget', __('Weather Feed Widget', 'weather_feed'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {

		extract( $args );
		$title = isset( $instance['title'] ) && !empty( $instance['title'] ) ? $instance['title'] : '';
		echo $before_widget;
		echo '<h3 class="widget-title">' . $title . '</h3>';
		echo do_shortcode('[weather_feed]');
		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;

	}

	function form( $instance ) {

		$defaults = array( 'title' => __('Weather Feed', 'weather-feed') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'weather-feed'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" style="width:100%;" value="<?php echo $instance['title']; ?>" />
		</p>

	<?php

	}

}