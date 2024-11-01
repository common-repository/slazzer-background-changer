<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_filter('big_image_size_threshold', '__return_false');



add_action('admin_menu', 'slazzer_admin_menu');



function slazzer_admin_menu(){

    add_menu_page('Slazzer - Auto Background Remover',

        'Slazzer - Auto Background Remover',

        'edit_posts',

        'slazzer-background-changer',

        'slazzer_page_callback_function',

        'dashicons-format-gallery'

    );

}



function slazzer_page_callback_function(){

    include('slazzer-background-changer_plugin_ui.php');

}


add_action( 'woocommerce_product_options_inventory_product_data', 'slazzer_misha_option_group' );
 
function slazzer_misha_option_group() {
	$post_id = sanitize_text_field($_GET['post']);
	if(is_numeric($post_id)){
		$gallery_images = get_post_meta($post_id,'_product_image_gallery',true);
	}

	if($gallery_images){		
		echo  wp_kses_post('<div class="options_group"><p class="form-field _gallery_field"><label>'.esc_html__('Gallery Images:', 'slazzer-background-changer').'</label><textarea readonly="readonly">'.$gallery_images.'</textarea></p></div>'); 	
	}
}




include('class-slazzer-background-changer-service.php');

include('class-slazzer-background-changer-utility.php');

include('slazzer-background-changer-save-settings.php');

include('slazzer-background-changer-get_all_image_ids.php');

include('slazzer-background-changer-single_image_process.php');

include('slazzer-background-changer-delete_backup.php');

include('slazzer-background-changer-restore_backup.php');

//Media Edit
add_action('handle_bulk_actions-upload', 'slazzer_background_upload_media_remove_bg_action',10,3);
function slazzer_background_upload_media_remove_bg_action($redirect, $doaction, $object_ids){
    if('slz-remove-background' == $doaction){
       $slazzer_api_key = get_option('slazzer_live_key');
       if($object_ids){
           foreach($object_ids as $single_img){
               if($single_img){
                   $image_id = $single_img;
                   $arr_media_image = slazzer_get_base64_image($image_id);
                   $old_image_file_name = $arr_media_image['old_image_file_name'];
                   $arr_request_body = slazzer_get_request_body($arr_media_image,$old_image_file_name);
                   $arr_response_body = slazzer_remove_image_background($arr_request_body,$slazzer_api_key);
                  
                   if ($arr_response_body['status']) {                    		 
                   		$old_image_file_name = esc_html($old_image_file_name);
                        $image_url = slazzer_download_image_from_url_basic($arr_response_body['api_response'], $old_image_file_name);
                        $title = get_the_title($image_id);
                        $title = esc_html($title);
                        $attachment_id = slazzer_rs_upload_from_url( $image_url, $title );
                        
                        //metadata
                        $new_metadata = get_post_meta($attachment_id,'_wp_attachment_metadata',true);
                        $old_metadata = get_post_meta($image_id,'_wp_attachment_metadata',true);
                        update_post_meta($image_id,'_wp_attachment_metadata',$new_metadata);
                        update_post_meta($image_id,'_wp_old_attachment_metadata',$old_metadata);
                        
                        //attachment
                        $new_attachment = get_post_meta($attachment_id,'_wp_attached_file',true);
                        $old_attachment = get_post_meta($image_id,'_wp_attached_file',true);
                        update_post_meta($image_id,'_wp_attached_file',$new_attachment);
                        update_post_meta($image_id,'_wp_old_attached_file',$old_attachment);
                        
                        //sending temporary attachment to draft
                        global $wpdb;
                        $post_table = $wpdb->prefix.'posts';
                        $sql = $wpdb->prepare("DELETE FROM $post_table WHERE ID=%d", $attachment_id);
                        $wpdb->query($sql);
                        delete_post_meta( $attachment_id, '_wp_attached_file');
                        delete_post_meta( $attachment_id, '_wp_attachment_metadata');
                   }
               }
           }
       }
    }
    
    if('slz-restore-background' == $doaction){
       $slazzer_api_key = get_option('slazzer_live_key');
       if($object_ids){
           foreach($object_ids as $single_img){
               if($single_img){
                   $image_id = $single_img;
                    //metadata
                    $old_metadata = get_post_meta($image_id,'_wp_old_attachment_metadata',true);
                    update_post_meta($image_id,'_wp_attachment_metadata',$old_metadata);
                    
                    //attachment
                    $old_attachment = get_post_meta($image_id,'_wp_old_attached_file',true);
                    update_post_meta($image_id,'_wp_attached_file',$old_attachment);
               }
           }
       }
    }
    
    
    $redirect = site_url('wp-admin/upload.php');
    return $redirect;
}

add_action('bulk_actions-upload', 'slazzer_background_upload_media_remove_bg');
function slazzer_background_upload_media_remove_bg($doaction){
    $doaction[ 'slz-remove-background' ] = 'Remove Background';
    $doaction[ 'slz-restore-background' ] = 'Restore Background';
    return $doaction;
}

function slazzer_rs_upload_from_url( $url, $title ) {
	require_once( ABSPATH . "/wp-load.php");
	require_once( ABSPATH . "/wp-admin/includes/image.php");
	require_once( ABSPATH . "/wp-admin/includes/file.php");
	require_once( ABSPATH . "/wp-admin/includes/media.php");

	$tmp = download_url( $url );
	if ( is_wp_error( $tmp ) ) return false;
	
	$filename = pathinfo($url, PATHINFO_FILENAME);
	$extension = pathinfo($url, PATHINFO_EXTENSION);
	
	if ( ! $extension ) {
		
		$mime = mime_content_type( $tmp );
		$mime = is_string($mime) ? sanitize_mime_type( $mime ) : false;
		
		$mime_extensions = array(
			'text/plain'         => 'txt',
			'text/csv'           => 'csv',
			'application/msword' => 'doc',
			'image/jpg'          => 'jpg',
			'image/jpeg'         => 'jpeg',
			'image/gif'          => 'gif',
			'image/png'          => 'png',
			'video/mp4'          => 'mp4',
		);
		
		if ( isset( $mime_extensions[$mime] ) ) {
			$extension = $mime_extensions[$mime];
		}else{
			// Could not identify extension
			@unlink($tmp);
			return false;
		}
	}
	
	
	$args = array(
		'name' => "$filename.$extension",
		'tmp_name' => $tmp,
	);
	
	$attachment_id = media_handle_sideload( $args, 0, $title);
	
	@unlink($tmp);
	

	if ( is_wp_error($attachment_id) ) return false;

	return (int) $attachment_id;
}