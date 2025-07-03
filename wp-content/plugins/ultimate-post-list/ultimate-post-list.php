<?php

/**
 * @link              https://www.kybernetik-services.com
 * @since             1.0.0
 * @package           Ultimate_Post_List
 *
 * @wordpress-plugin
 * Plugin Name:       Ultimate Post List
 * Plugin URI:        http://wordpress.org/plugins/ultimate-post-list/
 * Description:       Make up custom-tailored preview lists of the contents easily and place them in widget areas and post contents.
 * Version:           5.2.7.1
 * Requires at least: 4.0
 * Requires PHP:      5.2
 * Author:            Kybernetik Services
 * Author URI:        https://www.kybernetik-services.com/?utm_source=wordpress_org&utm_medium=plugin&utm_campaign=ultimate-post-list&utm_content=author
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ultimate-post-list
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The constants for this plugin
 */
const UPL_NAME = 'Ultimate Post List';
const UPL_VERSION = '5.2.7.1';
define( 'UPL_ROOT', plugin_dir_path( __FILE__ ) );
define( 'UPL_URL', plugin_dir_url( __FILE__ ) );
define( 'UPL_ROOT_FILE', plugin_basename( __FILE__ ) );
const UPL_POST_TYPE = 'upl_post_list';
const UPL_SHORTCODE_NAME = 'ultimate_post_list';
const UPL_OPTION_NAME = 'ultimate_post_list_settings';
const UPL_CLONE_ACTION_NAME = 'clone_upl';
const UPL_TRANSIENT_PLUGIN_ACTIVATED = 'ultimate-post-list-plugin-activated';
const UPL_TRANSIENT_LIST_CLONED = 'ultimate-post-list-list-cloned';
define( 'UPL_CSS_FILE_PATH', dirname( __FILE__ ) . '/public/css/ultimate-post-list-public.css' );
define( 'UPL_CSS_FILE_URL', plugin_dir_url( __FILE__ ) . 'public/css/ultimate-post-list-public.css' );

/**
 * Class autoload for plugin classes
 *
 * @param $class_name
 */
function upl_autoloader( $class_name )
{
    if ( false !== strpos( $class_name, 'Ultimate_Post_List' ) ) {
        include UPL_ROOT . 'includes/class-' . $class_name . '.php';
    }
}
spl_autoload_register('upl_autoloader');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ultimate-post-list-activator.php
 */
function activate_ultimate_post_list() {
	Ultimate_Post_List_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ultimate-post-list-deactivator.php
 */
function deactivate_ultimate_post_list() {
	Ultimate_Post_List_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ultimate_post_list' );
register_deactivation_hook( __FILE__, 'deactivate_ultimate_post_list' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ultimate_post_list() {

	$plugin = new Ultimate_Post_List();
	$plugin->run();

}
run_ultimate_post_list();
