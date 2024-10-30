<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://vinamian.com
 * @since             1.0.0
 * @package           Cafepress
 *
 * @wordpress-plugin
 * Plugin Name:       CafePress
 * Plugin URI:        https://wordpress.org/plugins/cafepress/
 * Description:       Powerful WordPress plugin designed to streamline the operations of your cafe, restaurants
 * Version:           1.0.6
 * Author:            Vinamian
 * Author URI:        https://vinamian.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cafepress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CAFEPRESS_VERSION', '1.0.6' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cafepress-activator.php
 */
function cafepress_activate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cafepress-activator.php';
	Cafepress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cafepress-deactivator.php
 */
function cafepress_deactivate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cafepress-deactivator.php';
	Cafepress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'cafepress_activate_plugin' );
register_deactivation_hook( __FILE__, 'cafepress_deactivate_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cafepress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function cafepress_run_plugin() {

	$plugin = new Cafepress();
	$plugin->run();

}
cafepress_run_plugin();