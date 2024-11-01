<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://slazzer.com/
 * @since             1.0.0
 * @package           Slazzer_Background_Changer
 *
 * @wordpress-plugin
 * Plugin Name:       Slazzer background changer
 * Plugin URI:        https://slazzer.com
 * Description:       The plugin to change the background color or background image of any product's image. This plugin is based on woocoomerce plugin. So activation of woocommerce plugin is must.
 * Version:           3.14
 * Author:            slazzer
 * Author URI:        https://slazzer.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       slazzer-background-changer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if (is_multisite() && is_network_admin()) {
  $class = 'notice notice-error';
  $message = __('Slazzer background changer is not compatible with WordPress multisite.You can try this with single individual child sites or deactivate this plugin manually.', 'slazzer');
  printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}


require_once plugin_dir_path( __FILE__ ) . 'includes/slazzer-background-changer.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SLAZZER_BACKGROUND_CHANGER_VERSION', '3.14' );
define( 'SLAZZER_ROOT_URL', plugin_dir_url( __FILE__ ) );
define( 'SLAZZER_PLUGIN_PREFIX', 'slazzer_' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-slazzer-background-changer-activator.php
 */
function slazzer_activate_slazzer_background_changer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-slazzer-background-changer-activator.php';
	Slazzer_Background_Changer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-slazzer-background-changer-deactivator.php
 */
function slazzer_deactivate_slazzer_background_changer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-slazzer-background-changer-deactivator.php';
	Slazzer_Background_Changer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'slazzer_activate_slazzer_background_changer' );
register_deactivation_hook( __FILE__, 'slazzer_deactivate_slazzer_background_changer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-slazzer-background-changer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function slazzer_run_slazzer_background_changer() {

	$plugin = new Slazzer_Background_Changer();
	$plugin->run();

}
slazzer_run_slazzer_background_changer();


add_filter( 'plugin_row_meta', 'slazzer_support_and_contact_links', 10, 4 );
function slazzer_support_and_contact_links( $links_array, $plugin_file_name, $plugin_data, $status )
{

  if( strpos( $plugin_file_name, basename(__FILE__) ))
  {
    $links_array[] = 'Support: support@slazzer.com';
  }
 
  return $links_array;
}
