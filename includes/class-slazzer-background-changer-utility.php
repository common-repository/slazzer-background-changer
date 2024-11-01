<?php

if ( ! defined( 'ABSPATH' ) ) exit;
const API_ACCOUNT = "https://api.slazzer.com/v2.0/account_balance";

const API_SLAZZER_BACKGROUND_REMOVER = "https://api.slazzer.com/v2.0/remove_image_background";



function slazzer_get_base64_image($image_id){
    $full_path = get_attached_file( $image_id );

    $old_image_file_name = basename($full_path);

    $b64image = base64_encode(file_get_contents($full_path));

    $b64arr = array("b64image" => $b64image, "old_image_file_name" => $old_image_file_name);


    return $b64arr;

}



function slazzer_get_request_body($arr_media_image, $old_image_file_name){

    if (get_option('background_option') == 'remove_background_option') {

        $postRequest = array(
            'source_image_base64' => $arr_media_image['b64image'],
            'client' => 'woocommerce_plugin'
        );

        $old_image_file_name = str_replace(".jpg", ".png", $old_image_file_name);

        $old_image_file_name = str_replace(".JPG", ".png", $old_image_file_name);

        $old_image_file_name = str_replace(".jpeg", ".png", $old_image_file_name);

        $old_image_file_name = str_replace(".JPEG", ".png", $old_image_file_name);



    } elseif (get_option('background_option') == 'background_color_option') {

        $postRequest = array(

            'source_image_base64' => $arr_media_image['b64image'],

            'client' => 'woocommerce_plugin',

            'format' => 'jpg',

            'bg_color_code' => get_option('background_color')

        );

    } elseif (get_option('background_option') == 'background_image_option') {

        $postRequest = array(

            'source_image_base64' => $arr_media_image['b64image'],

            'format' => 'jpg',

            'client' => 'woocommerce_plugin',

            'bg_image_url' => get_option('slazzer_background_image')

        );

    }


    $preview = (get_option('Slazzer_Preserve_Resize') == 'preview')?true : false;

    $crop = (get_option('Slazzer_crop_image') == 'on')?true : false;

    if($preview == true){
        $postRequest['preview'] = $preview;
    }
    
    if($crop == true){
        $postRequest['crop'] = $crop;
    }

    return array('request_body' => $postRequest, 'old_image_file_name' => $old_image_file_name);

}



function slazzer_download_image_from_url($image_url, $product_id, $old_image_file_name)

{

    $upload_dir = wp_upload_dir();



    $image_data = base64_encode($image_url);

    // print_r($image_data );

    $unique_file_name = wp_unique_filename($upload_dir['path'], $old_image_file_name);

    $filename = basename($unique_file_name);



    if (wp_mkdir_p($upload_dir['path'])) {

        $file = $upload_dir['path'] . '/' . $filename;

    } else {

        $file = $upload_dir['basedir'] . '/' . $filename;

    }

    // file_put_contents($file, $image_data);

     $fp = fopen($file, "wb");

    fwrite($fp, $image_url);

    fclose($fp);



    $wp_filetype = wp_check_filetype($filename, null);

    $attachment = array(

        'post_mime_type' => $wp_filetype['type'],

        'post_title' => sanitize_file_name($filename),

        'post_content' => '',

        'post_status' => 'inherit'

    );

    $attach_id = wp_insert_attachment($attachment, $file, $product_id);

    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $attach_data = wp_generate_attachment_metadata($attach_id, $file);

    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;

}

function slazzer_download_image_from_url_basic($image_url, $old_image_file_name){

    $upload_dir = wp_upload_dir();

    $image_data = base64_encode($image_url);


    $unique_file_name = wp_unique_filename($upload_dir['path'], $old_image_file_name);

    $filename = basename($unique_file_name);



    if (wp_mkdir_p($upload_dir['path'])) {

        $file = $upload_dir['path'] . '/' . $filename;
        
        $file_url = $upload_dir['url'] . '/' . $filename;

    } else {

        $file = $upload_dir['basedir'] . '/' . $filename;
        
        $file_url = $upload_dir['baseurl'] . '/' . $filename;

    }

     $fp = fopen($file, "wb");

    fwrite($fp, $image_url);

    fclose($fp);
    
    return $file_url;
}