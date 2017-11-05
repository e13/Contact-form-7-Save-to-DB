<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Form 7 Save To DB
 * Plugin URI:        mfc.mechanical-pie.com
 * Description:       A free version of Mechanical Forms Collector WP Plugin. Collects data from Contact Form 7 forms
 * Version:           1.0.0
 * Text Domain:       cf7-save-to-db
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cf7-stdb.php';

function activate_cf7_stdbr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7-stdb-activator.php';
	CF7_STDB_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_cf7_stdbr' );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cf7_stdb() {

	$plugin = new CF7_STDB();
	$plugin->run();

}
run_cf7_stdb();
