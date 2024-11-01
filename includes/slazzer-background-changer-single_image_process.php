<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_action('wp_ajax_slazzer_start_processing_single_image', 'slazzer_start_processing_single_image');

add_action('wp_ajax_slazzer_nopriv_start_processing_single_image', 'slazzer_start_processing_single_image');



function slazzer_start_processing_single_image(){


    global $wpdb;

    $slazzer_api_key = get_option('slazzer_live_key');

    $sql = $wpdb->prepare("SELECT * FROM `".$wpdb->prefix."wc_slazzer_process_status` WHERE `status` = 0 ORDER BY id ASC LIMIT 1");


    $res = $wpdb->get_results( $sql );


    if(count($res) > 0){

        $product_id = $res[0]->product_id;

        $image_id = $res[0]->image_id;

        $type = $res[0]->type;

        $id = $res[0]->id;


        update_option('show_backup', 1);



        $arr_media_image = slazzer_get_base64_image($image_id);

        $old_image_file_name = $arr_media_image['old_image_file_name'];



        $arr_request_body = slazzer_get_request_body($arr_media_image,$old_image_file_name);

        

        $old_image_file_name = $arr_request_body['old_image_file_name'];

        
      
        $arr_response_body = slazzer_remove_image_background($arr_request_body,$slazzer_api_key);

    
        if ($arr_response_body['status']) {



            $sql = $wpdb->prepare("UPDATE `".$wpdb->prefix."wc_slazzer_process_status` SET `status` = 1 WHERE id= %s", $id);

           

            $wpdb->query($sql);

            

            $image_url = $arr_response_body['api_response'];



            $attach_id = slazzer_download_image_from_url($image_url, $product_id,$old_image_file_name);




            if ($image_id) {

                

                 $can_restore_or_delete = 1;

                    // $sql = "INSERT INTO `".$wpdb->prefix."wc_slazzer_process_history` (image_id, product_id, attach_id, can_restore_or_delete) VALUES (".$image_id.", ".$product_id.", ".$attach_id.", ".$can_restore_or_delete.")";


                    $args = array(
                        'image_id' => $image_id,
                        'product_id' => $product_id,
                        'attach_id' => $attach_id,
                        'can_restore_or_delete' => $can_restore_or_delete
                    );
                

                    $wpdb->insert($wpdb->prefix."wc_slazzer_process_history",$args);                

            }



            if ($type == "product_image")

            {

                update_post_meta($product_id, '_thumbnail_id_backup', $image_id);

                set_post_thumbnail($product_id, $attach_id);

            }



            if ($type == "gallery_image"){


                // first take backup
                $string_checkalreadybackup = get_post_meta($product_id, '_product_image_gallery_backup', true);

                $array_of_gallery_images_forbackup = array();



                if (!empty($string_checkalreadybackup)) {

                    $array_of_gallery_images_forbackup = explode(",", $string_checkalreadybackup);

                    if (!in_array($image_id, $array_of_gallery_images_forbackup)) {

                        array_push($array_of_gallery_images_forbackup, $image_id);

                        $newstring_forbackup = implode(',', $array_of_gallery_images_forbackup);

                        update_post_meta($product_id, '_product_image_gallery_backup', $newstring_forbackup);

                    }

                } else {

                    update_post_meta($product_id, '_product_image_gallery_backup', $image_id);

                }



                $string_gallery_images = get_post_meta($product_id, '_product_image_gallery', true);

                $array_of_gallery_images = explode(",", $string_gallery_images);

                $array_of_gallery_images_new = array_replace($array_of_gallery_images,

                    array_fill_keys(

                        array_keys($array_of_gallery_images, $image_id),

                        $attach_id

                    )

                );



                $newstring = implode(',', $array_of_gallery_images_new);

                update_post_meta($product_id, '_product_image_gallery', $newstring);



            }

            slazzer_get_remaining_credits($slazzer_api_key,API_ACCOUNT);              

            $remaining_credits = get_option('remaining_credits');           

            $sql = $wpdb->prepare("SELECT * FROM `".$wpdb->prefix."wc_slazzer_process_status` WHERE `status` = 0");

            $res = $wpdb->get_results($sql );

            $img_count = count($res);

            echo wp_json_encode(array('status' => true, 'message' => 'success', 'count' => $img_count,'remaining_credits' => $remaining_credits));

        } else {
            $sql = $wpdb->prepare("UPDATE `".$wpdb->prefix."wc_slazzer_process_status` SET `status` = 1 WHERE id= %s", $id);
            $wpdb->query($sql);
            
            $sql = $wpdb->prepare("SELECT * FROM `".$wpdb->prefix."wc_slazzer_process_status` WHERE `status` = 0");
            $res = $wpdb->get_results($sql );
            $img_count = count($res);
            
            
            $remaining_credits = get_option('remaining_credits');
            echo wp_json_encode(array('status' => false, 'message' => 'Unable to process this image.','count' => $img_count,'remaining_credits' => $remaining_credits));

        }

        exit;

    }else{
        $remaining_credits = get_option('remaining_credits');  
        echo wp_json_encode(array('status' => false, 'message' => 'Done', 'count' => 0,'remaining_credits' => $remaining_credits ));

         exit;

    }

    

}



?>