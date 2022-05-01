<?php 

/**
 * Plugin Name: WooCommerce Abandoned Cart Recovery
 * Plugin URI: 
 * Description: Track cart/checkout, remainder and recover abandoned cart.
 * Version: 1.1.1
 * Author: 
 * Author URI: 
 * Text Domain: xit_wacr
 * Domain Path: /i18n/languages/
 */

defined( 'ABSPATH' ) || exit;

// Defines XIT_WOO_PLUGIN_FILE
if ( ! defined( 'XIT_WACR_PLUGIN_FILE' ) ) {
	define( 'XIT_WACR_PLUGIN_FILE', __FILE__ );
}

// Includes the main Woocommerce_Abandoned_Cart_Recovery class
if ( ! class_exists( 'Woocommerce_Abandoned_Cart_Recovery' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-woocommerce-abandoned-cart-recovery.php';
}

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */ 
function xit_woo_plugin_init() {
	return Woocommerce_Abandoned_Cart_Recovery::instance();
}

xit_woo_plugin_init();