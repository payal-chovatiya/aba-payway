<?php
class aba_PAYWAY_AIM extends WC_Payment_Gateway {
        function __construct() {
		
                // global ID
		$this->id = "aba_payway_aim";

		// Show Title
		$this->method_title = __( "ABA Payway", 'aba-payway-aim' );

		// Show Description
		$this->method_description = __( "ABA Payway Payment Gateway Plug-in for WooCommerce", 'aba-payway-aim' );

		// vertical tab title
		$this->title = __( "ABA Payway", 'aba-payway-aim' );


		$this->icon = null;

		$this->has_fields = false;

		// support default form with credit card
		//$this->supports = array( 'default_credit_card_form' );

		// setting defines
		$this->init_form_fields();
                $this->test_pi_url = $this->get_option('test_api_url');
		// load time variable setting
		$this->init_settings();
		
		// Turn these settings into variables we can use
		foreach ( $this->settings as $setting_key => $value ) {
			$this->$setting_key = $value;
		}
		
		// further check of SSL if you want
		//add_action( 'admin_notices', array( $this,	'do_ssl_check' ) );
		
		$mode_api = $this->get_option('environment');
		
		if($mode_api == 'yes')
		{
                    if ( !defined('ABA_PAYWAY_API_URL') )	
                    define('ABA_PAYWAY_API_URL',$this->get_option('api_url'));
		
		}else{
                    if ( !defined('ABA_PAYWAY_API_URL') )
                    define('ABA_PAYWAY_API_URL',$this->get_option('test_pi_url'));	
		
		}
                
                
		/*
		|--------------------------------------------------------------------------
		| ABA PayWay API KEY
		|--------------------------------------------------------------------------
		| API KEY that is generated and provided by PayWay must be required in your post form
		|
		*/
                if ( !defined('ABA_PAYWAY_API_KEY') )
                    define('ABA_PAYWAY_API_KEY', $this->get_option('api_url'));

		/*
		|--------------------------------------------------------------------------
		| ABA PayWay Merchant ID
		|--------------------------------------------------------------------------
		| Merchant ID that is generated and provided by PayWay must be required in your post form
		|
		*/
                if ( !defined('ABA_PAYWAY_MERCHANT_ID') )
		define('ABA_PAYWAY_MERCHANT_ID', $this->get_option('maerchant_id'));
		
		// Save settings
		if ( is_admin() ) {
                    
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
	}
        
	public function get_icon() {
		$get_size = $this->get_option('image_size');
		$payment_option = $this->get_option('payment_options');
		$aba_payway = $payment_option[0];
                $master_card = $payment_option[1];
                $visa_card =  $payment_option[2];

		
		if( $get_size == 'small' ){
                    $icon = "";
                    if($aba_payway == 'aba_apy')
                    {
                    $icon  .= sprintf(
                        '<img src="%s" alt="Visa" class="img-1x"  style="margin-left: 3px;"/>', 
                        WC_HTTPS::force_https_url( plugins_url( '/images/1x/aba-apy-logo.png', __FILE__ ) ) 
                    );
                    }
                    if($master_card == 'master_card'  || $payment_option[0] == 'master_card' || $payment_option[1] == 'master_card')
                    {
                    $icon  .= sprintf(
                        '<img src="%s" alt="MasterCard" class="img-1x"  style="margin-left: 3px;"/>', 
                        WC_HTTPS::force_https_url( plugins_url('/images/1x/master-card.png', __FILE__ ) ) 
                    );
                    }
                      if($visa_card == 'visa_card'  || $payment_option[0] == 'visa_card' || $payment_option[1] == 'visa_card')
                    {
                    $icon  .= sprintf(
                        '<img src="%s" alt="MasterCard" class="img-1x"  />', 
                        WC_HTTPS::force_https_url( plugins_url('/images/1x/visa-card.png', __FILE__ ) ) 
                    );
                    }
		}elseif($get_size == 'medium'){
                        $icon = "";
                        if($aba_payway == 'aba_apy')
                        {
                        $icon  .= sprintf(
                            '<img src="%s" alt="Visa" class="img-2x" style="margin-left: 3px;"/>', 
                            WC_HTTPS::force_https_url( plugins_url( '/images/2x/aba-apy-logo.png', __FILE__ ) ) 
                        ); 
                        }
                        if($master_card == 'master_card'  || $payment_option[0] == 'master_card' || $payment_option[1] == 'master_card')
                        {
                        $icon  .= sprintf(
                            '<img src="%s" alt="MasterCard" class="img-2x"  style="margin-left: 3px;"/>', 
                            WC_HTTPS::force_https_url( plugins_url('/images/2x/master-card.png', __FILE__ ) ) 
                        );
                         }
                          if($visa_card == 'visa_card'  || $payment_option[0] == 'visa_card' || $payment_option[1] == 'visa_card')
                        {
                        $icon  .= sprintf(
                        '<img src="%s" alt="MasterCard" class="img-2x" />', 
                        WC_HTTPS::force_https_url( plugins_url('/images/2x/visa-card.png', __FILE__ ) ) 
                    );
                        }
		}elseif($get_size == 'large'){
                        $icon = "";
                        
                        if($aba_payway == 'aba_apy')
                         {
                        $icon  .= sprintf(
                            '<img src="%s" alt="Visa" class="img-3x" style="margin-left: 3px;" />', 
                            WC_HTTPS::force_https_url( plugins_url( '/images/3x/aba-apy-logo.png', __FILE__ ) ) 
                        ); 
                         }
                        if($master_card == 'master_card'  || $payment_option[0] == 'master_card' || $payment_option[1] == 'master_card')
                        {
                        $icon  .= sprintf(
                            '<img src="%s" alt="MasterCard" class="img-3x" style="margin-left: 3px;"/>', 
                            WC_HTTPS::force_https_url( plugins_url('/images/3x/master-card.png', __FILE__ ) ) 
                        );
                         }
                         if($visa_card == 'visa_card'  || $payment_option[0] == 'visa_card' || $payment_option[1] == 'visa_card')
                        {
                        $icon  .= sprintf(
                            '<img src="%s" alt="MasterCard"  class="img-3x"/>', 
                            WC_HTTPS::force_https_url( plugins_url('/images/3x/visa-card.png', __FILE__ ) ) 
                        );
                        }
		
		}
		return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
        } 
	
	// administration fields for specific Gateway
	public function init_form_fields() {
		
		$this->form_fields = array(
			'enabled' => array(
				'title'		=> __( 'Enable / Disable', 'aba-payway-aim' ),
				'label'		=> __( 'Enable this payment gateway', 'aba-payway-aim' ),
				'type'		=> 'checkbox',
				'default'	=> 'no',
			),
			
			'title' => array(
				'title'		=> __( 'Title', 'aba-payway-aim' ),
				'type'		=> 'text',
				'desc_tip'	=> __( 'Payment title of checkout process.', 'aba-payway-aim' ),
				'default'	=> __( 'Pay with', 'aba-payway-aim' ),
			),
			
			
			'payment_options' => array(
					 'title' => 'Payment Options',
					 'description' => false,
					 'type' => 'multiselect',
					 'default' => 'Default value for the option',
					 'class' => 'Class for the input',
					 'css' => 'width:120px;height:90px;',
					 'label' => 'Label', // checkbox only
					 'options' => array(
						  'aba_apy' => 'ABA Pay',
						  'master_card' => 'Master Card',
						  'visa_card' => 'Visa Card',
					 ) // array of options for select/multiselects only
			),
			
			'image_size' => array(
					'title'    => __( 'Image Size', 'woocommerce' ),
					'description'    => __( 'This controls the position of the currency symbol.', 'woocommerce' ),
					'id'      => 'woocommerce_currency_pos',
					'css'     => 'min-width:150px;',
					'std'     => 'small', // WooCommerce < 2.0
					'default' => 'small', // WooCommerce >= 2.0
					'type'    => 'select',
					'options' => array(
					  'small'        => __( 'Small', 'woocommerce' ),
					  'medium'       => __( 'Medium', 'woocommerce' ),
					  'large'  => __( 'Large', 'woocommerce' )
					),
					'desc_tip' =>  true,
				),
				
			'description' => array(
				'title'		=> __( 'Description', 'aba-payway-aim' ),
				'type'		=> 'textarea',
				'desc_tip'	=> __( 'Payment title of checkout process.', 'aba-payway-aim' ),
				'default'	=> __( 'Successfully payment through credit card.', 'aba-payway-aim' ),
				'css'		=> 'max-width:450px;'
			),
			
			'maerchant_id' => array(
				'title'		=> __( 'Merchant Id', 'aba-payway-aim' ),
				'type'		=> 'text',
				'desc_tip'	=> __( 'Merchant id will be here.', 'aba-payway-aim' ),
				'default'	=> false,
			),
			
			'api_url' => array(
				'title'		=> __( 'API URL', 'aba-payway-aim' ),
				'type'		=> 'text',
				'desc_tip'	=> __( '', 'aba-payway-aim' ),
			),
			
			'test_api_url' => array(
				'title'		=> __( 'Test API URL', 'aba-payway-aim' ),
				'type'		=> 'text',
				'desc_tip'	=> __( '', 'aba-payway-aim' ),
			),
			'api_key' => array(
				'title'		=> __( 'API Key', 'aba-payway-aim' ),
				'type'		=> 'password',
				'desc_tip'	=> __( '' ),
			),
			'environment' => array(
				'title'		=> __( 'Sandbox Mode', 'aba-payway-aim' ),
				'label'		=> __( 'Enable Test Mode', 'aba-payway-aim' ),
				'type'		=> 'checkbox',
				'description' => __( 'This is the test mode of gateway.', 'aba-payway-aim' ),
				'default'	=> 'no',
			),
                        'production_css' => array(
				'title'		=> __( 'Production Css', 'aba-payway-aim' ),
				'type'		=> 'text',
                                'class'		=> 'production-css-js',
                                'default' => 'https://payway-staging.ababank.com/checkout-popup.html?file=css',
				'desc_tip'	=> __( '', 'aba-payway-aim' ),
			),
                        'production_js' => array(
				'title'		=> __( 'Production Js', 'aba-payway-aim' ),
				'type'		=> 'text',
                                'class'		=> 'production-css-js',
                                'default' => 'https://payway-staging.ababank.com/checkout-popup.html?file=js&custom-js=magento1',
				'desc_tip'	=> __( '', 'aba-payway-aim' ),
			),
                        'sandbox_css' => array(
				'title'		=> __( 'Sandbox Css', 'aba-payway-aim' ),
				'type'		=> 'text',
                                'class'		=> 'sandbox-css-js',
                                'default' => 'https://payway-staging.ababank.com/checkout-popup.html?file=css',
				'desc_tip'	=> __( '', 'aba-payway-aim' ),
			),
                        'sandbox_js' => array(
				'title'		=> __( 'Sandbox Js', 'aba-payway-aim' ),
				'type'		=> 'text',
                                'class'		=> 'sandbox-css-js',
                                'default' => 'https://payway-staging.ababank.com/checkout-popup.html?file=js&custom-js=magento1',
				'desc_tip'	=> __( '', 'aba-payway-aim' ),
			),
                        'hide_close' => array(
					'title'    => __( 'Hide Close', 'woocommerce' ),
					'description'    => __( 'This controls the position of the currency symbol.', 'woocommerce' ),
					'id'      => 'hide_close',
					'css'     => 'min-width:150px;',
					'std'     => 'small', // WooCommerce < 2.0
					'default' => 'small', // WooCommerce >= 2.0
					'type'    => 'select',
					'options' => array(
					  'hide_yes'        => __( 'Yes', 'woocommerce' ),
					  'hide_no'       => __( 'No', 'woocommerce' ),
					),
					'desc_tip' =>  true,
                        )
		);		
	}
	
	// Response handled for payment gateway
	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new WC_Order( $order_id );
                
                //Set transaction id in session 
                if( $_SESSION['aba_transaction_id'] ) {
                    update_post_meta( $order_id,'aba_transaction_id',$_SESSION['aba_transaction_id'] );
                }
                if( !empty($order_id) ) {
                    $_SESSION['aba_order_id'] = $order_id;
                }
		// Mark as Pending payment (we're awaiting the payment)
		$order->update_status('Pending payment', __( 'Awaiting payment', 'woocommerce-other-payment-gateway' ));
		
                // Reduce stock levels
		wc_reduce_stock_levels( $order_id );
		if(isset($_POST[ $this->id.'-admin-note']) && trim($_POST[ $this->id.'-admin-note'])!=''){
			$order->add_order_note(esc_html($_POST[ $this->id.'-admin-note']),1);
		}
		
                // Remove cart
		//$woocommerce->cart->empty_cart();
                
                // Return thankyou redirect
		return array(
			'result' => 'success',
			//'redirect' =>  $order->get_checkout_payment_url( true )
		);  

	}
        public static function getApiUrl() {
            $myPluginGateway = new aba_PAYWAY_AIM();
            $mode_api = $myPluginGateway->get_option('environment');
            if($mode_api == 'yes')
            {
                $api_url = $myPluginGateway->get_option('test_api_url');
            
            }else{

                $api_url = $myPluginGateway->get_option('api_url');	

            }
            return $api_url;
	}
        public static function getHash($transactionId, $amount, $items) {
                $PluginGateway = new aba_PAYWAY_AIM();
                $hash = base64_encode(hash_hmac('sha512', $PluginGateway->get_option('maerchant_id') . $transactionId . $amount . $items, $PluginGateway->get_option('api_key'), true));
		return $hash;
	}
        public static function getApiKey() {
                $PluginGateway = new aba_PAYWAY_AIM();
                $api_key = $PluginGateway->get_option('api_key');
                return $api_key;
	}
        
        public static function getCsssend() {
                $PluginGateway = new aba_PAYWAY_AIM();
                $mode_api = $PluginGateway->get_option('environment');
                if($mode_api == 'yes')
                {
                    $payway_css = $PluginGateway->get_option('sandbox_css');

                }else{

                    $payway_css = $PluginGateway->get_option('production_css');	

                }
                return $payway_css;
	}
        
        public static function getJssend() {
                $PluginGateway = new aba_PAYWAY_AIM();
                $mode_api = $PluginGateway->get_option('environment');
                if($mode_api == 'yes')
                {
                    $payway_js = $PluginGateway->get_option('production_js');

                }else{

                    $payway_js = $PluginGateway->get_option('sandbox_js');	

                }
                return $payway_js;
	}
        
        public static function getHideclose() {
                $PluginGateway = new aba_PAYWAY_AIM();
                $hide_close = $PluginGateway->get_option('hide_close');
    
                if($hide_close == 'hide_yes')
                {
                    $close_stat = '&hide-close=1';

                }else{

                    $close_stat = '&hide-close=0';	

                }
                return $close_stat;
	}
}