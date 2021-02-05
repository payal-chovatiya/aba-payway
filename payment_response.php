<?php
if (isset($_REQUEST['response'])) 
{    
include_once '../../../wp-config.php';
include_once '../../../wp-load.php';
include_once '../../../wp-includes/wp-db.php';
include_once '../../../wp-includes/pluggable.php';
global $wpdb,$woocommerce;
$response = json_decode(stripslashes($_REQUEST['response']), TRUE);
if ($response['status'] == 0 || $response['status'] == "0") {
$query = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'aba_transaction_id' and meta_value = %s";
$order_id = $wpdb->get_var($wpdb->prepare($query, $response['tran_id']));
$order = new WC_Order($order_id);
$order->update_status('processing');
$woocommerce->cart->empty_cart();
}}die;