<?php
/**
 * Public class.
 *
 * @package V7_Cache_Cleaner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_Cache_Cleaner_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function add_browser_cache_headers() {
		if ( is_admin() || is_user_logged_in() ) {
			return;
		}

		$lifetime = (int) get_option( 'v7_cache_lifetime', 24 );
		$seconds  = $lifetime * 3600;

		header( 'Cache-Control: public, max-age=' . $seconds );
		header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $seconds ) . ' GMT' );
		header( 'Vary: Accept-Encoding' );
	}
}
