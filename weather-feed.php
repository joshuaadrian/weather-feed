<?php

/*
Plugin Name: Weather Feed
Plugin URI: https://github.com/joshuaadrian/weather-feed
Description: Pull in weather feed. Place them with shortcodes.
Author: Joshua Adrian
Version: 0.3.0
Author URI: https://github.com/joshuaadrian/
*/

/************************************************************************/
/* ERROR LOGGING
/************************************************************************/

/**
 *  Simple logging function that outputs to debug.log if enabled
 *  _log('Testing the error message logging');
 *	_log(array('it' => 'works'));
 */

if (!function_exists('_log')) {
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}

/************************************************************************/
/* DEFINE PLUGIN ID AND NICK
/************************************************************************/

// DEFINE PLUGIN BASE
define('WF_PATH', plugin_dir_path(__FILE__));
// DEFINE PLUGIN URL
define('WF_URL_PATH', plugins_url() . '/weather-feed');
// DEFINE PLUGIN ID
define('WF_PLUGINOPTIONS_ID', 'weather-feed');
// DEFINE PLUGIN NICK
define('WF_PLUGINOPTIONS_NICK', 'Weather Feed');
// DEFINE PLUGIN NICK
register_activation_hook(__FILE__, 'wf_add_defaults');
// DEFINE PLUGIN NICK
register_uninstall_hook(__FILE__, 'wf_delete_plugin_options');
// ADD LINK TO ADMIN
add_action('admin_init', 'wf_init' );
// ADD LINK TO ADMIN
add_action('admin_menu', 'wf_add_options_page');
// ADD LINK TO ADMIN
add_filter( 'plugin_action_links', 'wf_plugin_action_links', 10, 2 );
// GET OPTION
$weather_feed_options = get_option('wf_options');

if ( isset( $weather_feed_options['debug'] ) && $weather_feed_options['debug'] ) {
	_log( $weather_feed_options );
}

if ( !function_exists( 'get_plugins' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( is_admin() ) {
	$wf_data = get_plugin_data( WF_PATH . plugin_basename( dirname( __FILE__ ) ) . '.php', false, false );
}

if ( !function_exists('markdown') ) {
	require_once WF_PATH . 'assets/libs/php-markdown/markdown.php';
}

/************************************************************************/
/* Delete options table entries ONLY when plugin deactivated AND deleted
/************************************************************************/
function wf_delete_plugin_options() {
	delete_option('wf_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'posk_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------
//delete_option( 'wf_options' ); wf_add_defaults();
// Define default option settings
function wf_add_defaults() {

	global $weather_feed_options;

  if ( !$weather_feed_options || !is_array( $weather_feed_options ) ) {

		delete_option( 'wf_options' );

		$wf_defaults = array(
			'cron_frequency'      => 'every_fifteen_minutes',
			'skin'                => 'super-fresh',
			'weather_lattitude'   => '44.983',
			'weather_longitude'   => '-93.266',
			'weather_measurement' => 'fahrenheit',
			'forecastio_api_key'  => '',
			'debug'               => false
		);

		update_option( 'wf_options', $wf_defaults );

	}

}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'posk_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function wf_init() {
	register_setting( 'wf_plugin_options', 'wf_options', 'wf_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'posk_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function wf_add_options_page() {
	add_options_page('Weather Feed', WF_PLUGINOPTIONS_NICK, 'manage_options', WF_PLUGINOPTIONS_ID, 'wf_render_form');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------
// Render the Plugin options form
function wf_render_form() {

	global $wf_data;

	?>

	<div id="weather-feed-options" class="wrap">

		<div class="icon32"><img src="<?php echo WF_URL_PATH . '/images/weather-feed-admin-icon.png'; ?>" alt="" /></div>

		<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'weather_options'; ?>

		<h2 class="nav-tab-wrapper">
		  <a href="?page=weather-feed&tab=weather_options" class="nav-tab <?php echo $active_tab == 'weather_options' ? 'nav-tab-active' : ''; ?>">Weather</a>
		  <a href="?page=weather-feed&tab=settings_options" class="nav-tab <?php echo $active_tab == 'settings_options' ? 'nav-tab-active' : ''; ?>">Settings</a>
		  <a href="?page=weather-feed&tab=wiki_options" class="nav-tab <?php echo $active_tab == 'wiki_options' ? 'nav-tab-active' : ''; ?>">Wiki</a>
		</h2>

		<?php if ( $active_tab == 'weather_options' ) : ?>

			<div class="weather-feed-options-section">

	    	<form action="options.php" method="post" id="<?php echo WF_PLUGINOPTIONS_ID; ?>-options-form" name="<?php echo WF_PLUGINOPTIONS_ID; ?>-options-form">

	    		<?php

					settings_fields('wf_plugin_options');
					$options = get_option('wf_options');

					?>

	    		<h1>Location Settings</h1>

	    		<table class="form-table">

			    	<tr>

				    	<th>
				    		<label for="weather_lattitude">Weather Lattitude</label>
				    	</th>

				    	<td>
				    		<input type="text" size="57" name="wf_options[weather_lattitude]" value="<?php echo $options['weather_lattitude']; ?>" />
							</td>

						</tr>

						<tr>

				    	<th>
				    		<label for="weather_longitude">Weather Longitude</label>
				    	</th>

				    	<td>
				    		<input type="text" size="57" name="wf_options[weather_longitude]" value="<?php echo $options['weather_longitude']; ?>" /><br />
				    		<span class="help">Easily find any location's lattitude / longitude at <a href="http://www.mashupsoft.com/" target="_blank">mashupsoft.com</a></span>
							</td>

						</tr>

					</table>

		    	<div class="weather-feed-form-action">
		      	<p><input name="Submit" type="submit" value="<?php esc_attr_e('Update Settings'); ?>" class="button-primary" /></p>
		      </div>

				</form>

			</div>

		<?php endif; ?>

		<?php if ( $active_tab == 'settings_options' ) : ?>

    	<div class="weather-feed-options-section">

	    	<form action="options.php" method="post" id="<?php echo WF_PLUGINOPTIONS_ID; ?>-options-form" name="<?php echo WF_PLUGINOPTIONS_ID; ?>-options-form">

	    		<?php

					settings_fields('wf_plugin_options');
					$options = get_option('wf_options');

					?>

	    		<h1>Settings</h1>

	    		<table class="form-table">

			    	<tr>

							<th>
				    		<label for="wf_skin">Skin</label>
				    	</th>

				    	<td>

								<select name='wf_options[skin]'>

									<option value='none' <?php selected('none', $options['skin']); ?>>&mdash; None &mdash;</option>
									
									<?php

									if ( $handle = opendir( WF_PATH . 'css/skins' ) ) {

								    while ( false !== ( $entry = readdir( $handle ) ) ) {

								    	if ($entry != "." && $entry != "..") { ?>
								        <option value='<?php echo $entry; ?>' <?php selected($entry, $options['skin']); ?>><?php echo ucwords( str_replace( '-', ' ', $entry ) ); ?></option>
								    	<?php }

								    }

								    closedir( $handle );

									}

									?>
								</select>
							</td>

						</tr>

						<tr>

				    	<th>
				    		<label for="cron_freuquency">Cron Frequency</label>
				    	</th>

				    	<td>
								<select name='wf_options[cron_frequency]'>
									<option value='every_five_minutes' <?php selected('every_five_minutes', $options['cron_frequency']); ?>>Every 5 minutes</option>
									<option value='every_fifteen_minutes' <?php selected('every_fifteen_minutes', $options['cron_frequency']); ?>>Every 15 minutes</option>
									<option value='every_half_hour' <?php selected('every_half_hour', $options['cron_frequency']); ?>>Every 30 minutes</option>
								</select>
							</td>

						</tr>

						<tr>

					    <th>
					    	<label for="forecastio_api_key">Forecast.io API Key</label>
					    </th>

					    <td>
					    	<input type="text" size="57" name="wf_options[forecastio_api_key]" value="<?php echo $options['forecastio_api_key']; ?>" id="forecastio_api_key" /><br />
					    	<span class="help">Sign up for a free forecast.io developer account and get your free API key at <a href="https://developer.forecast.io/" target="_blank">forecast.io</a></span>
							</td>

						</tr>

					</table>

	    		<div class="weather-feed-form-action">

	          <p><input name="Submit" type="submit" value="<?php esc_attr_e('Update Settings'); ?>" class="button-primary" /></p>
	        
	        </div>

				</form>

			</div>

	    <?php endif; ?>

		  <?php if ( $active_tab == 'wiki_options' ) : ?>

		    <div class="weather-feed-options-section">

	    		<div class="weather-feed-copy">

	    			<?php

    				$text = file_get_contents( WF_PATH . 'README.md' );

    				if ( false !== $text ) {

    					$html = Markdown( $text );
					  	echo $html;

    				} else {

    					echo '<h3>Issue retrieving wiki content</h3>';

    				}

					  ?>

					</div>

				</div>

			<?php endif; ?>

			<div class="credits">
				<p><?php echo $wf_data['Name']; ?> Plugin | Version <?php echo $wf_data['Version']; ?> | <a href="<?php echo $wf_data['PluginURI']; ?>">Plugin Website</a> | Author <a href="<?php echo $wf_data['AuthorURI']; ?>"><?php echo $wf_data['Author']; ?></a> | <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" style="position:relative; top:3px; margin-left:3px"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/80x15.png" /></a><a href="http://joshuaadrian.com" target="_blank" class="alignright"><img src="<?php echo plugins_url( 'images/ja-logo.png' , __FILE__ ); ?>" alt="Joshua Adrian" /></a></p>
			</div>

		</div>

	<?php

	}

/************************************************************************/
/* Sanitize and validate input. Accepts an array, return a sanitized array.
/************************************************************************/
function wf_validate_options( $input ) {

	global $weather_feed_options;

	if ( isset( $input['cron_frequency'] ) )
		wp_filter_nohtml_kses( $input['cron_frequency'] );
	
	if ( isset( $input['skin'] ) )
		wp_filter_nohtml_kses( $input['skin'] );

	if ( isset( $input['weather_lattitude'] ) )
		wp_filter_nohtml_kses( $input['weather_lattitude'] );

	if ( isset( $input['weather_longitude'] ) )
		wp_filter_nohtml_kses( $input['weather_longitude'] );

	if ( isset( $input['weather_measurement'] ) )
		wp_filter_nohtml_kses( $input['weather_measurement'] );

	if ( isset( $input['forecastio_api_key'] ) )
		wp_filter_nohtml_kses( $input['forecastio_api_key'] );

	if ( isset( $input['debug'] ) )
		wp_filter_nohtml_kses( $input['debug'] );

	return $input;

}

/************************************************************************/
/* Display a Settings link on the main Plugins page
/************************************************************************/

function wf_plugin_action_links( $links, $file ) {

	$tmp_id = WF_PLUGINOPTIONS_ID . '/weather-feed.php';

	if ( $file == $tmp_id ) {

		$wf_links = '<a href="' . get_admin_url() . 'options-general.php?page=' . WF_PLUGINOPTIONS_ID . '">' . __('Settings') . '</a>';
		array_unshift( $links, $wf_links );

	}

	return $links;

}

/************************************************************************/
/* IMPORT CSS AND JAVASCRIPT STYLES
/************************************************************************/

function weather_feed_enqueue() {

  wp_register_style('weather_feed_css', plugins_url('/css/weather-feed.css', __FILE__), false, '1.0.0' );
  wp_enqueue_style('weather_feed_css');
  wp_enqueue_script('weather_feed_scripts', plugins_url('/js/weather-feed.min.js', __FILE__), array( 'jquery' ) );

}

add_action('admin_enqueue_scripts', 'weather_feed_enqueue');

function weather_feed_skin_styles() {

	$skin = get_option('wf_options');
	$skin = $skin['skin'];

	wp_register_script('weather-feed-pinterest', plugins_url('/js/weather-feed-pinterest.min.js', __FILE__), false, '1.0', true );

  if ( $skin != 'none' ) {

		wp_register_style('wf-skin-default', plugins_url('/css/skins/'.$skin.'/style.css', __FILE__), false, '1.0.0' );
		wp_enqueue_style('wf-skin-default');
		wp_enqueue_script('wf-skin-default', plugins_url('/css/skins/'.$skin.'/app.min.js', __FILE__), array( 'jquery' ), '1.0.0' );
	
	}

}

add_action('wp_enqueue_scripts', 'weather_feed_skin_styles');

/************************************************************************/
/* INCLUDES
/************************************************************************/

require WF_PATH . 'assets/inc/weather-feed-functions.php';
require WF_PATH . 'assets/inc/weather-feed-shortcodes.php';
require WF_PATH . 'assets/inc/weather-feed-widget.php';