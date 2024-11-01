<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Fired during plugin activation
 *
 * @link       slazzer.com
 * @since      1.0.0
 *
 * @package    Slazzer_Background_Changer
 * @subpackage Slazzer_Background_Changer/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Slazzer_Background_Changer
 * @subpackage Slazzer_Background_Changer/includes
 * @author     Slazzer Developers <developers@slazzer.com>
 */
class Slazzer_Background_Changer_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	global $wpdb;
        $sql = "CREATE TABLE `".$wpdb->prefix."wc_slazzer_process_status` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `product_id` int(11) DEFAULT NULL,
                  `image_id` int(11) DEFAULT NULL,
                  `status` varchar(10) DEFAULT NULL,
                  `type` text,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $wpdb->query($sql);
        $sql = "CREATE TABLE `".$wpdb->prefix."wc_slazzer_process_history` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `image_id` int(11) DEFAULT NULL,
                  `product_id` int(11) DEFAULT NULL,
                  `attach_id` int(11) DEFAULT NULL,
                  `can_restore_or_delete` varchar(10) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;";
        $wpdb->query($sql);
        
	}

}
