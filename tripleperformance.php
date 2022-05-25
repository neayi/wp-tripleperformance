<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://tripleperformance.fr/
 * @since             1.0.0
 * @package           Tripleperformance
 *
 * @wordpress-plugin
 * Plugin Name:       Triple Performance
 * Plugin URI:        https://tripleperformance.fr/
 * Description:       Ce plugin permet d'intégrer des pages du site Triple Performance dans un site WordPress automatiquement
 * Version:           1.0.0
 * Author:            Bertrand Gorge
 * Author URI:        https://neayi.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tripleperformance
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
define( 'TRIPLEPERFORMANCE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tripleperformance-activator.php
 */
function activate_tripleperformance() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tripleperformance-activator.php';
	Tripleperformance_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tripleperformance-deactivator.php
 */
function deactivate_tripleperformance() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tripleperformance-deactivator.php';
	Tripleperformance_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tripleperformance' );
register_deactivation_hook( __FILE__, 'deactivate_tripleperformance' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tripleperformance.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tripleperformance() {

	$plugin = new Tripleperformance();
	$plugin->run();

}
run_tripleperformance();
