<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_action('wp_ajax_slazzer_restore_backup', 'slazzer_restore_backup');

add_action('wp_ajax_nopriv_slazzer_restore_backup', 'slazzer_restore_backup');

function slazzer_restore_backup()

{

    update_option('show_backup', 0);

    
    global $wpdb;

    $sql = $wpdb->prepare("SELECT * FROM `".$wpdb->prefix."wc_slazzer_process_history` WHERE can_restore_or_delete = 1");

    $all_ids = $wpdb->get_results($sql );

    foreach ($all_ids as $id) {

        if (get_option('Slazzer_gallery_image') == 1) {

            $attachment_ids1 = get_post_meta($id->product_id, '_product_gallery_original_images', true);

            if (!empty($attachment_ids1)) {

                update_post_meta($id->product_id, '_product_image_gallery', $attachment_ids1);

            }

        }

        if (get_option('Slazzer_main_image') == 1) {

            $image_id = get_post_meta($id->product_id, '_thumbnail_id_backup', true);

            if (!empty($image_id)) {

                update_post_meta($id->product_id, '_thumbnail_id', $image_id);

            }

        }

    }

    $sql = $wpdb->prepare("DELETE FROM `".$wpdb->prefix."wc_slazzer_process_history` WHERE can_restore_or_delete = %s", 1);

    $wpdb->query($sql);

    exit();

}



?>