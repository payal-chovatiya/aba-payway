<?php 
/*
Plugin Name: ABA Payway WooCommerce Payment Gateway
Plugin URI: http://www.shaligraminfotech.com/
Description: WooCommerce ABA payaway wopcpmmerce payment getway integration.
Author: Shaligram Infotech
Author URI: http://www.shaligraminfotech.com/
Version: 1.0
*/

if ( ! defined( 'ABSPATH' ) )
	die( "Can't load this file directly" );

if ( in_array( 
    'woocommerce/woocommerce.php', 
    apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) 
  ) 
);
define('ABA_PAYMENT_PLUGIN_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );

add_action( 'plugins_loaded', 'aba_payway_aim_init', 0 );
function aba_payway_aim_init() {
    //if condition use to do nothin while WooCommerce is not installed
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
	include_once( 'PayWayApiCheckout.php' );
	
	// class add it too WooCommerce
	add_filter( 'woocommerce_payment_gateways', 'add_aba_payway_gateway' );
	function add_aba_payway_gateway( $methods ) {
		$methods[] = 'aba_PAYWAY_AIM';
		return $methods;
	}
}
// Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'aba_payway_aim_action_links' );
function aba_payway_aim_action_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'aba-payway-aim' ) . '</a>',
	);
	return array_merge( $plugin_links, $links );
}

//Add Payway form in checkout page
add_action('woocommerce_after_checkout_form','aba_woocommerce_after_checkout_form',10,1);
function aba_woocommerce_after_checkout_form(){
    global $woocommerce;
    $cart = WC()->cart;
    $cart_url = get_permalink( wc_get_page_id( 'cart' ) );
    $currency_option = get_option('woocommerce_currency');
    $currency = get_woocommerce_currency_symbol($currency_option);
    $cart_total = strip_tags($cart->get_total());
    $amount = str_replace($currency,'',$cart_total);
    //$amount = floatval( preg_replace( '#[^\d.]#', '', $cart->get_total() ) );
    $transactionId = rand();
    $_SESSION['aba_transaction_id'] = $transactionId;
    $items_result = $woocommerce->cart->get_cart();
    
    $key = 0;
    if( !empty($items_result) ){ 
        foreach( $items_result as $t_key => $values ) { 
            $_product =  wc_get_product( $values['data']->get_id());         
            $price = get_post_meta($values['product_id'] , '_price', true);
            $item[$key]['name'] = $_product->get_title();
            $item[$key]['quantity'] = $values['quantity'];
            $item[$key]['price'] = $price*$values['quantity'];
            $key++;
        }
        $items = base64_encode(json_encode($item));
    }
    ?>
        <div id="aba_main_modal" class="aba-modal">
            <div class="aba-modal-content">
                <!-- Include PHP class -->
                <form method="POST" target="aba_webservice" action="<?php echo aba_PAYWAY_AIM::getApiUrl(); ?>" id="aba_merchant_request" target="aba_webservice" name="aba_merchant_request">
                    <input type="hidden" name="hash" value="<?php echo aba_PAYWAY_AIM::getHash($transactionId, $amount, $items); ?>" id="hash"/>
                    <input type="hidden" name="tran_id" value="<?php echo $transactionId; ?>" id="tran_id"/>
                    <input type="hidden" name="amount" value="<?php echo $amount; ?>" />
                    <input type="hidden" name="firstname" value="" />
                    <input type="hidden" name="lastname" value="" />
                    <input type="hidden" name="phone" value=""  />
                    <input type="hidden" name="email" value="" />
                    <input type="hidden" name="cancel_url" value="<?php echo $cart_url; ?>" id="cancel_url">
                    <input type="hidden" name="return_url" value="<?php echo base64_encode(ABA_PAYMENT_PLUGIN_PATH.'payment_response.php'); ?>" id="return_url">
                    <input type="hidden" name="return_params" value="json" id="return_params">  
                    <input type="hidden" name="items" value="<?php echo $items; ?>" id="items"/>
                </form>
            </div>
        </div>
        <input type="hidden" name="aba_api_key" id="aba_api_key" value="<?php echo aba_PAYWAY_AIM::getApiKey(); ?>">
        <input type="hidden" name="aba_api_url" id="aba_api_url" value="<?php echo aba_PAYWAY_AIM::getApiUrl(); ?>">
<?php
}


add_action( 'wp_enqueue_scripts', 'aba_payway_scripts_basic' );
function aba_payway_scripts_basic() {
    $pay_css = aba_PAYWAY_AIM::getCsssend();
    $pay_js = aba_PAYWAY_AIM::getJssend();
    $hide_close = aba_PAYWAY_AIM::getHideclose();
    wp_deregister_script('wc-checkout');
    wp_enqueue_script('wc-checkout', ABA_PAYMENT_PLUGIN_PATH . 'js/checkout.js', array('jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n'), time(), true);
    wp_enqueue_script( 'aba-payway-custom-script', ABA_PAYMENT_PLUGIN_PATH. 'js/custom.js', array( 'jquery' ), time(), true );
    wp_enqueue_script( 'checkout-popup', $pay_js.$hide_close, false );
    
    wp_localize_script( 'aba-payway-custom-script', 'frontend_ajax_object',
        array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        )
    );
    wp_enqueue_style( 'aba-payway-checkout-style' , $pay_css);
 
}
add_action('init', 'aba_payway_start_session', 1);
function aba_payway_start_session() {
    if( !session_id() ) {
        session_start();
    }
}
add_action("wp_ajax_aba_check_payment_cancel_order", "aba_check_payment_cancel_order");
add_action("wp_ajax_nopriv_aba_check_payment_cancel_order", "aba_check_payment_cancel_order");
function aba_check_payment_cancel_order(){
    if( !empty($_SESSION['aba_order_id']) ) {
        $order = new WC_Order( $_SESSION['aba_order_id'] );
        if ( $order->status == 'pending') {
          $order->update_status('cancelled');
        }
    }
    exit;
}

add_action('wp_head','hook_header');

function hook_header()
{
?>
<style>
#payment .payment_methods li img.img-1x {
    max-height: 20px;
}
#payment .payment_methods li img.img-2x {
    max-height: 25px;
}

#payment .payment_methods li img.img-3x {
    max-height: 30px;
}
</style>
<?php 
}