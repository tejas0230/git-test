<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Wdm_Electsure_Learndash_Customization
 * @subpackage Wdm_Electsure_Learndash_Customization/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wdm_Electsure_Learndash_Customization
 * @subpackage Wdm_Electsure_Learndash_Customization/includes
 * @author     wisdmlabs <tejas0215@gmail.com>
 */
class Wdm_Electsure_Learndash_Customization_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wdm-electsure-learndash-customization',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
