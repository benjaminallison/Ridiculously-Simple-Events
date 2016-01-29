<?php
/**
 * Plugin Name: Ridiculously Simple Events
 * Plugin URI: http://www.benjaminallison.com/
 * Description: A no nonesense plugin to add basic events, with date range to show the event, event date/time, and description.
 * Version: 1.1.2
 * Author: Benjamin Allison
 * Author URI: http://benjaminallison.com
 * Copyright: (c) 2015 Benjamin allison
 * Text Domain: rse
 */

define('RSE_PLUGIN_VERSION', '1.1.2');
define('RSE_PLUGIN_NAME', "Ridiculously Simple Events");
define('RSE_PLUGIN_BASENAME', plugin_basename( __FILE__ ));
define('RSE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RSE_PLUGIN_URL', plugins_url('/', __FILE__));
define('RSE_POST_TYPE', 'event');
define('RSE_TAXONOMY', 'event_type');

require_once RSE_PLUGIN_DIR . "lib/rse_core.php" ;
add_action('init', 'RSE::rse_register_post_type');
add_action('init', 'RSE::rse_register_taxonomy');
add_action('add_meta_boxes', 'RSE::rse_init_meta_box');
add_action('save_post', 'RSE::rse_save');
add_action('admin_enqueue_scripts', 'RSE::rse_enqueue_scripts');
add_action('admin_init', 'RSE::rse_replace_post_excerpt');
add_shortcode('rse_events', 'RSE::rse_init_shortcode');
add_action( 'rse_clean_old_events_hook', 'RSE::rse_clean_old_events' );

register_activation_hook( __FILE__, 'rse_activation' );
function rse_activation() {
	RSE::rse_initialize();
	wp_schedule_event( time(), 'hourly', 'rse_clean_old_events_hook' );
}

register_deactivation_hook( __FILE__, 'rse_deactivation' );
function rse_deactivation() {
	wp_clear_scheduled_hook( 'rse_clean_old_events_hook' );
}