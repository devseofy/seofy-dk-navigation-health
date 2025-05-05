<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://seofy.dk
 * @since      1.0.0
 *
 * @package    Seofy_Dk_Navigation_Health
 * @subpackage Seofy_Dk_Navigation_Health/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Seofy_Dk_Navigation_Health
 * @subpackage Seofy_Dk_Navigation_Health/includes
 * @author     Jonard Aragon <jonardaragon@gmail.com>
 */
class Seofy_Dk_Navigation_Health_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'seofy-dk-navigation-health',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
