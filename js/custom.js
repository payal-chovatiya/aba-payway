jQuery( function($) {
                                                                        
        $(document).on('click','input[type=submit][name=woocommerce_checkout_place_order],button[type=submit][name=woocommerce_checkout_place_order]',function() {
            jQuery('.woocommerce-error').remove();
            if ( $('input[type=radio][name=payment_method]:checked').val() == 'aba_payway_aim' ) {
                    
                    var first_name = jQuery('#billing_first_name').val();
                    var last_name = jQuery('#billing_last_name').val();
                    var phone = jQuery('#billing_phone').val();
                    var email = jQuery('#billing_email').val();
                    var address1 = jQuery('#billing_address_1').val();
                    var city = jQuery('#billing_city').val();
                    var state = jQuery('#billing_state').val();
                    var postcode = jQuery('#billing_postcode').val();
                    var aba_api_key = jQuery('#aba_api_key').val();
                    var aba_api_url = jQuery('#aba_api_url').val();
                    
                    
                    if( aba_api_key == '' ) {
                        jQuery('<div class="woocommerce-error">Payway Api key is missing.Please contact administrator to add api key.</div>').insertBefore('#customer_details');
                        $( 'html, body' ).animate( {
				scrollTop: ( jQuery('.woocommerce-error').offset().top - 100 )
			}, 1000 );
                        return false;
                    }
                    if( aba_api_url == '' ) {
                        jQuery('<div class="woocommerce-error">Payway Api url is missing.Please contact Administrator to add api url.</div>').insertBefore('#customer_details');
                        $( 'html, body' ).animate( {
				scrollTop: ( jQuery('.woocommerce-error').offset().top - 100 )
			}, 1000 );
                        return false;
                    }
                    
                    //var transation_id = Math.floor(Math.random() * 1000000000);
                    if( first_name != '' && last_name != '' && phone != '' && email != '' && address1 != '' && city != '' && state != '' && postcode != '' && jQuery('.woocommerce-error').length <= 0 ) {
                        jQuery('input[name=firstname]').val(first_name);
                        jQuery('input[name=lastname]').val(last_name);
                        jQuery('input[name=phone]').val(phone);
                        jQuery('input[name=email]').val(email);
                        //AbaPayway.checkout();
                        //return false;
                    }
                           //---------Close Popup----------------------------
                    jQuery(document).on("click touchstart", ".aba-close", function () {
                        closePopup();
                    });
                    
                        var closePopup = function () {
        var confirmClose = true;

        confirmClose = confirm("Do you want to leave?");


        if (confirmClose == true) {
            
            
            jQuery.ajax({
                    url: frontend_ajax_object.ajaxurl,
                    type: 'post',
                    data: {
                        'action':'aba_check_payment_cancel_order'
                    },
                    success: function( response ) {
                        location.href = jQuery("#cancel_url").val();
                    },
            });
            
            if (jQuery('#aba_main_modal').hide()) {
                jQuery('#aba_webservice').attr('src', "");

                if (jQuery(window).width() < 500) {
                    jQuery('html, body').css({"overflow": "auto", "height": "auto"});
                    //Enable scrool in iOS
                }
            }
        }
    }
            }else{
                 $('button[type=submit][name=woocommerce_checkout_place_order]').attr('onclick','');
            }
        });
} );