<?php 

/**
  * WooCommerce Abandoned Cart Recovery Setup
  *
  * @since 1.0.0
  */

defined( 'ABSPATH' ) || exit;

class Xit_Wacr_Install {

	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}
		
		// Returns if the process is already running.
		if ( 'yes' === get_transient( 'xit_wacr_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'xit_wacr_installing', 'yes', MINUTE_IN_SECONDS * 2 );
		
        // Creates necessary tables
		$result = self::create_tables();

		delete_transient( 'xit_wacr_installing' );
	}
	
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	    dbDelta( self::get_schema(), true );
	}
	
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = "
CREATE TABLE {$wpdb->prefix}xit_wacr_log_failed_data (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  status VARCHAR(50) NOT NULL,
  data LONGTEXT NOT NULL,
  created_at DATETIME NULL DEFAULT NULL,
  updated_at DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id)
) $collate;
		";

		return $tables;
	}
}