<?php
/*
	Plugin Name: GTPay CI Payment Gateway
	Plugin URI: https://github.com/billdoss/gtpay-ci-woocommerce 
	Description: GTPay Woocommerce Payment Gateway allows you to accept payment on your Woocommerce store via Visa Cards, Mastercards, Verve Cards and eTranzact.
	Version: 1.0.0
	Author: Abdoul-Rhamane Dosso
	Author email: abdoul.dosso@gtbank.com
	Author URI: http://umbron.com.ng/
	WC requires at least: 3.3
    WC tested up to: 3.4
	License:           GPL-2.0+
 	License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter( 'woocommerce_payment_gateways', 'gtpay_add_gateway_class' );

function gtpay_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_GTPay'; // your class name is here
	return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'gtpay_init_gateway_class' );

function gtpay_init_gateway_class() {
 
	class WC_GTPay extends WC_Payment_Gateway {
 		/**
		  * Class constructor, more about it in Step 3
		  */
		public function __construct() {
			$this->id = "gtpay_gateway"; // global ID
			$this->has_fields = false;
			$this->method_title = "GTPay Payment Gateway"; // Show Title
			$this->method_description = 'GTPay Payment Gateway allows you to receive Mastercard, Verve Card and Visa Card Payments via your Woocommerce Powered Site.';// Show Description
			$this->icon = apply_filters('woocommerce_gtpay_icon', plugins_url( 'assets/images/logo1.png' , __FILE__ ) );// Icon link
			$this->notify_url = WC()->api_request_url( 'WC_GTPay' );
			
			
			// Method with all the options fields
			$this->init_form_fields();

			// load variable setting
			$this->init_settings();
			$this->title = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->enabled = $this->get_option( 'enabled' );
			$this->gtpay_mert_id = $this->get_option('gtpay_mert_id');
			$this->gtpay_cust_id = $this->get_option('gtpay_cust_id');
			//$gtpay_hash = $hashed = hash('sha512', $gtpay_hash);
			$this->hashkey = $this->get_option('hashkey');
			$this->gtpay_tranx_curr = $this->get_option('gtpay_tranx_curr');
			if ($this->get_option('sandbox') == 'yes') {
				$this->posturl = $this->get_option('live_url');
				$this->geturl =  $this->get_option('live_tranx_resp_url');
				
				//$this->posturl = 'https://ebank.gtbankci.com/GTPay_demo/Tranx.aspx';
				//$this->geturl =  'https://ebank.gtbankci.com/GTPayService_demo/gettransactionstatus.json';
			} else {
				$this->posturl = $this->get_option('test_url');
				$this->geturl =  $this->get_option('test_tranx_resp_url');

				//$this->posturl = 'https://ibank.gtbank.com/GTPay/Tranx.aspx';
				//$this->geturl =  'https://ibank.gtbank.com/GTPayService/gettransactionstatus.json';
			}
			
			

			// This action hook saves the settings
			add_action('woocommerce_receipt_' . $this->id, array( $this, 'receipt_page'), 10, 1);
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			// You can also register a webhook here
			add_action( 'woocommerce_api_'.strtolower(get_class($this)) , array(&$this, 'get_gtpay_response' ));
 		 }
		 
		/**
	 	* Plugin options, we deal with it in Step 3 too
		*/
		public function init_form_fields(){

			$this->form_fields = array(
				'enabled' => array(
					'title'		=> 'Enable / Disable',
					'label'		=> 'Enable GTPay',
					'type'		=> 'checkbox',
					'default'	=> 'no',
				),
				'title' => array(
					'title'		=> 'Title',
					'type'		=> 'text',
					'description' => 'This controls the title which the user sees during checkout.',
					'default'	=> 'GTPay',
					'desc_tip'  => true,
				),
				'description' => array(
					'title'		=> 'Description',
					'type'		=> 'textarea',
					'description' => 'This controls the description which the user sees during checkout.',
					'default'	=> 'Payment Methods Accepted: MasterCard, VisaCard, Verve Card. GTbank Assured',
					'desc_tip'    => true,
					'css'		=> 'max-width:450px;',
				),
				'gtpay_mert_id' => array(
					'title' => 'GTPay Merchant Id',
					'type' => 'text',
					'description' => 'Enter Your GTPay Merchant ID, this can be gotten on your account page when you login on GTPay',
					'desc_tip'    => true,
				),
				'gtpay_cust_id' => array(
					'title' => 'GTPay Customer Id',
					'type' => 'text',
					'description' => 'Enter Your GTPay Merchant ID, this can be gotten on your account page when you login on GTPay',
					'desc_tip'    => true,
				),
				'live_url' => array(
						'title' => 'Live url', 
						'type' => 'text',
						'description' => 'Put here the live url',
						'desc_tip'    => true,
				),
				'live_tranx_resp_url' => array(
					'title' => 'Live transaction response url', 
					'type' => 'text',
					'description' => 'Put here the live url for response',
					'desc_tip'    => true,
				),
				'test_url' => array(
					'title' => 'Test url', 
					'type' => 'text',
					'description' => 'Put here the test url',
					'desc_tip'    => true,
				),
				'test_tranx_resp_url' => array(
					'title' => 'Test transaction response url', 
					'type' => 'text',
					'description' => 'Put here the test url for response',
					'desc_tip'    => true,
				),
				'hashkey' => array(
						'title' => 'Hash Key', 
						'type' => 'text',
						'description' => 'long string of Alphanumeric characters sent to you by your account officer',
						'desc_tip'    => true,
				),
				'gtpay_tranx_curr' => array(
						'title' => 'Store Currency',
						'type' => 'select',
						'description' => 'Currency you wish to accept payment in. Make sure this tallys with set currency of woocommerce and bank',
						'desc_tip'    => true,
						'options' => array(
								"566" => "XOF", 
								"826" => "USD"
							),
						),
				'sandbox' => array(
						'title' => 'Sandbox',
						'label'		=> 'Enable Live Mode',
						'type' => 'checkbox',
						'description' => 'Enable/Disable Live mode',
						'desc_tip'    => true,
						'default'	=> 'yes',
					)
			);

		}

		public function receipt_page($order_id) {
			echo '<p><span style="background-color: blue;color: #f4f4f4;padding: 5px;">Please review your order, then click on "Pay via GTPay" to be redirected to the Payment Gateway</span></p>';
			echo $this->generate_gtpay_button($order_id);
		}

		/**
		 * Generate the GTPay Payment button link
	    **/
		public function generate_gtpay_button($order_id) {
			global $woocommerce;
			// we need it to get any order details
			$order = wc_get_order( $order_id );
			$gtpay_mert_id = $this->gtpay_mert_id;
			$hashkey = $this->hashkey;
			$gtpay_tranx_curr = $this->gtpay_tranx_curr;
			$gtpay_tranx_amt = $order->get_total();
			$gtpay_tranx_amt *=100;
			$gtpay_tranx_id = $order->get_id() . '_' . date("ymds");
			$gtpay_tranx_noti_url = $this->notify_url;
			$gtpay_cust_id = $this->gtpay_cust_id;
			$billing_fname = $order->get_billing_first_name();
			$billing_lname = $order->get_billing_last_name();
			$gtpay_cust_name = trim($billing_lname . ' ' . $billing_fname);
			

			//Perform hash
			$my_hash = $gtpay_mert_id . $gtpay_tranx_id . $gtpay_tranx_amt . $gtpay_tranx_curr . $gtpay_cust_id . $gtpay_tranx_noti_url . $hashkey;
			$gtpay_hash = hash('sha512', $my_hash);

			/*
			* Array with parameters for API interaction
			*/
			$gtpay_args = array(
				'gtpay_mert_id' => $gtpay_mert_id,
                'gtpay_tranx_id' => $gtpay_tranx_id,
                'gtpay_tranx_amt' => $gtpay_tranx_amt,
                'gtpay_tranx_curr' => $gtpay_tranx_curr,
                'gtpay_cust_id' => $gtpay_cust_id,
                'gtpay_cust_name' => $gtpay_cust_name,
                'gtpay_hash' => $gtpay_hash,
                'gtpay_tranx_noti_url' => $gtpay_tranx_noti_url,
                'gtpay_echo_data' => $gtpay_tranx_id . ";" . $gtpay_hash
			);

			$gtpay_args_array = array();
            foreach ($gtpay_args as $key => $value) {
                $gtpay_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
            }
			return '<form action="' . $this->posturl . '" method="post" id="gtpay_payment_form">' . implode('', $gtpay_args_array) . '
			<input type="submit" class="button-alt" id="submit_gtpay_payment_form" value="' . __('Pay via GTPay', 'gtpay') . '" /> <a class="button cancel" href="' . $order->get_cancel_order_url() . '">' . __('Cancel order &amp; restore cart', 'gtpay') . '</a>
            <script type="text/javascript">
                function processGTPayJSPayment(){
                jQuery("body").block(
                        {
                            message: "<img src=\"' . plugins_url('assets/images/ajax-loader.gif', __FILE__) . '\" alt=\"redirecting...\" style=\"float:left; margin-right: 10px;\" />' . __('Thank you for your order. We are now redirecting you to Payment Gateway to make payment.', 'gtpay') . '",
                                overlayCSS:
                        {
                            background: "#fff",
                                opacity: 0.6
                    },
                    css: {
                        padding:        20,
                            textAlign:      "center",
                            color:          "#555",
                            border:         "3px solid #aaa",
                            backgroundColor:"#fff",
                            cursor:         "wait",
                            lineHeight:"32px"
                    }
                    });
                    jQuery("#gtpay_payment_form").submit();
                    }
                    jQuery("#submit_gtpay_payment_form").click(function (e) {
                        e.preventDefault();
                        processGTPayJSPayment();
                    });
            </script>
			</form>';
		}

		/*
		 * We're processing the payments here, everything about it is in Step 5
		 */
		public function process_payment( $order_id ) {
			global $woocommerce;

			$order = wc_get_order($order_id);
			return array(
				'result' => 'success',
				'redirect'	=> $order->get_checkout_payment_url( true )
			);

		}

		/*
		 * In case you need a webhook, like PayPal IPN etc
		 */
		public function get_gtpay_response() {
		    global $woocommerce;
			//Get reponse
			$filepamil = 'logPam.txt';
			//var_dump($_POST);
			if(! empty( $_POST )){
			    //echo '<pre>'; print_r($_POST); echo '</pre>';
			    $posted = wp_unslash($_POST);
			    $gtpay_mert_id = $this->gtpay_mert_id;
			    $hashkey = $this->hashkey;
    			
            	$tranxid = $posted['gtpay_tranx_id'];
    			$gtpay_echo_data = $posted['gtpay_echo_data'];
    			$data = explode(";", $gtpay_echo_data);
    			$wc_order_arr = explode("_", $data[0]);
    			$wc_order_id = trim($wc_order_arr[0]);
    			$resp_order = wc_get_order($wc_order_id);
    			$total_amount = $resp_order->get_total();
    			$total_amt =$total_amount * 100;
    			$reff = base64_encode($total_amount . "| GT_TranxId" . $tranxid);
    			$resp_order->add_order_note('Reff: ' . $reff);
				if ($posted['gtpay_tranx_status_code'] === 'G300') {
					#payment cancelled
					$respond_desc = $posted['gtpay_tranx_status_msg'];
					$message_resp = "Your transaction failed. <br><strong>Reason</strong>: " . $respond_desc . "<br><strong>Transaction Reference<strong>:" . $tranxid . '<br>You may restart your payment below.';
					$message_type = "error";
					$resp_order->add_order_note('GTPay payment failed:<br> ' . $respond_desc . '<br>Transaction Reference: ' . $tranxid, true);
					$resp_order->update_status('cancelled');
					wc_add_notice($message_resp, $message_type);
					wp_redirect($resp_order->get_cancel_order_url());
				}else{
					//Payment params successfully posted
					//confirm hash
				// 	if (strtolower($posted['gtpay_verification_hash']) == strtolower($gtpay_hash)){
					$hash_req = hash('sha512', $gtpay_mert_id.$tranxid.$hashkey);
					$get_url = $this->geturl;
					$params ='mertid='.$gtpay_mert_id.'&amount='.$total_amt.'&tranxid='.$tranxid.'&hash='.$hash_req;
					$my_url = $get_url. '?' .$params;
					$gt_json_response = wp_remote_get($my_url);
					    
					if( !is_wp_error( $gt_json_response ) ) {
						$data = json_decode( $gt_json_response['body'], true );
						if( $data['ResponseCode'] == '00'){
							#payment successful
							// we received the payment
							$respond_desc = $data['ResponseDescription'];
							$message_resp = "Thank You!. <br><strong>Reason</strong>: " . $respond_desc . "<br><strong>Transaction Reference</strong>:" . $tranxid;
							$message_type = "success";
							$resp_order->payment_complete();
							$resp_order->update_status('completed');
							wc_reduce_stock_levels( $resp_order->get_id() );
							$resp_order->add_order_note( 'Hey, your order is paid! Thank you! <br>GTPay Transaction Reference: ' . $tranxid, true );
							wc_add_notice( $message_resp, $message_type );
							// Empty cart
							$woocommerce->cart->empty_cart();
							// Redirect to the thank you page
							wp_redirect($this->get_return_url( $resp_order ));

						}else{
							// something went horribly wrong
							$respond_desc = $data['ResponseDescription'];
							$message_resp = "Your Transaction was unsuccessful.<br>Reason: ". $respond_desc . "<br><strong>Transaction Reference</strong>:" . $tranxid ."<br><strong>Please Try Again!</strong>";
							$message_type = "error";
							$resp_order->add_order_note('GTPay payment failed:<br> ' . $respond_desc . '<br>Transaction Reference: ' . $tranxid, true);
							$resp_order->update_status('cancelled');
							wc_add_notice( $message_resp, $message_type );
							wp_redirect($resp_order->get_cancel_order_url());
						}
					} else {
						wc_add_notice(  'Connection error.', 'error' );
					}
				}
			}else {
				exit();
			die();
			}
		}
	}
}
