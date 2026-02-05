<?php
/**
 * Activator class.
 *
 * @package V7_Cache_Cleaner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_Cache_Cleaner_Activator {

	public static function activate() {
		$defaults = array(
			'v7_cache_page_cache'      => '1',
			'v7_cache_browser_cache'   => '1',
			'v7_cache_auto_post'       => '1',
			'v7_cache_auto_plugin'     => '1',
			'v7_cache_woo_integration' => '1',
			'v7_cache_lifetime'        => '24',
		);

		foreach ( $defaults as $key => $value ) {
			if ( false === get_option( $key ) ) {
				add_option( $key, $value );
			}
		}

		$cache_dir = WP_CONTENT_DIR . '/cache/v7-cache/';
		if ( ! file_exists( $cache_dir ) ) {
			wp_mkdir_p( $cache_dir );
		}
	}

	public static function deactivate() {
		require_once V7_CACHE_DIR . 'includes/class-v7-cache-cleaner-cache.php';
		V7_Cache_Cleaner_Cache::clear_all_cache();
	}
}
