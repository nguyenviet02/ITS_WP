<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.kybernetik-services.com
 * @since      1.0.0
 *
 * @package    Ultimate_Post_List
 * @subpackage Ultimate_Post_List/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ultimate_Post_List
 * @subpackage Ultimate_Post_List/includes
 * @author     Kybernetik Services <wordpress@kybernetik.com.de>
 */
class Ultimate_Post_List_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ultimate-post-list',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
