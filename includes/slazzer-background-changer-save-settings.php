<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_action('wp_ajax_slazzer_save_settings', 'slazzer_save_settings');
add_action('wp_ajax_nopriv_slazzer_save_settings', 'slazzer_save_settings');
function slazzer_save_settings(){
    if (isset($_POST['slazzer_save_settings'])) {
        
        check_ajax_referer('slazzer_setting_nonce', 'nonce');
        $nonce = $_POST['nonce'];
        if (!wp_verify_nonce($nonce, 'slazzer_setting_nonce')) {
            $result['msg'] = "Failed to varify nonce value.";
            echo wp_json_encode($result); 
            die();
        }

        if (!current_user_can('manage_options')) {
            $result['msg'] = "Sorry! You are not authorized to access this page.";
            echo wp_json_encode($result); 
            die();
        }

        $result = array();
        $Slazzer_Include_Processed = sanitize_text_field($_POST['Slazzer_Include_Processed']);
        $slazzer_api_key = sanitize_text_field($_POST['slazzer_live_api_key']);
        $slazzer_products = sanitize_text_field($_POST['slazzer_products']);
        $Slazzer_products_IDs = sanitize_text_field($_POST['Slazzer_products_IDs']);
        if(is_array($Slazzer_products_IDs)){ $Slazzer_products_IDs = implode(",",$Slazzer_products_IDs); }
        $slazzer_main_image = sanitize_text_field($_POST['slazzer_main_image']);
        $slazzer_gallery_image = sanitize_text_field($_POST['slazzer_gallery_image']);
        $background_option = sanitize_text_field($_POST['background_option']);
        $background_color = sanitize_text_field($_POST['background_color']);
        $Slazzer_gallery_image_IDs = sanitize_text_field($_POST['product_gallery_image']);
        $Slazzer_crop_image = sanitize_text_field($_POST['Slazzer_Crop_Image']);

        //image_upload
        if (isset($_FILES['background_image'])) {            
            $profilepicture = sanitize_file_name( $_FILES['background_image']['name'] );
            $slazzer_allowed_types = array( 'image/jpeg', 'image/png' );
            $slazzer_file_type = wp_check_filetype( $profilepicture );
            

            if ( in_array( $slazzer_file_type['type'], $slazzer_allowed_types ) &&  $profilepicture) {
                // looks like everything is OK
                $slazzer_background_file = $_FILES['background_image'];
                $slazzer_background_upload_overrides = array('test_form' => false);
                $slazzer_background_image = wp_handle_upload($slazzer_background_file, $slazzer_background_upload_overrides);         
              
                if (empty($slazzer_background_image['error'])) {                
                    $slazzer_background_image = $slazzer_background_image['url'];
                    update_option('slazzer_background_image', $slazzer_background_image);
                }
            }
        }

        //image_upload
        $Slazzer_Preserve_Resize = sanitize_text_field($_POST['Slazzer_Preserve_Resize']);

        update_option('slazzer_live_key', $slazzer_api_key);
        update_option('slazzer_products', $slazzer_products);
        update_option('Slazzer_products_IDs', $Slazzer_products_IDs);
        update_option('slazzer_main_image', $slazzer_main_image);
        update_option('slazzer_gallery_image', $slazzer_gallery_image);
        update_option('background_option', $background_option);
        update_option('background_color', $background_color);
        update_option('Slazzer_Include_Processed', $Slazzer_Include_Processed);
        update_option('Slazzer_Preserve_Resize', $Slazzer_Preserve_Resize);
        
        update_option('Slazzer_gallery_image_IDs', $Slazzer_gallery_image_IDs);
        update_option('Slazzer_crop_image', $Slazzer_crop_image);
        $response = slazzer_get_remaining_credits($slazzer_api_key,API_ACCOUNT); 
        $result['error'] = 1;  
        if($response == -1){
            $result['msg'] = 'Invalid api key.';
            update_option('invalid_api_key', 'true');
        }else{   
            $remaining_credit = get_option('remaining_credits'); 
            if($remaining_credit > 0){ 
                $result['error'] = 0; 
                $result['remaining_credit'] = 'Remaining Credits: '.$remaining_credit;
                update_option('invalid_api_key', 'false');
            }else{
                $result['msg'] = 'No credit left.Please recharge your credit balance.';
            }
        }

    }
    echo wp_json_encode($result);  
    exit;
}