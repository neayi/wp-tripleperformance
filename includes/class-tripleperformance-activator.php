<?php

/**
 * Fired during plugin activation
 *
 * @link       https://tripleperformance.fr/
 * @since      1.0.0
 *
 * @package    Tripleperformance
 * @subpackage Tripleperformance/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tripleperformance
 * @subpackage Tripleperformance/includes
 * @author     Bertrand Gorge <bertrand.gorge@neayi.com>
 */
class Tripleperformance_Activator {

	/**
	 * Synchronize the articles once every hour.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if (! wp_next_scheduled ( 'tp_syncArticles' )) {
			wp_schedule_event( time(), 'hourly', 'tp_syncArticles' ); // DEBUG: 'tp_sync_interval'
		}
	}

}
