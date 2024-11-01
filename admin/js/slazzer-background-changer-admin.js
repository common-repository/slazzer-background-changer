    function tempAlert(msg, duration) {

        var el = document.createElement("div");

        el.setAttribute("style", "position:absolute;top:50%;left:30%;font-size:50px;height:70px; padding:20px; width:500px;background-color:white;");

        el.innerHTML = msg;

        setTimeout(function () {

            el.parentNode.removeChild(el);

        }, duration);

        document.body.appendChild(el);

    }



    jQuery(document).ready(function ($){

        var thisajaxquery;

        jQuery.ajaxQ = (function () {

            var id = 0,

                Q = {};



            jQuery(document).ajaxSend(function (e, jqx) {

                jqx._id = ++id;

                Q[jqx._id] = jqx;

            });

            jQuery(document).ajaxComplete(function (e, jqx) {

                delete Q[jqx._id];

            });



            return {

                abortAll: function () {

                    var r = [];

                    jQuery.each(Q, function (i, jqx) {

                        r.push(jqx._id);

                        jqx.abort();

                    });

                    return r;

                }

            };



        })();





        jQuery('.stopajaxqueue').click(function () {

            jQuery(':input[type="button"]').prop('disabled', false);

            jQuery(':input[type="button"]').css('cursor', 'pointer');

            jQuery("#slazzer-loader").hide();

            location.reload();

        });





 





    jQuery("#slazzer_live_api_key").on('change', function () {

        if (jQuery(this).val() == "")

            jQuery("#slazzerapiwarning").show();

        else

            jQuery("#slazzerapiwarning").hide()

    });

    

    jQuery(".slazzer_input").on('change', function () {

        jQuery("#slazzer_input").val("0");

        jQuery("#success_msg").hide();

        jQuery("#slazzer_change_check").show();

    });

    

    jQuery("input[name='slazzer_products']").change(function () {

        if (jQuery("input[name='slazzer_products']:checked").val() == 'specified')

            jQuery("#s2id_Slazzer_products_IDs").show();

        else

            jQuery("#s2id_Slazzer_products_IDs").hide();

    });

    

    jQuery("#slazzer_save_settings").on('click', function () {

        jQuery("#success_msg").hide();

        var slazzer_live_api_key = jQuery('#slazzer_live_api_key').val();

        if (jQuery('#slazzer_live_api_key').val() == "") {

            jQuery("#missing_key").show();

            jQuery('html, body').animate({

                scrollTop: jQuery("#missing_key").offset().top

            }, 1000);

            return false;

        } else {

            jQuery("#missing_key").hide();

        }

        var slazzer_products = jQuery("input[name='slazzer_products']:checked").val();

        var slazzer_products = jQuery("input[name='slazzer_products']:checked").val();

        if (slazzer_products == 'specified') {

            var slazzer_product_ids = jQuery("#Slazzer_products_IDs").val();

            if (slazzer_product_ids == "") {

                jQuery("#slazzer_product_IDs_missind").show();

                jQuery('html, body').animate({

                    scrollTop: jQuery("#slazzer_product_IDs_missind").offset().top

                }, 1000);

                return false;

            }



        }else{

            jQuery("#slazzer_product_IDs_missind").hide();

        }

        jQuery("#slazzer-loader").show();

        

        var Slazzer_Include_Processed = jQuery("input[name='Slazzer_Include_Processed']:checked").val();   

        var Slazzer_products_IDs = jQuery("#Slazzer_products_IDs").val();

        var slazzer_main_image = jQuery("input[name='Slazzer_main_image']:checked").val();

        var slazzer_gallery_image = jQuery("input[name='Slazzer_gallery_image']:checked").val();

        var Slazzer_Preserve_Resize = jQuery("input[name='Slazzer_Preserve_Resize']:checked").val();

        var background_option = jQuery("input[name='background_option']:checked").val();

        var background_color = jQuery('#background_color').val();

        var background_image = jQuery('#background_image_file')[0].files[0];

        var product_gallery_image_ids = jQuery('#Slazzer_Product_Gallery_Image_Ids').val();

        var Slazzer_Crop_Image = jQuery("input[name='Slazzer_Crop_Image']:checked").val(); 

        var slazzer_settings_nonce = $('#slazzer-background-changer-nonce').val();
        

        var form_data = new FormData();

        form_data.append('slazzer_save_settings', 1);

        form_data.append('slazzer_live_api_key', slazzer_live_api_key);

        form_data.append('slazzer_products', slazzer_products);

        form_data.append('Slazzer_Include_Processed', Slazzer_Include_Processed);

        form_data.append('Slazzer_products_IDs', Slazzer_products_IDs);

        form_data.append('slazzer_main_image', slazzer_main_image);

        form_data.append('slazzer_gallery_image', slazzer_gallery_image);

        form_data.append('Slazzer_Preserve_Resize', Slazzer_Preserve_Resize);

        form_data.append('background_option', background_option);

        form_data.append('background_color', background_color);

        form_data.append('background_image', background_image);

        form_data.append('product_gallery_image', product_gallery_image_ids);

        form_data.append('Slazzer_Crop_Image', Slazzer_Crop_Image);

        form_data.append('action', 'slazzer_save_settings');

        form_data.append('nonce', slazzer_settings_nonce);
        

        jQuery.ajax({

            url: ajaxurl,

            dataType: 'text',

            cache: false,

            contentType: false,

            processData: false,

            data: form_data,

            type: 'post',

            success: function (data) {

                jQuery(':input[type="button"]').prop('disabled', false);

                jQuery(':input[type="button"]').css('cursor', 'pointer');

                jQuery("#slazzer-loader").hide();

                jQuery("#success_msg").text("Settings saved successfully");

                var response = eval('(' +data+ ')');

                //console.log(response);
                if (response.error == 1) {

                    jQuery("#remaining_credits").text(response.msg);

                    jQuery("#remaining_credits").css('color', 'red');

                } else {

                    jQuery("#remaining_credits").text(response.remaining_credit);

                    jQuery("#remaining_credits").css('color', 'green');

                }



                jQuery(".remaining_credits").show();

                jQuery("#success_msg").css('color', 'green');

                jQuery("#slazzer_change_check").hide();

                jQuery("#success_msg").show();

                //location.reload();

            },
            error: function (jqXHR, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 403) {
                    msg = 'Nonce varification failed. [403]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
            }

        });

    });



    jQuery("#slazzer_start_background_removal").on('click', function () {

        jQuery(':input[type="button"]').prop('disabled', true);

        jQuery(':input[type="button"]').css('cursor', 'not-allowed');

        jQuery('.remaining_credits_ajax').html('');

        jQuery('.tostopforeach').val('nostop');

        if (jQuery('#slazzer_live_api_key').val() == "") {

            jQuery("#missing_key").show();

            jQuery('html, body').animate({

                scrollTop: jQuery("#missing_key").offset().top

            }, 1000);

            return false;

        } else {

            jQuery("#missing_key").hide();

        }



        jQuery(':input[type="button"]').prop('disabled', true);

        jQuery(':input[type="button"]').css('cursor', 'not-allowed');

        jQuery("#slazzer-loader").show();

        jQuery("#success_msg").hide();

        var remaining_credits = parseInt(jQuery("#remaining_credits").text());

        var product_type = jQuery("input[name='slazzer_products']:checked").val();

        if (product_type == 'specified') {

            var slazzer_product_ids = jQuery("#Slazzer_products_IDs").val();

        } else {

            var slazzer_product_ids = "all";

        }



        var slazzer_product_image = jQuery('#slazzer_target_product_image').prop('checked');

        var slazzer_gallery_image = jQuery('#slazzer_target_gallery_image').prop('checked');

        var is_slazzer_include_processed_image = jQuery('#Slazzer_Include_Processed').prop('checked');

        var is_preview_mode_active = jQuery('#Slazzer_Preview_Mode').prop('checked');

        var nonce = jQuery('#slazzer-background-changer-nonce').val();



        if (!slazzer_product_image && !slazzer_gallery_image) {

            jQuery("#slazzer-loader").hide();

            alert('Please select main image or gallery image or both')



            jQuery(':input[type="button"]').prop('disabled', false);

            jQuery(':input[type="button"]').css('cursor', 'pointer');

            return false;

        }

        if (slazzer_product_ids == '') {

            jQuery("#slazzer-loader").hide();

            alert('Product not found.');

            jQuery(':input[type="button"]').prop('disabled', false);

            jQuery(':input[type="button"]').css('cursor', 'pointer');

            return false;

        }



        /* to get media ids */

        var formdata = new FormData();

        formdata.append('slazzer_product_ids', slazzer_product_ids);

        formdata.append('action', 'slazzer_get_all_image_ids');

        formdata.append('slazzer_product_image', slazzer_product_image ? 'TRUE' : 'FALSE');

        formdata.append('slazzer_gallery_image', slazzer_gallery_image ? 'TRUE' : 'FALSE');

        formdata.append('is_slazzer_include_processed_image', is_slazzer_include_processed_image ? 'TRUE' : 'FALSE');

        formdata.append('is_preview_mode_active', is_preview_mode_active ? 'TRUE' : 'FALSE');

        formdata.append('nonce', nonce);



        var ajaxurl = apAjax.ajaxurl;

        jQuery.ajax({

            url: ajaxurl,

            type: "POST",

            data: formdata,

            dataType: 'json',

            contentType: false,

            cache: false,

            processData: false,

            timeout: 180000,

            success: function (data) {

                if (data.status == false) {

                    jQuery("#slazzer-loader").hide();

                    jQuery(':input[type="button"]').prop('disabled', false);

                    jQuery(':input[type="button"]').css('cursor', 'pointer');

                    alert(data.message);

                    return false;

                }

                var total_image_count = data.count;

                jQuery('.remaining_images_ajax').html('<span>Remaining imgaes in queue ' + total_image_count + '</span>');



                if (total_image_count > 0) {

                    call_ajax_sync();

                } else {

                    jQuery(':input[type="button"]').prop('disabled', false);

                    jQuery(':input[type="button"]').css('cursor', 'pointer');

                    jQuery("#slazzer-loader").hide();

                    alert('Background change process completed successfully.');

                    location.reload();

                }

            }, error: function (jqXHR, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
                call_ajax_sync();
            }

        });



    });



    function call_ajax_sync() {

        var is_include_processed_image = jQuery("input[name='Slazzer_Include_Processed']:checked").val();

        var form_data_processsingleimage = new FormData();

        form_data_processsingleimage.append('is_include_processed_image', is_include_processed_image);

        form_data_processsingleimage.append('action', 'slazzer_start_processing_single_image');



        thisajaxquery = jQuery.ajax({

                url: ajaxurl,

                dataType: 'json',

                cache: false,

                contentType: false,

                processData: false,

                data: form_data_processsingleimage,

                type: 'post',

                success: function (res) {

                    //console.log(res);


                    var count_total_images = res.count;

                    if (count_total_images == 0) {
                
                       // jQuery("#slazzer-loader").hide();

                        //var thismessage = res.message;

                       // jQuery(':input[type="button"]').prop('disabled', false);

                        //jQuery(':input[type="button"]').css('cursor', 'pointer');
                        alert('Background change process completed successfully.');
                        
                        location.reload();

                    }else{

                        jQuery('.remaining_images_ajax').html('<span>Remaining imgaes in queue ' + count_total_images + '</span>');

                        jQuery("#remaining_credits").text("Remaining Credits: " + res.remaining_credits);

                        jQuery("#slazzer_restore_backup").show();

                        jQuery("#slazzer_delete_backup").show();

                        call_ajax_sync();

                    }

                }

            });

    }



    jQuery("#slazzer_restore_backup").on('click', function () {

        jQuery(':input[type="button"]').prop('disabled', true);

        jQuery(':input[type="button"]').css('cursor', 'not-allowed');



        jQuery("#slazzer-loader").show();

        jQuery("#success_msg").hide();

        var form_data = new FormData();

        form_data.append('slazzer_restore_backup', 1);

        form_data.append('action', 'slazzer_restore_backup');

        jQuery.ajax({

            url: ajaxurl,

            dataType: 'text', // what to expect back from the PHP script, if anything

            cache: false,

            contentType: false,

            processData: false,

            data: form_data,

            type: 'post',

            success: function (data) {



                jQuery(':input[type="button"]').prop('disabled', false);

                jQuery(':input[type="button"]').css('cursor', 'pointer');

                jQuery("#slazzer-loader").hide();

                jQuery("#slazzer_restore_backup").hide();

                jQuery("#slazzer_delete_backup").hide();

                jQuery("#success_msg").text("Restore backup is done successfully");

                jQuery("#success_msg").show();

                //location.reload();

            }

        });

    });





    jQuery("#slazzer_delete_backup").on('click', function () {

        jQuery("#slazzer-loader").show();

        jQuery("#success_msg").hide();

        var form_data = new FormData();

        form_data.append('slazzer_delete_backup', 1);

        form_data.append('action', 'slazzer_delete_backup');

        jQuery.ajax({

            url: ajaxurl,

            dataType: 'text', // what to expect back from the PHP script, if anything

            cache: false,

            contentType: false,

            processData: false,

            data: form_data,

            type: 'post',

            success: function (data) {

                jQuery(':input[type="button"]').prop('disabled', false);

                jQuery(':input[type="button"]').css('cursor', 'pointer');

                jQuery("#slazzer-loader").hide();

                jQuery("#success_msg").text("Delete backup is done successfully");

                jQuery("#success_msg").show();

            }

        });

    });





    jQuery("#startpreview").on('click', function () {

        if (jQuery("#Slazzer_TestProduct").val() == "") {

            alert("Please fill product id to preview.");

            return false;

        }



        jQuery(':input[type="button"]').prop('disabled', true);

        jQuery(':input[type="button"]').css('cursor', 'not-allowed');



        jQuery("#slazzer-loader").show();

        jQuery("#previewresult").hide();

        var preview_pro_id = jQuery("#Slazzer_TestProduct").val();

        var ajaxurl = apAjax.ajaxurl;

        var pro_id = jQuery("#pro_id").val();

        var background_color = '';

        var background_image = '';

        var background_option = jQuery("input[name='background_option']").filter(':checked').val();

        if (jQuery("input[name='background_option']").filter(':checked').val() == 'remove_background_option') {



        } else if (jQuery("input[name='background_option']").filter(':checked').val() == 'background_color_option') {

            background_color = jQuery("#background_color").val();

        } else if (jQuery("input[name='background_option']").filter(':checked').val() == 'background_image_option') {

            background_image = jQuery("#background_image").val();

        }

        //alert(background_image);

        var form_data = new FormData();

        form_data.append('pro_id', preview_pro_id);

        form_data.append('background_option', background_option);

        form_data.append('background_color', background_color);

        form_data.append('background_image', background_image);

        form_data.append('action', 'slazzer_preview_image');

        jQuery.ajax({

            url: ajaxurl,

            dataType: 'text', // what to expect back from the PHP script, if anything

            cache: false,

            contentType: false,

            processData: false,

            data: form_data,

            type: 'post',

            success: function (data) {

                var res = data.split(",");

                //console.log(res[0]);

                jQuery("#startpreview").val('Preview');

                jQuery(".img-before-remove-bg").attr('src', res[1]);

                jQuery(".img-after-remove-bg").attr('src', res[0]);



                jQuery(':input[type="button"]').prop('disabled', false);

                jQuery(':input[type="button"]').css('cursor', 'pointer');





                jQuery("#slazzer-loader").hide();

                jQuery("#previewresult").show();

                //location.reload();

            }

        });

    });

    

    

    jQuery("#test_mode").on('change', function () {

        if (this.checked) {

            jQuery("#slazzer_test_api_key").show();

            jQuery("#mode").val('test');

            jQuery("#slazzer_live_api_key").hide();

        } else {

            jQuery("#slazzer_test_api_key").hide();

            jQuery("#mode").val('live');

            jQuery("#slazzer_live_api_key").show();

        }

    });

    

    

    jQuery("#slazzer_submit").on('click', function () {

        if (!jQuery("#mode").val())

            jQuery("#mode").val('live');

    });

    

    

    jQuery("input[name='background_option']").on('change', function () {

        if (jQuery(this).filter(':checked').val() == 'remove_background_option') {

            jQuery("#background_color_p").hide();

            jQuery("#background_image").hide();

            jQuery("#background_image_file").hide();

        } else if (jQuery(this).filter(':checked').val() == 'background_color_option') {

            jQuery("#background_color_p").show();

            jQuery("#background_image").hide();

            jQuery("#background_image_file").hide();

        } else if (jQuery(this).filter(':checked').val() == 'background_image_option') {

            jQuery("#background_color_p").hide();

            jQuery("#background_image").show();

            jQuery("#background_image_file").show();

        }

    });



    jQuery("#background_submit").on('click', function () {

        if (jQuery("input[name='background_option']").filter(':checked').val()) {

            jQuery("#background_submit").val('Updating...');

            var ajaxurl = apAjax.ajaxurl;

            var img_url = jQuery("#image_url").val();

            var pro_id = jQuery("#pro_id").val();

            var background_color = '';

            var background_image = '';

            var background_option = jQuery("input[name='background_option']").filter(':checked').val();

            if (jQuery("input[name='background_option']").filter(':checked').val() == 'remove_background_option') {



            } else if (jQuery("input[name='background_option']").filter(':checked').val() == 'background_color_option') {

                background_color = jQuery("#background_color").val();

            } else if (jQuery("input[name='background_option']").filter(':checked').val() == 'background_image_option') {

                background_image = jQuery("#background_image").val();

            }

            //alert(background_color);

            var form_data = new FormData();

            form_data.append('pro_id', pro_id);

            form_data.append('img_url', img_url);

            form_data.append('background_option', background_option);

            form_data.append('background_color', background_color);

            form_data.append('background_image', background_image);

            form_data.append('action', 'slazzer_update_image');

            jQuery.ajax({

                url: ajaxurl,

                dataType: 'text', // what to expect back from the PHP script, if anything

                cache: false,

                contentType: false,

                processData: false,

                data: form_data,

                type: 'post',

                success: function (data) {

                    //alert(data);

                    location.reload();

                }

            });

        }

        return false;

    });

        $('#slazzer_target_gallery_image').click(function(){       
           var product_type = $('input[name="slazzer_products"]:checked').val();
           if($(this).prop("checked") == true && product_type == 'specified'){
                $('.slazzer_gallery_image_ids').show();
           }else{
                $('.slazzer_gallery_image_ids').hide();
           }
        });

        $('input[name="slazzer_products"]').change(function(){
            var radio_val = $(this).val();
            if($('#slazzer_target_gallery_image').prop("checked") == true && radio_val == 'specified'){
                $('.slazzer_gallery_image_ids').show();
            }else{
                $('.slazzer_gallery_image_ids').hide();
            }
        });
        
        $('#Slazzer_products_IDs').select2();
         

    

    });