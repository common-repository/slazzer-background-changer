<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_action('wp_ajax_slazzer_get_all_image_ids', 'slazzer_get_all_image_ids');

add_action('wp_ajax_slazzer_nopriv_get_all_image_ids', 'slazzer_get_all_image_ids');



function slazzer_get_all_image_ids(){
    check_ajax_referer('slazzer_setting_nonce', 'nonce');

    $nonce = $_POST['nonce'];
    
    if (!wp_verify_nonce($nonce, 'slazzer_setting_nonce')) {
        $result['msg'] = "Failed to varify nonce value.";
        echo wp_json_encode($result); 
        die();
    }

    global $wpdb;

    $data = array();

    $arr = array();

    $delete_query = $wpdb->prepare('delete from `'.$wpdb->prefix.'wc_slazzer_process_status` where status="0"');

    $wpdb->query($delete_query);

    $slazzer_product_ids = sanitize_text_field($_POST['slazzer_product_ids']);

    $slazzer_processed_image_sql = $wpdb->prepare("SELECT `product_id`, `image_id`, `type` FROM `" . $wpdb->prefix . "wc_slazzer_process_status` WHERE status = 1");

    $slazzer_processed_images_results = $wpdb->get_results(  $slazzer_processed_image_sql );

    if($slazzer_product_ids == 'all'){

        $val = get_posts(array('post_type' => 'product', 'numberposts' => -1, 'post_status' => 'publish', 'update_post_meta_cache' => false, 'fields' => 'ids',));

        $slazzer_product_ids = implode(',', $val);

    }



    $arr_product_ids = explode(",", $slazzer_product_ids);

   

    $is_select_main_image = sanitize_text_field($_POST['slazzer_product_image']);

    $is_select_gallery_image = sanitize_text_field($_POST['slazzer_gallery_image']);

    $is_include_processed_image = sanitize_text_field($_POST['is_slazzer_include_processed_image']);


    if ($is_select_main_image == 'FALSE' && $is_select_gallery_image == 'FALSE') {

        $arr['status'] = false;

        $arr['message'] = "Please choose target image.";

        echo wp_json_encode($arr);

        exit();

    }

    if (count($arr_product_ids) == 0) {

        $arr['status'] = false;

        $arr['message'] = "Product not found.";

        echo wp_json_encode($arr);

        exit();

    }

    $all_gallery_image = true;

    $Slazzer_gallery_image_IDs = get_option('Slazzer_gallery_image_IDs');
    $slazzer_products = get_option('slazzer_products');

    if($slazzer_products == 'specified' && $Slazzer_gallery_image_IDs ){
        $all_gallery_image = false;
        $gallery_process_images = explode(',',$Slazzer_gallery_image_IDs);        
    }


    

    foreach ($arr_product_ids as $product_id) {

        if ($is_select_main_image == 'TRUE') {

            $product_object = wc_get_product($product_id);

            if(isset($product_object) && $product_object != '') {

                $image_id = $product_object->get_image_id();

                if (!empty($image_id)) {
                    $table_name = $wpdb->prefix."wc_slazzer_process_status";
              

                    $product_status_insert_args = array(
                        'product_id' => $product_id,
                        'image_id' => $image_id,
                        'status' => 0,
                        'type' => 'product_image'
                    );


                    

                    if ($is_include_processed_image == 'TRUE') {

                        $wpdb->insert($table_name, $product_status_insert_args);                     

                    } else {

                        if (slazzer_is_image_processed($product_id, $image_id) == false) {

                          $wpdb->insert($table_name, $product_status_insert_args);   

                       }

                    }

                }

                if( $product_object->is_type( 'variable' ) ) {
                    $table_name = $wpdb->prefix."wc_slazzer_process_status";    
                    $variations = $product_object->get_children();
                    
                    foreach($variations as $variation_id){

                        $thumbnail_id = get_post_meta($variation_id,'_thumbnail_id',true);

                        if($thumbnail_id > 0){

                            $product_status_insert_args = array(
                                'product_id' => $variation_id,
                                'image_id' => $thumbnail_id,
                                'status' => 0,
                                'type' => 'product_image'
                            );

                            if ($is_include_processed_image == 'TRUE') {
                                $wpdb->insert($table_name, $product_status_insert_args);   
                            }else{
                                if (slazzer_is_image_processed($variation_id, $thumbnail_id) == false) {
                                    $wpdb->insert($table_name, $product_status_insert_args);  
                                }
                            }
                        }
                    }

                }    

            }

        }



        if ($is_select_gallery_image == 'TRUE') {

            $attachment_ids = get_post_meta($product_id, '_product_image_gallery', true);

            if (!empty($attachment_ids)) {
                
                update_post_meta($product_id,'_product_gallery_original_images',$attachment_ids);
                $attachment_ids = explode(',', $attachment_ids);

                if (is_array($attachment_ids)) {

                    $table_name = $wpdb->prefix."wc_slazzer_process_status";

                    $attachment_args = array(
                        'product_id' => $product_id,
                        'status' => 0,
                        'type' => 'gallery_image'
                    );

                    foreach ($attachment_ids as $attachment_id) {

                        if (!empty($attachment_id)) {

                            if($all_gallery_image == false && !in_array($attachment_id, $gallery_process_images)){
                                continue;
                            }


                            $attachment_args['image_id'] = $attachment_id;

                            if ($is_include_processed_image == 'TRUE') {

                              $wpdb->insert($table_name, $attachment_args); 

                            } else {

                               if (slazzer_is_image_processed($product_id, $attachment_id) == false) {

                                    $wpdb->insert($table_name, $attachment_args); 

                               }

                            }

                        }

                    }

                }

            }

        }

    }





    $sql = $wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . "wc_slazzer_process_status` WHERE status = 0");

    $res = $wpdb->get_results(  $sql );

    
    $arr['status'] = true;

    $arr['count'] = count($res);

   

    echo wp_json_encode($arr);

    exit();

}



function slazzer_is_image_processed($product_id, $image_id)

{

    global $wpdb;
    $table_name = $wpdb->prefix."wc_slazzer_process_history";

    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE product_id= %d AND attach_id = %d", $product_id, $image_id);


    $res = $wpdb->get_results( $sql);

    if ( count($res) > 0){

        return true;

    }

    return false;
}
?>