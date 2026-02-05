<?php
/**
 * Cache management class.
 *
 * @package V7_Cache_Cleaner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class V7_Cache_Cleaner_Cache {

	public static function clear_all_cache() {
		self::clear_page_cache();
		self::clear_object_cache();
		self::clear_opcache();
		self::clear_third_party_caches();

		do_action( 'v7_cache_cleared' );

		return true;
	}

	public static function clear_page_cache() {
		$cache_dir = WP_CONTENT_DIR . '/cache/v7-cache/';

		if ( is_dir( $cache_dir ) ) {
			self::delete_directory_contents( $cache_dir );
		}
	}

	public static function clear_object_cache() {
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Required for transient cleanup.
		$wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%'"
		);
	}

	public static function clear_opcache() {
		if ( function_exists( 'opcache_reset' ) ) {
			opcache_reset();
		}
	}

	public static function clear_third_party_caches() {
		if ( function_exists( 'w3tc_flush_all' ) ) {
			w3tc_flush_all();
		}

		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
		}

		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}

		if ( class_exists( 'LiteSpeed_Cache_API' ) && method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) ) {
			LiteSpeed_Cache_API::purge_all();
		}

		if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
			autoptimizeCache::clearall();
		}
	}

	private static function delete_directory_contents( $dir ) {
		if ( ! is_dir( $dir ) ) {
			return;
		}

		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ( $files as $fileinfo ) {
			$action = $fileinfo->isDir() ? 'rmdir' : 'unlink';
			$action( $fileinfo->getRealPath() );
		}
	}

	public static function get_cache_size() {
		$cache_dir = WP_CONTENT_DIR . '/cache/v7-cache/';

		if ( ! is_dir( $cache_dir ) ) {
			return 0;
		}

		$size = 0;
		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $cache_dir ) ) as $file ) {
			if ( $file->isFile() ) {
				$size += $file->getSize();
			}
		}

		return $size;
	}

	public static function format_size( $bytes ) {
		$units = array( 'B', 'KB', 'MB', 'GB' );
		$i     = 0;
		while ( $bytes >= 1024 && $i < count( $units ) - 1 ) {
			$bytes /= 1024;
			$i++;
		}
		return round( $bytes, 2 ) . ' ' . $units[ $i ];
	}
}
