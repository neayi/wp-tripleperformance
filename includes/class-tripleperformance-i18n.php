<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://tripleperformance.fr/
 * @since      1.0.0
 *
 * @package    Tripleperformance
 * @subpackage Tripleperformance/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Tripleperformance
 * @subpackage Tripleperformance/includes
 * @author     Bertrand Gorge <bertrand.gorge@neayi.com>
 */
class Tripleperformance_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'tripleperformance',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
