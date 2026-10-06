<?php

/** WC blocks payment method */
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Automattic\WooCommerce\Blocks\Payments\PaymentContext;
use Automattic\WooCommerce\Blocks\Payments\PaymentResult;

final class Uni_Payment_Gateway_Blocks extends AbstractPaymentMethodType {

	private $gateway;
	protected $name = 'uni_payment_gateway';
	
	public function __construct( $payment_request_configuration = null ) {
		add_action( 'woocommerce_rest_checkout_process_payment_with_context', [ $this, 'add_payment_request_order_meta' ], 8, 2 );
	}

	public function initialize() {
		$this->settings = get_option( 'woocommerce_uni_payment_gateway_settings', [] );
		$this->gateway = new Uni_Payment_Gateway();
	}

	public function is_active() {
		return $this->gateway->is_available();
	}

	public function get_payment_method_script_handles() {
		$uni_blocks_script = plugin_dir_path(__FILE__) . 'build/index.js';
		$uni_blocks_version = file_exists($uni_blocks_script) ? filemtime($uni_blocks_script) : UNI_MOD_VERSION;

		wp_register_script(
			'uni_payment_gateway-blocks-integration',
			plugin_dir_url(__FILE__) . 'build/index.js',
			[
				'wc-blocks-registry',
				'wc-settings',
				'wp-element',
				'wp-html-entities',
				'wp-i18n',
			],
			$uni_blocks_version,
			true
		);
		if( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'uni_payment_gateway-blocks-integration');
			
		}
		return [ 'uni_payment_gateway-blocks-integration' ];
	}

	public function get_payment_method_data() {
		/** payment_fields parameters for a blocks */
		$uni_unicid = (string)get_option("unipayment_unicid");
		global $woocommerce;

		$uni_ch = curl_init();
		curl_setopt($uni_ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($uni_ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($uni_ch, CURLOPT_MAXREDIRS, 2);
		curl_setopt($uni_ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($uni_ch, CURLOPT_URL, UNI_LIVEURL . '/function/getparameters.php?cid='.$uni_unicid);
		$paramsuni = json_decode(curl_exec($uni_ch), true);
		curl_close($uni_ch);

		$uni_product_cat_id = 0;
		if ($woocommerce->cart) {
			$cart = $woocommerce->cart->get_cart();
			$uni_product = reset($cart);
			if ($uni_product) {
				$uni_product_id = $uni_product['product_id'];
				$terms = get_the_terms( $uni_product_id, 'product_cat' );
				foreach ($terms as $term) {
					$uni_product_cat_id = $term->term_id;
				}
			}
		}


		global $current_user;
		if( version_compare( $woocommerce->version, '4.5', ">=" ) ) {
			wp_get_current_user();
		}else{
			get_current_user();
		}
		if (get_user_meta( $current_user->ID )){
			$uni_all_meta_for_user = array_map( function( $a ){ return $a[0]; }, get_user_meta( $current_user->ID ) );
		}

		$uni_proces1 = intval($paramsuni['uni_proces1']);
		$uni_total = $woocommerce->cart->total;
		$uni_first_vnoska = $paramsuni['uni_first_vnoska'];
		$uni_proces2 = intval($paramsuni['uni_proces2']);
		$uni_firstname = isset($uni_all_meta_for_user['first_name']) ? $uni_all_meta_for_user['first_name'] : uni_wordpress_get_params( 'billing_first_name', '' );
		$uni_lastname = isset($uni_all_meta_for_user['last_name']) ? $uni_all_meta_for_user['last_name'] : uni_wordpress_get_params( 'billing_last_name', '' );
		$uni_phone = isset($uni_all_meta_for_user['billing_phone']) ? $uni_all_meta_for_user['billing_phone'] : uni_wordpress_get_params( 'billing_phone', '' );
		$uni_email = isset($uni_all_meta_for_user['billing_email']) ? $uni_all_meta_for_user['billing_email'] : uni_wordpress_get_params( 'billing_email', '' );
		$uni_uslovia = UNI_CSS_URI . "/uni_uslovia.pdf";
		$uni_mod_version = UNI_MOD_VERSION;
		$uni_promo = $paramsuni['uni_promo'];
		$uni_promo_data = $paramsuni['uni_promo_data'];
		$uni_promo_meseci_znak = $paramsuni['uni_promo_meseci_znak'];
		$uni_promo_meseci = $paramsuni['uni_promo_meseci'];
		$uni_promo_price = floatval($paramsuni['uni_promo_price']);
		$uni_shema_current = intval($paramsuni['uni_shema_current']);
		$uni_meseci_3 = intval($paramsuni['uni_meseci_3']);
		$uni_meseci_4 = intval($paramsuni['uni_meseci_4']);
		$uni_meseci_5 = intval($paramsuni['uni_meseci_5']);
		$uni_meseci_6 = intval($paramsuni['uni_meseci_6']);
		$uni_meseci_9 = intval($paramsuni['uni_meseci_9']);
		$uni_meseci_10 = intval($paramsuni['uni_meseci_10']);
		$uni_meseci_12 = intval($paramsuni['uni_meseci_12']);
		$uni_meseci_15 = intval($paramsuni['uni_meseci_15']);
		$uni_meseci_18 = intval($paramsuni['uni_meseci_18']);
		$uni_meseci_24 = intval($paramsuni['uni_meseci_24']);
		$uni_meseci_30 = intval($paramsuni['uni_meseci_30']);
		$uni_meseci_36 = intval($paramsuni['uni_meseci_36']);

		$uni_eur = (int)$paramsuni['uni_eur'];
		$uni_currency_code = get_woocommerce_currency();

		switch ($uni_eur) {
			case 0:
				break;
			case 1:
				if ($uni_currency_code == "EUR") {
					$uni_total = $uni_total * 1.95583;
				}
				break;
			case 2:
			case 3:
				if ($uni_currency_code == "BGN") {
					$uni_total = $uni_total / 1.95583;
				}
				break;
		}

		$uni_mesecna_second = 0;
		$uni_sign = 'лева';
		$uni_sign_second = 'евро';
		switch ($uni_eur) {
			case 0:
				$uni_price_second = 0;
				$uni_sign = 'лева';
				$uni_sign_second = 'евро';
				break;
			case 1:
				$uni_price_second = number_format($uni_total / 1.95583, 2, ".", "");
				$uni_sign = 'лева';
				$uni_sign_second = 'евро';
				break;
			case 2:
				$uni_price_second = number_format($uni_total * 1.95583, 2, ".", "");
				$uni_sign = 'евро';
				$uni_sign_second = 'лева';
				break;
			case 3:
				$uni_price_second = 0;
				$uni_sign = 'евро';
				$uni_sign_second = 'лева';
				break;
		}

		return [
			'title' => $this->gateway->title,
			'description' => $this->gateway->description,
			'uni_proces1' => $uni_proces1,
			'uni_promo' => $uni_promo,
			'uni_promo_data' => $uni_promo_data,
			'uni_promo_meseci_znak' => $uni_promo_meseci_znak,
			'uni_promo_meseci' => $uni_promo_meseci,
			'uni_promo_price' => $uni_promo_price,
			'uni_product_cat_id' => $uni_product_cat_id,
			'uni_shema_current' => $uni_shema_current,
			'uni_saglasie' => "No",
			'uni_proces2' => $uni_proces2,
			'uni_eur' => $uni_eur,
			'uni_sign' => $uni_sign,
			'uni_sign_second' => $uni_sign_second,
			'uni_total' => $uni_total,
			'uni_price_second' => $uni_price_second,
			'uni_meseci_3' => $uni_meseci_3,
			'uni_meseci_4' => $uni_meseci_4,
			'uni_meseci_5' => $uni_meseci_5,
			'uni_meseci_6' => $uni_meseci_6,
			'uni_meseci_9' => $uni_meseci_9,
			'uni_meseci_10' => $uni_meseci_10,
			'uni_meseci_12' => $uni_meseci_12,
			'uni_meseci_15' => $uni_meseci_15,
			'uni_meseci_18' => $uni_meseci_18,
			'uni_meseci_24' => $uni_meseci_24,
			'uni_meseci_30' => $uni_meseci_30,
			'uni_meseci_36' => $uni_meseci_36,
			'uni_first_vnoska' => $uni_first_vnoska,
			'uni_firstname' => $uni_firstname,
			'uni_lastname' => $uni_lastname,
			'uni_phone' => $uni_phone,
			'uni_email' => $uni_email,
			'uni_uslovia' => $uni_uslovia,
			'uni_mod_version' => $uni_mod_version
		];
	}

	public function add_payment_request_order_meta( PaymentContext $context, PaymentResult &$result ) {
		if ( 'uni_payment_gateway' === $context->payment_method ) {
			$uni_proces2 = WC()->session->get( 'uni_proces2' );
			$uni_uslovia_check = WC()->session->get( 'uni_uslovia_check' );
			$uni_egn = WC()->session->get( 'uni_egn' );
			if ($uni_proces2 == 1){
				if ($uni_uslovia_check == 0){
					wc_add_notice(  "Необходимо е да се съгласите с Общите условия на UniCredit!", 'error' );
					return false;
				}
				if ($uni_egn == ''){
					wc_add_notice(  "Необходимо е да попълните полето ЕГН!", 'error' );
					return false;
				}
			}
		}

		return $result;
	}

}
/** WC blocks payment method */
