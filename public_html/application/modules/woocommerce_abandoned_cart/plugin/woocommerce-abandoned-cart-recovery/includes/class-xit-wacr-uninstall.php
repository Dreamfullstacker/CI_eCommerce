<?php 

/**
  * WooCommerce Abandoned Cart Recovery Setup
  *
  * @since 1.0.0
  */

defined( 'ABSPATH' ) || exit;

class Xit_Wacr_Uninstall {
    
    /**
     * Accomplishes activities for uninstalling plugin
     * 
     * @return void
     * @since 1.0.0
     */
    public static function uninstall() {
        global $wpdb;
        
        // Removes options
        delete_option( "xit_wacr_abandoned_cart_options" );        
        
        // Removes tables
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}xit_wacr_log_failed_data" );
        
    	// Clear any cached data that has been removed.
    	wp_cache_flush();        
    }
    
}