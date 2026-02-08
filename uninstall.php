<?php
/**
 * Uninstall script.
 *
 * @package V7_Cache_Cleaner
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
$cache_dir = WP_CONTENT_DIR . '/cache/v7-cache/';
if ( is_dir( $cache_dir ) ) {
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $cache_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::CHILD_FIRST
	);
	foreach ( $files as $fileinfo ) {
		$action = $fileinfo->isDir() ? 'rmdir' : 'unlink';
		$action( $fileinfo->getRealPath() );
	}
	rmdir( $cache_dir );
}
$options = array(
	'v7_cache_page_cache',
	'v7_cache_browser_cache',
	'v7_cache_auto_post',
	'v7_cache_auto_plugin',
	'v7_cache_woo_integration',
	'v7_cache_lifetime',
);
foreach ( $options as $option ) {
	delete_option( $option );
}
