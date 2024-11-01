<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       slazzer.com
 * @since      1.0.0
 *
 * @package    Slazzer_Background_Changer
 * @subpackage Slazzer_Background_Changer/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Slazzer_Background_Changer
 * @subpackage Slazzer_Background_Changer/includes
 * @author     Slazzer Developers <developers@slazzer.com>
 */
class Slazzer_Background_Changer_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'slazzer-background-changer',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
