<?php
/**
 * Plugin Name: УНИ Кредит
 * Plugin URI: 
 * Description: Кредитен калкулатор 
 * Version: 1.4.2
 * Author: Ilko Ivanov
 * Author URI: http://avalonbg.com
 * Text Domain: unipayment
 * Domain Path: /languages
 * Network: 
 * License: 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 **/
// Makes sure the plugin is defined before trying to use it
if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

if ( (is_plugin_active_for_network( 'woocommerce/woocommerce.php' )) ||
	in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) 
) {
	/** definitions */
	define('UNI_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));
	define('UNI_INCLUDES_DIR', UNI_PLUGIN_DIR . '/includes');
	define('UNI_CSS_URI', WP_CONTENT_URL . '/plugins/unipayment/css');
	define('UNI_LIVEURL', 'https://unicreditconsumerfinancing.info');
	define('UNI_FINANCIAL_PRECISION', 1.0e-08);
	define('UNI_FINANCIAL_MAX_ITERATIONS', 128);
	define('UNI_MOD_VERSION', '1.4.2');

	/** includes */
	require_once UNI_INCLUDES_DIR . '/functions.php';
	require_once UNI_INCLUDES_DIR . '/admin.php';
	require_once UNI_INCLUDES_DIR . '/unipaymentredirect.php';

	/** add admin menu options page ###includes/admin.php### */
	add_action('admin_menu', 'uni_admin_actions');
	/** output buffer ###includes/functions.php### */
	add_action('init', 'uni_do_output_buffer');

	/** vizualize credit button ###includes/functions.php### */
	add_action('woocommerce_after_add_to_cart_button','unipayment_button');

	/** reklama ###includes/functions.php### */
	add_action('wp_enqueue_scripts', 'uni_add_meta');
	add_action('admin_enqueue_scripts', 'uni_add_meta_admin');
	add_action('loop_start', 'uni_reklama');

	/** load plugin class */
	add_action('plugins_loaded', 'uni_load_class_plugin', 0);

	/** WC blocks payment method */
	/** add payment gateway */
	add_filter('woocommerce_payment_gateways', 'uni_add_gateway');
	/** declare compatibility with cart_checkout_blocks feature */
	function uni_declare_cart_checkout_blocks_compatibility() {
		if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
		}
	}
	add_action('before_woocommerce_init', 'uni_declare_cart_checkout_blocks_compatibility');

	// hook the function to the 'woocommerce_blocks_loaded' action
	add_action( 'woocommerce_blocks_loaded', 'uni_register_order_approval_payment_method_type' );
	function uni_register_order_approval_payment_method_type() {
		if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			return;
		}
		require_once plugin_dir_path(__FILE__) . 'class-block.php';
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new Uni_Payment_Gateway_Blocks );
			}
		);
	}
	/** WC blocks payment method */

	add_action('wp_loaded', 'uni_load_classes');
	function uni_load_classes() {
		global $wp;
		if (is_null($wp)) {
			error_log('Global $wp is null');
		} else {
			UnipaymentredirectPage::getInstance();
		}
	}
}
