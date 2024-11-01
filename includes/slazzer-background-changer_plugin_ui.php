<?php
    if ( ! defined( 'ABSPATH' ) ) exit;
    $slazzer_live_key = get_option('slazzer_live_key');
    $slazzer_products = get_option('slazzer_products');
    $Slazzer_products_IDs = get_option('Slazzer_products_IDs');
    $background_option = get_option('background_option');
    $background_color = get_option('background_color');
    $background_image = get_option('slazzer_background_image');
    $Slazzer_Preserve_Resize = get_option('Slazzer_Preserve_Resize');
    $Slazzer_main_image = get_option('Slazzer_main_image');
    $Slazzer_gallery_image = get_option('Slazzer_gallery_image');
    $Slazzer_Include_Processed = get_option("Slazzer_Include_Processed");
    $remaining_credits = get_option('remaining_credits');
    $Slazzer_crop_image = get_option('Slazzer_crop_image');
    $Slazzer_gallery_image_IDs = get_option('Slazzer_gallery_image_IDs');
?>


<style type="text/css" media="screen">
    .lds-ellipsis {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 50px;
    }

    .lds-ellipsis div {
        position: absolute;
        top: 33px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: #017dc7;
        animation-timing-function: cubic-bezier(0, 1, 1, 0);
    }

    .lds-ellipsis div:nth-child(1) {
        left: 8px;
        animation: lds-ellipsis1 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(2) {
        left: 8px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(3) {
        left: 32px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(4) {
        left: 56px;
        animation: lds-ellipsis3 0.6s infinite;
    }

    @keyframes lds-ellipsis1 {
        0% {
            transform: scale(0);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes lds-ellipsis3 {
        0% {
            transform: scale(1);
        }
        100% {
            transform: scale(0);
        }
    }

    @keyframes lds-ellipsis2 {
        0% {
            transform: translate(0, 0);
        }
        100% {
            transform: translate(24px, 0);
        }
    }
</style>


<div class="wc_remove_bg notice notice-error" id="missing_key" style="display: none;">
    <p>Missing API key</p>
</div>
<div class="slazzer-options widefat">
    <img src="<?php echo esc_url(SLAZZER_ROOT_URL.'icon-256x256.png'); ?>" style="height:35px;">
    <h3>Slazzer - Auto Background Remover</h3>
    <br/><br/>

    <div id="slazzerapiwarning" <?php if (!empty($slazzer_live_key)) echo 'style="display:none;"'; else 'style="display:block;"'; ?>>
        <p><span class="bold">WARNING:</span> You have not entered your API key. This plugin will not function without a
            valid API key. Kindly follow the below steps to obtain your API key :
        </p>
        <ol>
            <li>Sign up at <a href="https://www.slazzer.com/signup" target="_blank">https://www.slazzer.com</a>. Ignore
                this step if you have already signed-up.
            </li>
            <li>Go to My Account page by clicking this <a href="https://www.slazzer.com/account"
                                                          target="_blank">link</a>.
            </li>
            <li>Navigate to API key tab.</li>
            <li>Click on the copy button located just beside the API key and paste it in the API key section of this
                plugin.
            </li>
        </ol>
        <p></p>
    </div>
      

    <form action="" method="post">
        <table class="wp-list-table widefat fixed striped">
            <tbody>
            <tr valign="top">
                <th scope="row">
                    <strong>
                        <p class="tooltip">Slazzer Api key</p>
                    </strong>
                </th>
                <td>
                    <input type="text" name="slazzer_live_api_key" id="slazzer_live_api_key" class="slazzer_input"
                           value="<?php if ($slazzer_live_key) echo esc_attr($slazzer_live_key); ?>"
                           placeholder="Slazzer API key">
                    <p class="remaining_credits">
                        <?php 
                            $api_key_status = get_option('invalid_api_key');
                            $api_text_class = ( $api_key_status == 'true' || $remaining_credits <= 0 )?'style="color:red;"':'style="color:green;"';
                        ?>
                        <span id="remaining_credits" <?php echo esc_attr($api_text_class); ?>>
                            <?php
                                if($api_key_status == 'true'){
                                    echo 'Invalid API key';
                                }
                                else{
                                    if ($remaining_credits <= 0) {
                                        echo 'No credit left.Please recharge your credit balance.';
                                    } else {
                                        echo 'Remaining Credits: ' . esc_attr($remaining_credits);
                                    }
                                }
                            ?>

                        </span>
                    </p>
                    <p class="getapikey">You can get your api key from <a href='https://www.slazzer.com/api'>https://www.slazzer.com/api</a>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <strong>
                        <p class="tooltip">Choose target products<span class="tooltiptext">Whether to process all products or only products with provided IDs</span>
                        </p>
                    </strong>
                </th>
                <td>
                    <input type="radio" class="slazzer_input" id="slazzer_products" name="slazzer_products"
                           value="all" <?php if ($slazzer_products == 'all') echo "checked"; ?>><label
                            for="slazzer_products">Remove background from all products</label><br>
                    <input type="radio" id="products_spec" class="slazzer_input" name="slazzer_products"
                           value="specified" <?php if ($slazzer_products == 'specified') echo "checked"; ?>><label
                            for="products_spec">Remove background only from specified products </label><span
                            class="desc">(IDs of products to process: comma separated or ranges, i.e. 4,15.18)</span>
                    
                           
                    <select class="slazzer_input text-full-width" <?php if ($slazzer_products == 'specified') echo 'style="display: block;"'; else echo 'style="display: none;"'; ?> id="Slazzer_products_IDs" name="Slazzer_products_IDs" style="width:100%;" multiple="multiple">
                       <?php  
                            $args = array(
                                'post_type' => 'product',
                                'posts_per_page' => '-1'
                            );
                            $all_ids = array();
                            if($Slazzer_products_IDs){
                                $all_ids = explode(',',$Slazzer_products_IDs);    
                            }
                            
                            $query = new wp_query($args);
                            if($query->have_posts()){
                                while($query->have_posts()):$query->the_post();
                                    $selected = (in_array(get_the_id(), $all_ids))?'Selected="selected"':'';
                                    echo '<option value="'. esc_attr(get_the_id()).'" '.esc_attr($selected).'>'. esc_html(get_the_title()).'</option>';
                                endwhile;
                            }
                       ?> 
                    </select>       
                    <p class="slazzer_product_IDs_missind">
                        <span id="slazzer_product_IDs_missind" class="product_id_error">
                            Products IDs missing.
                        </span>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <strong>
                        <p class="tooltip">Choose target images ?<span class="tooltiptext">"Main image" - processes only main image of a product, "Product gallery" - processes only product gallery images.(You can get all the gallery image id's inside inventory tab of a product.) Check both to process all images of a product.</span>
                        </p>
                    </strong>
                </th>
                <td>
                    <input type="checkbox" class="slazzer_input" id="slazzer_target_product_image"
                           name="Slazzer_main_image"
                           value="1" <?php if ($Slazzer_main_image == 1) echo "checked"; ?>><label for="slazzer_target_product_image">Main
                        image</label><br>
                    <input type="checkbox" id="slazzer_target_gallery_image" class="slazzer_input"
                           name="Slazzer_gallery_image"
                           value="1" <?php if ($Slazzer_gallery_image == 1) echo "checked"; ?>><label
                            for="slazzer_target_gallery_image">Product gallery</label><br>
                    <?php 
                        $hide_class = ($slazzer_products == 'specified' && $Slazzer_gallery_image == 1)?'':'hide';
                    ?>        
                    <div class="slazzer_gallery_image_ids <?php echo  esc_attr($hide_class); ?>">        
                        <input type="text" name="Slazzer_Product_Gallery_Image_Ids" id="Slazzer_Product_Gallery_Image_Ids" value="<?php echo  esc_attr($Slazzer_gallery_image_IDs); ?>" class="slazzer_input text-full-width" >
                        <br/>
                        <label>(IDs of products gallery images to process: comma separated or ranges, i.e. 4,15.18 or leave this box blank for all gallery images.)</label>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <strong>
                        <p class="tooltip">Include processed images ?<span class="tooltiptext">By default, plugin processes each image only once. If checked, plugin will not skip earlier processed images and will overwrite them</span>
                        </p>
                    </strong>
                </th>
                <td>
                    <input type="checkbox" class="slazzer_input" name="Slazzer_Include_Processed"
                           id="Slazzer_Include_Processed"
                           value="1" <?php if ($Slazzer_Include_Processed == 1) echo "checked"; ?>>
                </td>
            </tr>
            <tr valign="top" >
                <th scope="row">
                    <strong>
                        <p class="tooltip">Image Size <span class="tooltiptext">Maximum output image resolution: "Auto" = Use highest available resolution (based on image size and available credits), "Preview" = Resize image to 0.25 megapixels (e.g. 625×400 pixels), "Full" = Use original image resolution, up to 10 megapixels (e.g. 4000x2500). </span>
                        </p>
                    </strong>
                </th>
                <td>
                    <input type="radio" id="out_preview" class="slazzer_input" name="Slazzer_Preserve_Resize"
                           value="preview" <?php if ($Slazzer_Preserve_Resize == 'preview') echo "checked"; ?>><label
                            for="out_preview">Preview</label><br>
                    <input type="radio" id="out_full" class="slazzer_input" name="Slazzer_Preserve_Resize"
                           value="full" <?php if ($Slazzer_Preserve_Resize == 'full') echo "checked"; ?>><label
                            for="out_full">Full</label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <strong>
                        <p class="tooltip">Make new background <span class="tooltiptext">"Transparent" - processed images will have transarent background, "Color" - sets chosen color as new background of processed images, "Custom image" - sets your image as new background of processed images</span>
                        </p>
                    </strong>
                </th>
                <td>
                    <input type="radio" class="slazzer_input" name="background_option"
                           value="remove_background_option" <?php if ($background_option == 'remove_background_option') echo "checked"; ?> id="transparent"/>
                    <label for="transparent">Transparent</label>
                    <br/>
                    <input type="radio" name="background_option" class="slazzer_input"
                           value="background_color_option" <?php if ($background_option == 'background_color_option') echo "checked"; ?> id="color"/>
                    <label for="color">Color</label>
                    <br/>
                    <p id="background_color_p" <?php if ($background_option == 'background_color_option') echo 'style="display:block;"'; else echo 'style="display:none;"'; ?>>
                        <input type="color" class="slazzer_input" name="background_color"
                               id="background_color" <?php $background_color = esc_attr($background_color);
                               if ($background_color) echo 'value="' . $background_color . '"'; ?>>
                        Select Background Color</p>
                        <input type="radio" name="background_option" class="slazzer_input"
                               value="background_image_option" <?php if ($background_option == 'background_image_option') echo "checked"; ?>  id="custom_image"/>
                        <label for="custom_image">Custom image</label>
                        <br/>
                    <p>
                        <input type="file" id="background_image_file"
                               class="slazzer_input" <?php if ($background_option == 'background_image_option') echo 'style="display:block;"'; else echo 'style="display:none;"'; ?>>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <strong>
                        <p class="tooltip">Crop Image</p>
                    </strong>
                </th>
                <td>
                    <span>OFF</span>
                    <label class="switch">
                      <input type="checkbox" name="Slazzer_Crop_Image" id="Slazzer_Crop_Image" <?php echo ($Slazzer_crop_image == 'on')?'checked':''; ?>>
                      <span class="slider round"></span>
                    </label>
                    <span>ON</span>
                </td>
            </tr>
            </tbody>
        </table>
        <!--new table-->
        <div id="slazzer-loader" style="display: none">
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <br>
            <a href="javascript:void(0)" class="stopajaxqueue">Stop process</a>
        </div>
        <!--        <div id="slazzer-loader" style="display: none;"></div>-->
        <div id="slazzer_ui_notificatuion" style="color: red;"></div>

        <br>
        <div class="remaining_credits_ajax"></div>
        <br>
        <div class="remaining_images_ajax"></div>
        <br>
        <p id="slazzer_change_check" style="display:none;color:tomato;">Note : You have made changes please save
            settings first.</p>
        <p id="success_msg" style="display:none;color:green;"></p>
        <input type="button" name="slazzer_save_settings" id="slazzer_save_settings" value="Save changes">
        <input type="button" name="slazzer_start_background_removal" id="slazzer_start_background_removal"
               value="Start process">

        <input type="button" name="slazzer_restore_backup" id="slazzer_restore_backup" value="Undo changes">
        <input type="button" name="slazzer_delete_backup" id="slazzer_delete_backup" value="Remove backup">

        <input type="hidden" name="tostopforeach" class="tostopforeach" value="nostop">

        <input type="text" value="1" id="slazzer_input" style="display:none;">
        <?php wp_nonce_field('slazzer_setting_nonce', 'slazzer-background-changer-nonce'); ?>
    </form>
</div>
<hr>
<lable>
    <h3><i>contact us for any support or bug reporting: support@slazzer.com</i></h3>
</lable>