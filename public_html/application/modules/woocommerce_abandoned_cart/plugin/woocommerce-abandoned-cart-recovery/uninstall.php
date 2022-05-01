<?php 

/**
  * WooCommerce Abandoned Cart Recovery Setup
  *
  * @since 1.0.0
  */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( ! class_exists( 'Xit_Wacr_Uninstall' ) ) {
    require_once dirname( __FILE__ ) . '/includes/class-xit-wacr-uninstall.php';
}

// Uninstalls the plugin
Xit_Wacr_Uninstall::uninstall();
